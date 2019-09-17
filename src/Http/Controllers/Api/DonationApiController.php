<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\DonationItem;
use BajakLautMalaka\PmiDonatur\Donator;
use BajakLautMalaka\PmiDonatur\Campaign;
use BajakLautMalaka\PmiDonatur\Http\Requests\StoreDonationRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateDonationRequest;
use BajakLautMalaka\PmiDonatur\Events\DonationSubmitted;
use BajakLautMalaka\PmiDonatur\Events\PaymentCompleted;

class DonationApiController extends Controller
{
    /**
     * To store the main table.
     *
     * @var mixed
     */
    protected $donations;

    /**
     * To store the related donation items table
     *
     * @var mixed
     */
    protected $donation_items;

    public function __construct(Donation $donations, DonationItem $donation_items)
    {
        $this->donations = $donations;
        $this->donation_items = $donation_items;
    }

    public function index($campaignId,Request $request, Donation $donations)
    {
        $donations = $this->handleDateRanges($request,$donations);
        $donations = $donations->with('campaign');
        $donations = $donations->where('campaign_id', $campaignId);
        $donations = $donations->where('status',3)->paginate(10);
        return response()->success($donations);
    }

    public function listByDonator(Request $request, $donatorId)
    {
        $isBetween = false;
        if ($request->startFrom && $request->finishTo) {
            $isBetween = true;
        }
        $donations = $this->donations
            ->where('donator_id', $donatorId)
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($isBetween, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->startFrom, $request->finishTo]);
            })
            ->get();

        foreach ($donations as $donation) {
            $donation->campaign;
        }

        return response()->success($donations);
    }

    public function listByRangeDate(Request $request, $donatorId)
    {
        $donations = $this->donations
            ->where('donator_id', $donatorId)
            ->whereBetween('created_at', [$request->start, $request->end])
            ->get();
            
        foreach ($donations as $donation) {
            $donation->campaign;
        }
        return response()->success($donations);
    }

    /**
     * Store donation data.
     *
     * @param StoreDonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDonationRequest $request)
    {
        if ($request->has('amount')) {
            // Make a unique code.
            $this->makeUniqueTransactionCode($request);
        }

        if ($request->hasFile('image_file')) {
            $request->merge([
                'image' => $request->image_file->store('donations','public')
            ]);
        }
        
        if (auth('admin')->user()) {
            //only admin make manual transaction
            $request->merge([
                'manual_payment' => 1,
                'status' => 3
            ]);
        }

        $this->makeInvoiceID($request);
            
        $donation = $this->donations->create($request->all());
        
        if ($request->has('donation_items')) {
            $donation->donationItems()->createMany($request->donation_items);
        }
        
        if (auth('admin')->user()) {
            event(new PaymentCompleted($donation));
        }else{
            event(new DonationSubmitted($donation));
        }

        $response = [
            'message' => 'Donations has been made.',
            'donation' => $donation
        ];

        return response()->success($response);
    }

    private function makeInvoiceID(Request $request){
        $next_id        = Donation::getNextID();
        $invoice_id     = str_pad($next_id, 5, "0", STR_PAD_LEFT);
        $invoice_parts  = array('INV', date('Y-m-d'), $invoice_id);
        $invoice        = implode('-', $invoice_parts);

        $request->merge([
            'invoice_id' => $invoice
        ]);
    }

    private function makeUniqueTransactionCode(StoreDonationRequest $request)
    {
        $request->merge([
            'amount' => $request->amount + rand(1, 99)
        ]);
    }

    public function proofUpload(Request $request)
    {
        $request->validate([
            'id'    => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $donation = $this->donations->find($request->id);

        if (!$donation)
            return response()->fail(['message' => 'Donation not found.']);
        
        $image = $request->image->store('donations','public');
        
        $donation->update([
            'image'  => $image,
            'status' => 2
        ]);

        return response()->success($data);
    }

    private function handleDateRanges(Request $request, $donations)
    {
        if (
            $request->has('from') &&
            $request->has('to')
        ) {
            $from   = trim($request->from);
            $to     = trim($request->to);
            if (!empty($from) && !empty($to)) {
                $donations = $donations->whereBetween('created_at', [$request->from, $request->to]);
            }
        }
        return $donations;
    }


    public function update(UpdateDonationRequest $request, Donation $donation)
    {
        
        $except = [];

        if ($request->has('status')) {

            $old_status = $donation->status;
            
            $donation->updateStatus($donation->id, $request->status);
        
            if (intval($old_status) !== intval($request->status)) {
                
                if ($donation->status === 3 ){
                
                    $amount_real = 0;

                    foreach ($donation->campaign->list_donators as $key => $value) {
                        $amount_real += intval($value->amount);
                    }

                    $campaign = Campaign::find($donation->campaign_id);
                    $campaign->amount_real = $amount_real;
                    $campaign->save();
                }
            }

            array_push($except, 'status');
        }

        if ($request->has('phone')) {
            
            $donator = Donator::firstOrCreate(
                [
                    'phone'=> $request->phone 
                ],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone
                ]
            );

            if ($request->has('address')) {
                
                if ($request->address != $donator->address ) {
                    $donator->update($request->only('address'));
                }

            }

            $request->merge([
                'donator_id' => $donator->id
            ]);

            array_push($except, 'address');

        }
        
        $data = ($except)? $request->except($except) : $request->all();

        $donation->update($data);
        
        if (isset($donation->donator)) {
            $donation->donator;
        }

        if (isset($donation->donationItems)) {
            $donation->donationItems;
        }
        
        if (isset($donation->campaign)) {
            if (isset($donation->campaign->getType)) {
                $donation->campaign->getType;
            }
        }

        return response()->success($donation);
    }
}