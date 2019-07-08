<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\DonationItem;
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

        $donation = $this->donations->create($request->all());

        $this->handleDonationItems($request->donation_items, $donation->id);

        $data = [
            'status'  => 'Pending',
            'message' => 'Terima kasih sudah berbuat baik silahkan transfer dan upload disini.'
        ];

        $this->donations->sendEmailStatus($donation->email, $data);

        $response = [
            'message' => 'Donations has been made.'
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
        if ($items) {
            foreach ($items as $item) {
                $item['donation_id'] = $id;
                $this->donation_items->create($item);
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

        if (!donation)
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
}