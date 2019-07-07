<?php

namespace BajakLautMalaka\PmiDonatur\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use BajakLautMalaka\PmiDonatur\Campaign;
use BajakLautMalaka\PmiDonatur\Requests\StoreCampaignRequest;
use BajakLautMalaka\PmiDonatur\Requests\UpdateCampaignRequest;
use BajakLautMalaka\PmiDonatur\Requests\UpdateFinishCampaignRequest;
use BajakLautMalaka\PmiDonatur\Requests\StoreGoodCampaignRequest;
use BajakLautMalaka\PmiDonatur\Requests\StoreMonthCampaignRequest;
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
        if (isset($campaign->getType)) {
            $campaign->getType;
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
        $campaign = Campaign::with('getType')
            ->with('getDonations')
            ->find($id);
        if (!is_null($campaign)) {
            
            if (isset($campaign->getDonations)) {
                foreach ($campaign->getDonations->where('status', 2) as $key => $value) {
                    $value->donator;
                }
            }

            return response()->success($campaign);
        } else {
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
        } else {
            return response()->fail($campaign);
        }
    }

    public function allFilter(Request $request, Campaign $campaign)
    {
        $campaign  = $campaign->newQuery();
        if ($request->has('publish')) {
            $campaign->where('publish', $request->input('publish'));
        }

        if ($request->has('keyword')) {
            $campaign->where('title', 'like', '%' . $request->input('keyword') . '%')
                ->orWhere('description', 'like', '%' . $request->input('keyword') . '%')
                ->orWhere('start_campaign', 'like', '%' . $request->input('keyword') . '%')
                ->orWhere('finish_campaign', 'like', '%' . $request->input('keyword') . '%');
        }

        if ($request->has('type_id')) {
            $campaign->where('type_id', $request->input('type_id'));
        }

        if ($request->has('order') && $request->has('column')) {
            if (in_array($request->input('order'), ['asc', 'desc'])) {
                $campaign->orderBy($request->input('column'), $request->input('order'));
            }
        }

        $campaign->with('getType')->with('getDonations');

        return response()->success($campaign->get());
    }

    public function storeMonthCampaign(StoreMonthCampaignRequest $request)
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
        if (isset($campaign->getType)) {
            $campaign->getType;
        }
        return response()->success($campaign);
    }

    public function storeGoodCampaign(StoreGoodCampaignRequest $request)
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
        if (isset($campaign->getType)) {
            $campaign->getType;
        }
        return response()->success($campaign);
    }
}
