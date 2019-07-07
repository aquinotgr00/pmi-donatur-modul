<?php

namespace BajakLautMalaka\PmiDonatur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'title' => 'unique:campaigns,title,' .$this->get('id') . ',id',
            'image_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'amount_goal' => 'numeric',
            'start_campaign' => 'date',
            'finish_campaign' => 'date',
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
