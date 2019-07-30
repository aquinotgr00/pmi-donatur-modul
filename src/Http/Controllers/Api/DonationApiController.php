<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\DonationItem;
use BajakLautMalaka\PmiDonatur\Donator;
use BajakLautMalaka\PmiDonatur\Http\Requests\StoreDonationRequest;

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

    public function list($campaignId)
    {
        $donations = $this->donations->where('campaign_id', $campaignId)->paginate(10);

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

    public function updateStatus(Request $request, $donationId)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $message = 'Maaf donasi anda tidak diterima karena suatu alasan.';

        if ($request->status === 3)
            $message = 'Terima kasih atas donasi anda, status donasi anda telah kami terima.';

        $donation = $this->donations->find($donationId);

        $this->donations->updateStatus($donationId, $request->status);

        $data = [
            'status'  => config('donation.status.'.$request->status),
            'message' => $message
        ];

        $this->donations->sendEmailStatus($donation->email, $data);

        return response()->success(['message' => 'Donations status successfully updated.']);
    }

    /**
     * Store donation data.
     *
     * @param StoreDonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreDonationRequest $request)
    {
        // Make a unique code.
        $this->makeUniqueTransactionCode($request);

        $donation_next_id   = Donation::whereDate('created_at',\Carbon\Carbon::today())->count();
        $donation_next_id   +=1;
        $invoice_id         = str_pad($donation_next_id, 5, "0", STR_PAD_LEFT);
        $invoice_parts      = array('INV', date('Y-m-d'), $invoice_id);
        $invoice            = implode('-', $invoice_parts);

        $request->request->add(['invoice_id' => $invoice]);
        
        $request->request->add(['payment_method' => 1]);

        $image = $this->donations->handleDonationImage($request->file('image_file'));

        $request->merge([
            'image' => $image
        ]);

        $donation = $this->donations->create($request->all());

        $this->handleDonationItems($request->donation_items, $donation->id);

        $data = [
            'status'  => 'Pending',
            'message' => 'Terima kasih sudah berbuat baik silahkan transfer dan upload disini.'
        ];

        $this->donations->sendEmailStatus($donation->email, $data);

        $response = [
            'message' => 'Donations has been made.',
            'donation' => $donation
        ];
        return response()->success($response);
    }

    private function makeUniqueTransactionCode(StoreDonationRequest $request)
    {
        if ($request->amount !== 0)
            $request->merge([
                'amount' => $request->amount + rand(1, 99)
            ]);
    }

    private function handleDonationItems($items, $id)
    {
        if($items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    $item['donation_id'] = $id;
                    $this->donation_items->create($item);
                }
            }
            else {
                foreach ($items as $item) {
                    $itemArr = (array) json_decode($item);
                    $itemArr['donation_id'] = $id;
                    $this->donation_items->create($itemArr);
                }
            }
        }
    }

    public function proofUpload(Request $request)
    {
        $request->validate([
            'id'    => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $donation = $this->donations->find($request->id);

        if (!$donation)
            return response()->fail(['message' => 'Donation not found.']);

        $image = $this->donations->handleDonationImage($request->file('image'));

        $data = [
            'status'  => 'Waiting',
            'message' => 'Terima kasih sudah meng-upload bukti transfer, silahkan tunggu.'
        ];

        $this->donations->sendEmailStatus($donation->email, $data);

        $donation->update([
                    'image'  => $image,
                    'status' => 2
                ]);

        return response()->success(['message' => 'success upload file']);
    }

    public function updateDetails(Request $request, $donationId)
    {
        $request->validate([
            'status' => 'required',
            'payment_method' => 'required'
        ]);

        $message = 'Maaf donasi anda tidak diterima karena suatu alasan.';

        if ($request->status === 3)
            $message = 'Terima kasih atas donasi anda, status donasi anda telah kami terima.';

        $donation = $this->donations->find($donationId);
        
        if (!is_null($donation)) {
            
            $donation->update($request->all());
            
            $data = [
                'status'  => config('donation.status.'.$request->status),
                'message' => $message
            ];

            $this->donations->sendEmailStatus($donation->email, $data);
            $donation->donator;
            $donation->campaign;
            $donation->campaign->getType;            
            $donation->donationItems;

            return response()->success($donation);
        }else{
            return response()->fail(['message' => 'Error! failed to update donations']);
        }
    }

    public function updateInfo(Request $request, $donationId)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email',
            'phone' => 'required'
        ]);


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
        }

        $request->request->add(['donator_id' => $donator->id]);

        $donation = $this->donations->find($donationId);
        
        if (!is_null($donation)) {
            
            $donation->update($request->except('address'));

            $donation->donator;
            $donation->campaign;
            $donation->campaign->getType;
            $donation->donationItems;

            return response()->success($donation);
        }else{
            return response()->fail(['message' => 'Error! failed to update donations']);
        }
    }


}