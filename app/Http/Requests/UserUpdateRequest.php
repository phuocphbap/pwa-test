<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserUpdateRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'email' => 'nullable|email|unique:users',
            'user_name' => 'nullable',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            'phone' => 'nullable|min:8|max:15',
            'user_phone' => 'nullable|min:8|max:15',
            'gender' => 'nullable',
            'birth_date' => 'nullable|date_format:Y-m-d|before:-18 years',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'address' => 'nullable',
            'user_address' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => __('api.user.email_email'),
            'email.unique' => __('api.user.email_unique'),
            'user_name.required' => __('api.user.user_name_required'),
            'avatar.mimes' => __('api.user.avatar_mimes'),
            'avatar.max' => __('api.user.avatar_max'),
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
        ];
    }
}
