<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use BajakLautMalaka\PmiDonatur\Donation;
use BajakLautMalaka\PmiDonatur\Exports\DonationExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportDonationApiController extends Controller
{
    public function index(Request $request, Donation $donation)
    {

        $donation = $this->handleDateRanges($request, $donation);

        $donation = $this->handleStatus($request, $donation);

        $donation = $this->handleSearchName($request, $donation);

        $donation = $this->handleSearchCampaign($request, $donation);

        $donation = $this->handleSort($request, $donation);
				
        $donation = $donation->whereHas('campaign', function ($query) use ($request) {
            
            if ($request->has('t')) {
                $query->where('type_id', $request->t);
            } else {
                $query->where('type_id', '<>', 3);
            }
            
            $query->where('fundraising', $request->input('f', 1));
        });
        $donation = $donation->orderBy('created_at','DESC');
        return response()->success($donation->with('campaign.getType')->with('donator')->paginate());
    }

    private function handleDateRanges(Request $request, $donation)
    {
        if (
            $request->has('from') &&
            $request->has('to')
        ) {
            $donation = $donation->whereBetween('created_at', [$request->from, $request->to]);
        }
        return $donation;
    }

    private function handleStatus(Request $request, $donation)
    {
        if ($request->has('st') && $request->st != 0) {
            $donation = $donation->where('status', $request->st);
        }
        return $donation;
    }

    private function handleSearchName(Request $request, $donation)
    {
        if ($request->has('n')) {
            $donation = $donation->where(function ($query) use ($request) {
                $query->where('invoice_id', 'like', '%' . $request->n . '%')
                ->orWhere('name', 'like', '%' . $request->n . '%');
            });
        }
        return $donation;
    }

    private function handleSearchCampaign(Request $request, $donation)
    {
        if ($request->has('c')) {
            $donation = $donation->whereHas('campaign', function ($query) use ($request) {
                $query->where('campaigns.title', 'like', '%' . $request->c . '%');
            });
        }
        return $donation;
    }

    private function handleSort(Request $request, $donation)
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
        $donation = Donation::with('campaign.getType')
            ->with('donator')
            ->with('donationItems')
            ->find($id);
        if (!is_null($donation)) {
            return response()->success($donation);
        } else {
            return response()->fail($donation);
        }
    }

    public function exportToPrint(Request $request,Donation $donations)
    {
        $donations = $this->handleDateRanges($request, $donations);

        $donations = $this->handleStatus($request, $donations);

        $donations = $this->handleSearchName($request, $donations);

        $donations = $this->handleSearchCampaign($request, $donations);

        $donations = $this->handleSort($request, $donations);

        $donations = $this->handleMultipleId($request,$donations);
                
        $donations = $donations->whereHas('campaign', function ($query) use ($request) {
            if ($request->has('t')) {
                $query->where('type_id', $request->t);
            } else {
                $query->where('type_id', '<>', 3);
            }
            $query->where('fundraising', $request->input('f', 1));
        });
        $donations = $donations->get();

        $html = view('donator::table-donations', compact('donations'))->render();

        return response()->success(compact('html'));
    }

    public function exportToExcel(Request $request)
    {
        if (class_exists('Excel')) {
            $file_name = 'donasi_'.date('Y-m-d-h-i-s',time());
            if ($request->has('id')) {
                $multi_id = json_decode($request->id);
                Excel::store(new DonationExport($multi_id), "public/$file_name.xlsx");
            }else{
                Excel::store(new DonationExport([]), "public/$file_name.xlsx");
            }
            return response()->success(['url' => url("storage/$file_name.xlsx")]);
        }
    }

    public function exportToPdf(Request $request,Donation $donations)
    {
        $pdf_title = 'Donasi ';
        $donations = $this->handleDateRanges($request, $donations);

        $donations = $this->handleStatus($request, $donations);

        $donations = $this->handleSearchName($request, $donations);

        $donations = $this->handleSearchCampaign($request, $donations);

        $donations = $this->handleSort($request, $donations);

        $donations = $this->handleMultipleId($request,$donations);
                
        $donations = $donations->whereHas('campaign', function ($query) use ($request) {
            
            if ($request->has('t')) {
                $query->where('type_id', $request->t);
            } else {
                $query->where('type_id', '<>', 3);
            }

            $query->where('fundraising', $request->input('f', 1));
        });

        if ($request->has('t') && $request->t == 3) {
            $pdf_title .='Bulan Dana';
        }
        if($request->has('f') && $request->f == 1){
            $pdf_title .=' Dana';
        }else{
            $pdf_title .=' Barang';
        }
        
        $donations = $donations->get();        

        $html = view('donator::table-donations',[
            'donations' => $donations
            ])->render();
        $file_name = $pdf_title.'_'.date('Y-m-d-h-i-s',time());

        PDF::SetTitle($pdf_title);
        PDF::AddPage();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output(public_path("$file_name.pdf"),'f');
        return response()->success(['url' => url("$file_name.pdf") ]);
    }

    public function handleMultipleId(Request $request, $donations)
    {
        if ($request->has('id') && !empty($request->id)) {
            $multi_id   = json_decode($request->id);
            $donations  = $donations->whereIn('id',$multi_id);
        }
        return $donations;
    }
}
