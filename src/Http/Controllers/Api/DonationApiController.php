<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use BajakLautMalaka\PmiDonatur\Requests\StoreDonationRequest;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\DonationItem;

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
        $donation = $this->donations->create($request->all());
        $this->handleDonationItems($request->donation_items, $donation->id);
        $response = [
            'message' => 'Donations has been made.'
        ];
        return response()->success($response);
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
}