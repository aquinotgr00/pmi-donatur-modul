<?php

namespace BajakLautMalaka\PmiDonatur\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use BajakLautMalaka\PmiDonatur\Campaign;
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
     * get list campaign
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
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
        return response()->json($campaign);
    }
    /**
     * store campaign
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_id' => 'required|exists:campaign_types,id',
            'title' => 'required|unique:campaigns',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'amount_goal' => 'required|numeric',
            'start_campaign' => 'date',
            'finish_campaign' => 'date',
            'fundraising' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

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
        return response()->json(['data' => $campaign]);
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
        return response()->json(['data' => $campaign]);
    }
    /**
     * update campaign
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'type_id' => 'exists:campaign_types,id',
            'title' => 'unique:campaigns,title,'.$id.',id',
            'image_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'amount_goal' => 'required|numeric',
            'start_campaign' => 'date',
            'finish_campaign' => 'date',
            'fundraising' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

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

                if (file_exists(storage_path('app/public/'.$campaign->image_file_name))) {
                    unlink(storage_path('app/public/'.$campaign->image_file_name));
                }

                $image_url = url('storage/' . $file_name);
                $request->request->add(['image' => $image_url]);
                $request->request->add(['image_file_name' => $file_name]);
            }

            $campaign->update($request->except('_token', '_method'));
        }

        return response()->json(['data' => $campaign]);
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
        return response()->json(['data' => $data]);
    }

    public function updateFinishCampaign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'finish_campaign' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }
        $data = (object) [
            'finish_campaign' => $request->finish_campaign
        ];
        $campaign = Campaign::updateFinishCampaign($data, $id);
        if (isset($campaign->getType)) {
            $campaign->getType;
        }
        if (isset($campaign->getDonations)) {
            $campaign->getDonations;
        }
        return response()->json(['data' => $campaign]);
    }
}
