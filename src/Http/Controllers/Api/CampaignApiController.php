<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Builder;
use BajakLautMalaka\PmiDonatur\Campaign;

use BajakLautMalaka\PmiDonatur\Http\Requests\StoreCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateFinishCampaignRequest;
use Berkayk\OneSignal\OneSignalClient;

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

        return response()->success($campaign->with('getType')->with('getDonations')->paginate());
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
        if (isset($request->user()->id)) {
            $request->request->add(['admin_id' => $request->user()->id]);
        }

        $image      = $request->file('image_file');
        $extension  = $image->getClientOriginalExtension();
        $file_name  = $image->getFilename() . '.' . $extension;

        Storage::disk('public')->put($file_name,  File::get($image));
        
        $image_url = url('storage/' . $file_name);

        $request->request->add(['image' => $image_url]);
        $request->request->add(['image_file_name' => $file_name]);

        $finish_campaign    = date('Y-m-d', strtotime("+1 days",strtotime($request->finish_campaign)));
        $start_campaign     = date('Y-m-d', strtotime("+1 days",strtotime($request->start_campaign)));
        
        $request->merge([
            'start_campaign' => $start_campaign,
            'finish_campaign' => $finish_campaign,
        ]);

        $campaign = Campaign::create($request->except('_token'));
        if (isset($campaign->getType)) {
            $campaign->getType;
        }
        
        $pushNotificationAppId = config('donation.push_notification.app_id',env('ONESIGNAL_APP_ID'));
        $pushNotificationRestApiKey = config('donation.push_notification.rest_api_key',env('ONESIGNAL_REST_API_KEY'));
        $pushNotificationClient = new OneSignalClient($pushNotificationAppId, $pushNotificationRestApiKey, $pushNotificationRestApiKey);
        $pushNotificationClient->sendNotificationToAll($campaign->title, null, null, null, null);
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
     * @param integer $campaign
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Campaign $campaign, UpdateCampaignRequest $request)
    {
        
        $campaign->admin_id = $request->user()->id;
        
        if ($request->has('image_file')) {
            $image      = $request->file('image_file');
            $extension  = $image->getClientOriginalExtension();
            $file_name  = $image->getFilename() . '.' . $extension;

            Storage::disk('public')->put($file_name,  File::get($image));

            if (file_exists(storage_path('app/public/' . $campaign->image_file_name))) {
                unlink(storage_path('app/public/' . $campaign->image_file_name));
            }

            $image_url = url('storage/' . $file_name);
            $request->request->add(['image' => $image_url]);
            $request->request->add(['image_file_name' => $file_name]);
        }

        $finish_campaign    = date('Y-m-d', strtotime("+1 days",strtotime($request->finish_campaign)));
        $start_campaign     = date('Y-m-d', strtotime("+1 days",strtotime($request->start_campaign)));
        
        $request->merge([
            'start_campaign' => $start_campaign,
            'finish_campaign' => $finish_campaign,
        ]);

        $campaign->update($request->except('_token', '_method'));

        return response()->success($campaign);
    }
    /**
     * delete campaign
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $id)
    {
        $campaign = Campaign::find($id);
        if (!is_null($campaign)) {
            $campaign->delete();
            $data = [
                'status' => 200,
                'message' => 'Success! to delete campaign'
            ];
        } else {
            $data = [
                'status' => 404,
                'message' => 'Error! data not found'
            ];
        }
        return response()->success(['data' => $data]);
    }

    public function updateFinishCampaign(int $id, UpdateFinishCampaignRequest $request)
    {
        $finish_campaign    = date('Y-m-d', strtotime("+1 days",strtotime($request->finish_campaign)));

        $data = (object) [
            'finish_campaign' => $finish_campaign
        ];
        $campaign = Campaign::updateFinishCampaign($data, $id);
        if (
            isset($campaign->getType) &&
            isset($campaign->getDonations)
        ) {
            $campaign->getType;
            $campaign->getDonations;
            return response()->success($campaign);
        } else {
            return response()->fail($campaign);
        }
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
