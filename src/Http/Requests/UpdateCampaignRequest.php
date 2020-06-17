<?php

namespace BajakLautMalaka\PmiDonatur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateCampaignRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type_id' => 'exists:campaign_types,id',
            'title' => 'unique:campaigns,title,' .$this->campaign->id . ',id',
            'image_file' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'sometimes|required',
            'amount_goal' => 'numeric',
            'start_campaign' => 'nullable|date',
            'finish_campaign' => 'nullable|date',
            'fundraising' => 'boolean',
            'publish'=>'boolean'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
