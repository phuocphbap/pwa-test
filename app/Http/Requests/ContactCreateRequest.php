<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactCreateRequest extends FormRequest
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
            'email' => 'required|email',
            'name' => 'required',
            'contents' => 'required',
            'phone' => 'nullable|min:8|max:15',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('api.user.email_required'),
            'email.email' => __('api.user.email_email'),
            'contents.required' => __('api.contact.contents_required'),
            'phone.min' => __('api.user.phone_min'),
            'phone.max' => __('api.user.phone_max'),
        ];
    }
}
