<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email' => 'required|email|unique:users',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            'password' => 'required|min:8',
            'password_confirm' => 'required_with:password|same:password',
            'user_name' => 'required',
            'phone' => 'nullable|min:8|max:15',
            'gender' => 'required',
            'birth_date' => 'required|date_format:Y-m-d|before:-18 years',
            'first_name' => 'required',
            'last_name' => 'required',
            'input_refferal_code' => 'nullable|exists:users,referral_code',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('api.user.email_required'),
            'email.email' => __('api.user.email_email'),
            'email.unique' => __('api.user.email_unique'),
            'avatar.mimes' => __('api.user.avatar_mimes'),
            'avatar.max' => __('api.user.avatar_max'),
            'password.required' => __('api.user.password_required'),
            'password.min' => __('api.user.password_min'),
            'password_confirm.required_with' => __('api.user.password_confirmation_required'),
            'password_confirm.same' => __('api.user.password_confirmation_same'),
            'user_name.required' => __('api.user.user_name_required'),
            'phone.min' => __('api.user.phone_min'),
            'phone.max' => __('api.user.phone_max'),
            'phone.regex' => __('api.user.phone_regex'),
            'gender.required' => __('api.user.gender_required'),
            'address_id.required' => __('api.user.address_id_required'),
            'birth_date.required' => __('api.user.birth_date_required'),
            'birth_date.date_format' => __('api.user.birth_date_invalid'),
            'birth_date.before' => __('api.user.age_invalid'),
            'first_name.required' => __('api.user.first_name_required'),
            'last_name.required' => __('api.user.last_name_required'),
            'input_refferal_code.exists' => __('api.user.input_refferal_code_exists'),
        ];
    }
}
