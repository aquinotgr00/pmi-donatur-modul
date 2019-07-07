<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use BajakLautMalaka\PmiDonatur\Campaign;
use BajakLautMalaka\PmiDonatur\Http\Requests\StoreCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateCampaignRequest;
use BajakLautMalaka\PmiDonatur\Http\Requests\UpdateFinishCampaignRequest;
use Validator;

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
    public function index()
    {
        $campaign = Campaign::where('publish', true)
            ->with('getType')
            ->with('getDonations')
            ->paginate($this->paginate);
        return response()->success($campaign);
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

        $campaign = Campaign::create($request->except('_token'));
        $campaign->getType;
        $campaign->getDonations;
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
        $campaign = Campaign::with('getType')
            ->with('getDonations')
            ->find($id);
        if (!is_null($campaign)) {
            return response()->success($campaign);
        }else{
            return response()->fail($campaign);
        }
    }
    /**
     * update campaign
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, UpdateCampaignRequest $request)
    {
        if (isset($request->user()->id)) {
            $request->request->add(['admin_id' => $request->user()->id]);
        }

        $campaign = Campaign::find($id);

        if (!is_null($campaign)) {

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

            $campaign->update($request->except('_token', '_method'));
        }

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
        $data = (object) [
            'finish_campaign' => $request->finish_campaign
        ];
        $campaign = Campaign::updateFinishCampaign($data, $id);
        if (
            isset($campaign->getType) &&
            isset($campaign->getDonations)
        ) {
            $campaign->getType;
            $campaign->getDonations;
            return response()->success($campaign);
        }else{
            return response()->fail($campaign);
        }
        
    }

    public function allFilter(Request $request)
    {
        $campaign  = Campaign::with('getType')
            ->with('getDonations')
            ->paginate($this->paginate);

        if ($request->has('publish')) {
            $campaign = Campaign::getByPublished($request->publish, intval($this->paginate));
        }

        if ($request->has('keyword')) {
            $campaign = Campaign::getByKeyword($request->keyword, intval($this->paginate));
        }

        if ($request->has('type_id')) {
            $campaign  = Campaign::where('type_id', $request->type_id)
                ->with('getType')
                ->with('getDonations')
                ->paginate($this->paginate);
        }
        return response()->success($campaign);
    }
}
