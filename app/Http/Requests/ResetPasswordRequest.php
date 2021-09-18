<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'password' => 'required|min:8',
            'password_confirm' => 'required_with:password|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => __('api.user.password_required'),
            'password.min' => __('api.user.password_min'),
            'password_confirm.required_with' => __('api.user.password_confirmation_required'),
            'password_confirm.same' => __('api.user.password_confirmation_same'),
        ];
    }
}
