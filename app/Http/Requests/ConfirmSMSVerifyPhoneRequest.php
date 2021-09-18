<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ConfirmSMSVerifyPhoneRequest extends FormRequest
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
            'code' => 'required',
            'phone' => 'required|min:8|max:15',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => __('api.verify_phone.code_required'),
            'phone.min' => __('api.user.phone_min'),
            'phone.max' => __('api.user.phone_max'),
        ];
    }
}
