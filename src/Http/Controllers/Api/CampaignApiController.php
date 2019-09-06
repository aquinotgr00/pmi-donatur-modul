<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use BajakLautMalaka\PmiDonatur\Campaign;

use BajakLautMalaka\PmiDonatur\Http\Requests\StoreCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateFinishCampaignRequest;
use BajakLautMalaka\PmiDonatur\Events\CampaignPublished;

class CampaignApiController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var object
     */
    protected $paginate;
    /**
     * function construction
     */
    public function __construct()
    {
        $this->paginate = 10;
    }
    /**
     * get published campaign
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Campaign $campaign)
    {
        // open or closed
        $campaign = $this->handleOpenOrClosed($request, $campaign);
        
        // visible or hidden
        $campaign = $this->handleVisibility($request, $campaign);
        
        // published or draft
        $campaign = $this->handlePublishedOrDraft($request, $campaign);

        // search keyword
        $campaign = $this->handleSearchKeyword($request, $campaign);

        // campaign type
        $campaign = $this->handleCampaignType($request, $campaign);

        // fundraising or in-kind (default = fundraising)
        $campaign = $this->handleDonationType($request, $campaign);

        // sort by 
        $campaign = $this->handleSort($request, $campaign);
        
        $getResult = $request->has('page')?'paginate':'get';
        return response()->success($campaign->with(['getType','getDonations'])->$getResult());
    }
    
    private function handleOpenOrClosed(Request $request, $campaign)
    {
        if ($request->has('a')) {
            $campaign = $campaign->where('closed', !$request->a);
        }
        return $campaign;
    }
    
    private function handleVisibility(Request $request, $campaign)
    {
        if ($request->has('v')) {
            $campaign = $campaign->where('hidden', !$request->v);
        }
        return $campaign;
    }

    private function handlePublishedOrDraft(Request $request, $campaign)
    {
        if (auth('admin')->user()) {
            // only admin can request publish or not
            if ($request->has('p')) {
                $campaign = $campaign->where('publish', $request->p);
            }
        }
        else {
            $campaign = $campaign->where('publish', 1);
        }
        
        return $campaign;
    }

    private function handleSearchKeyword(Request $request, $campaign)
    {
        if ($request->has('s')) {
            $campaign = $campaign->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->s . '%')
                ->orWhere('description', 'like', '%' . $request->s . '%')
                ->orWhereRaw("DATE_FORMAT(start_campaign,'%M') like CONCAT('%',?,'%')", $request->s)
                ->orWhereRaw("DATE_FORMAT(finish_campaign,'%M') like CONCAT('%',?,'%')", $request->s);
            });
        }
        return $campaign;
    }

    private function handleCampaignType(Request $request, $campaign)
    {
        if ($request->has('t')) {
            $campaign = $campaign->where('type_id', $request->t);
        } else {
            $campaign = $campaign->where('type_id', '<>', 3);
        }
        return $campaign;
    }
    
    private function handleDonationType(Request $request, $campaign)
    {
        if ($request->has('f')) {
            $campaign->where('fundraising', $request->input('f', 1));
        }
        return $campaign;
    }

    private function handleSort(Request $request, $campaign)
    {
        if ($request->has('ob')) {
            // sort direction (default = asc)
            $sort_direction = 'asc';
            if ($request->has('od')) {
                if (in_array($request->od, ['asc', 'desc'])) {
                    $sort_direction = $request->od;
                }
            }
            $campaign = $campaign->orderBy($request->ob, $sort_direction);
        }
        return $campaign;
    }

    /**
     * store campaign
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCampaignRequest $request)
    {
       if (
            $request->has('start_campaign') &&
            !is_null($request->start_campaign)
            ) {
            
            $start_campaign     = date('Y-m-d', strtotime($request->start_campaign));
            $finish_campaign    = date('Y-m-d', strtotime($request->finish_campaign));

            $request->merge([
                'start_campaign' => $start_campaign,
                'finish_campaign' => $finish_campaign,
            ]);
        }

        $image      = $request->image_file->store('campaigns','public');
        
        $campaign   = new Campaign;

        $campaign->fill($request->input());
        $campaign->image       = $image;
        $campaign->admin_id    = $request->user()->id;
        $campaign->save();

        if ($campaign->publish) {
            event(new CampaignPublished($campaign));
        }
        return response()->success($campaign);
    }

    /**
     * get details campaign
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $campaign = Campaign::with('getType')->find($id);
        
        if (!is_null($campaign)) {
            return response()->success($campaign);
        } else {
            return response()->fail($campaign);
        }
    }

    /**
     * update campaign
     *
     * @param \BajakLautMalaka\PmiDonatur\Campaign $campaign
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Campaign $campaign, UpdateCampaignRequest $request)
    {
        if (
            $request->has('start_campaign') &&
            !is_null($request->start_campaign)
            ) {
            
            $finish_campaign    = date('Y-m-d', strtotime($request->finish_campaign));
            $start_campaign     = date('Y-m-d', strtotime($request->start_campaign));
            
            $request->merge([
                'start_campaign' => $start_campaign,
                'finish_campaign' => $finish_campaign,
            ]);
        }

        $mustBroadcast = !$campaign->publish && $request->publish;

        if ($request->has('image_file')) {
            $campaign->image = $request->image_file->store('campaigns','public');
        }

        $campaign->admin_id = $request->user()->id;

        $campaign->update($request->input());

        if($mustBroadcast) {
            event(new CampaignPublished($campaign));
        }

        return response()->success($campaign);
    }

    /**
     * delete campaign
     *
     * @param \BajakLautMalaka\PmiDonatur\Campaign $campaign
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Campaign $campaign)
    {

        $campaign->delete();
        return response()->success($campaign);
    }

    public function updateFinishCampaign(Campaign $campaign, UpdateFinishCampaignRequest $request)
    {
        $finish_campaign = \Carbon\Carbon::parse($request->finish_campaign)->format('Y-m-d h:i:s');
        
        $campaign->update([
            'finish_campaign' => $finish_campaign
        ]);

        return response()->success($campaign);
    }

    public function toggle(Campaign $campaign,$toggleAttribute) {
        $togglables = [
            'visibility'=>'hidden',
            'open-close'=>'closed'
        ];
        $campaign->{$togglables[$toggleAttribute]} = !$campaign->{$togglables[$toggleAttribute]};
        $campaign->save();
        return response()->success($campaign->load('getType'));
    }
}
