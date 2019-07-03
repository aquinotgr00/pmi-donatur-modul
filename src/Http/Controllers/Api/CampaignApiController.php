<?php

namespace BajakLautMalaka\PmiDonatur\Http\Controllers\Api;

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
            'user_id' => 'required|numeric',
            'type_id' => 'required|exists:campaign_types,id',
            'title' => 'required|unique:campaigns',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'amount_goal' => 'required|numeric',
            'start_campaign' => 'date',
            'finish_campaign' => 'date',
            'fundraising' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

        $image = $request->file('image_file');
        $extension = $image->getClientOriginalExtension();
        Storage::disk('public')->put($image->getFilename() . '.' . $extension,  File::get($image));

        $image_url = url('images/campaign/' . $image->getFilename() . '.' . $extension);
        
        $request->request->add(['image' => $image_url]);

        $campaign = Campaign::create($request->except('_token'));

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
            'user_id' => 'numeric',
            'type_id' => 'exists:campaign_types,id',
            'title' => 'required',
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

        $campaign = Campaign::find($id);

        if (!is_null($campaign)) {

            if ($request->has('image_file')) {
                $image = $request->file('image_file');
                $extension = $image->getClientOriginalExtension();
                Storage::disk('public')->put($image->getFilename() . '.' . $extension,  File::get($image));

                if (file_exists(public_path($campaign->image))) {
                    Storage::delete(public_path($campaign->image));
                }

                $image_url = url('images/campaign/' . $image->getFilename() . '.' . $extension);
                $request->request->add(['image' => $image_url]);
            }

            $campaign->update($request->except('_token','_method'));
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
        }else{
            $data = [
                'status' => 404,
                'message' => 'Error! data not found'
            ];
        }
        return response()->json(['data' => $data]);
    }
}
