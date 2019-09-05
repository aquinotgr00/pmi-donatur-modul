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
        // Make a unique code.
        $this->makeUniqueTransactionCode($request);

        if ($request->has('image_file')) {
            $request->merge([
                'image' => $request->image_file->store('donations','public')
            ]);
        }
        
        $donation = $this->donations->create($request->all());

        $this->handleDonationItems($request->donation_items, $donation->id);

        $data = [
            'status'  => 'Pending',
            'message' => 'Terima kasih sudah berbuat baik silahkan transfer dan upload disini.'
        ];

        $this->donations->sendEmailStatus($donation->email, $donation);

        $response = [
            'message' => 'Donations has been made.',
            'donation' => $donation
        ];
        return response()->success($response);
    }

    private function makeUniqueTransactionCode(StoreDonationRequest $request)
    {
        $request->merge([
            'amount' => $request->amount + rand(1, 99)
        ]);
    }

    private function handleDonationItems($items, $id)
    {
        if($items) {
           foreach ($items as $item) {
                $itemArr = (array) json_decode($item);
                $itemArr['donation_id'] = $id;
                $this->donation_items->create($itemArr);
            }
        }
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

        $image = $this->donations->handleDonationImage($request->file('image'));
        
        $data = [
            'status'  => 'Waiting',
            'message' => 'Terima kasih sudah meng-upload bukti transfer, silahkan tunggu.'
        ];

        $this->donations->sendEmailStatus($donation->email, $donation);

        $donation->update([
                    'image'  => $image,
                    'status' => 2
                ]);

        return response()->success(['message' => 'success upload file']);
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

                $donation->sendEmailStatus($donation->email, $donation);
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
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]
            );

            if ($request->has('address') && !empty($request->address)) {
                
                if ($request->address != $donator->address ) {
                    $donator->update($request->only('address'));
                }

                array_push($except, 'address');
            }

            $request->request->add(['donator_id' => $donator->id]);
        }
        
        $data = ($except)? $request->except($except) : $request->all();

        $donation->update($data);

        return response()->success($donation);
    }
}