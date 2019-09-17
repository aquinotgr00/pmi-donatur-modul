<?php

namespace BajakLautMalaka\PmiDonatur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
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
            'campaign_id'=>[
                'required',
                Rule::exists('campaigns','id')->where(function ($query) {
                    $query
                    ->where('hidden', 0)
                    ->where('closed', 0)
                    ->where('publish', 1);
                })
            ],
            'name'=>'required|string',
            'email'=>'required|string|email',
            'phone'=>'required|string',
            'amount'=>[
                'numeric',
                'min:10000',
                Rule::requiredIf($this->input('fundraising') == 1)
            ],
            'payment_method' => 'string',
            'pick_method'    => 'string',
            'anonym'         => 'boolean'
        ];
    }

   /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->donator_id) {
            $this->merge([
                'guest' => true
            ]);
        }
    }
 
}