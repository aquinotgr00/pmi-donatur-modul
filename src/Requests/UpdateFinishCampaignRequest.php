<?php

namespace BajakLautMalaka\PmiDonatur\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinishCampaignRequest extends FormRequest
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
            'finish_campaign' => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
