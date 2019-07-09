<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use BajakLautMalaka\PmiDonatur\Donation;

class ReportDonationApiController extends Controller
{
    public function index(Request $request, Donation $donation)
    {

        $donation = $this->handleDateRanges($request, $donation);

        $donation = $this->handleStatus($request, $donation);

        $donation = $this->handleSearchName($request, $donation);

        $donation = $this->handleSearchCampaign($request, $donation);

        $donation = $this->handleSort($request, $donation);

        $donation->paginate();

        return response()->success($donation->with('campaign')->paginate());
    }

    private function handleDateRanges(Request $request, Donation $donation)
    {
        if (
            $request->has('from') &&
            $request->has('to')
        ) {
            $donation->whereBetween('created_at', [$request->from, $request->to]);
        }
        return $donation;
    }

    private function handleStatus(Request $request, $donation)
    {
        if ($request->has('st')) {
            $donation->where('status', $request->st);
        }
        return $donation;
    }

    private function handleSearchName(Request $request,$donation)
    {
        if ($request->has('n')) {
            $donation = $donation->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->n . '%');
            });
        }
        return $donation;
    }

    private function handleSearchCampaign(Request $request, $donation)
    {
        if ($request->has('c')) {
            $donation = $donation->whereHas('campaign', function($query) use ($request) {
                $query->where('campaigns.title', 'like', '%' . $request->c . '%');
            });
        }
        return $donation;
    }

    private function handleSort(Request $request,$donation)
    {
        if ($request->has('ob')) {
            // sort direction (default = asc)
            $sort_direction = 'asc';
            if ($request->has('od')) {
                if (in_array($request->od, ['asc', 'desc'])) {
                    $sort_direction = $request->od;
                }
            }
            $donation = $donation->orderBy($request->ob, $sort_direction);
        }
        return $donation;
    }

    public function show(int $id)
    {
        $donation = Donation::with('donation')
            ->with('donator')
            ->find($id);
        if (!is_null($donation)) {
            return response()->success($donation);
        } else {
            return response()->fail($donation);
        }
    }
}
