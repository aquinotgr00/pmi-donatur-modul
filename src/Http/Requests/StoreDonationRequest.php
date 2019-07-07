<?php

namespace BajakLautMalaka\PmiDonatur\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'campaign_id'    => 'required',
            'category'       => 'required',
            'name'           => 'required|string',
            'email'          => 'required|string|email',
            'phone'          => 'required|string',
            'amount'         => 'required',
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