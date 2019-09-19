<?php

namespace BajakLautMalaka\PmiDonatur\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserDonatorRequest extends FormRequest
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
            'name'        => 'required|string',
            'email'       => 'required|string|email|unique:users',
            'password'    => 'sometimes|required|string|confirmed',
            'phone'       => 'required|string|unique:donators',
            'image_file'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dob'         => 'date_format:Y-m-d',
            'address'     => 'string',
            'province'    => 'string',
            'city'        => 'string',
            'subdistrict' => 'string',
            'area'        => 'string',
            'postal_code' => 'numeric',
            'gender'      => 'in:male,female'
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
