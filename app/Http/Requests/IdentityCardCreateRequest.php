<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class IdentityCardCreateRequest extends FormRequest
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
            'images' => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'images.required' => __('api.identity_card.image_required'),
            'images.*.max' => __('api.identity_card.image_max'),
            'images.*.image' => __('api.identity_card.image_invalid'),
            'images.*.mimes' => __('api.identity_card.image_invalid'),
        ];
    }
}
