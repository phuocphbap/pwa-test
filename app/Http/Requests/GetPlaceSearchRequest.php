<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPlaceSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => __('api.search.text_required'),
        ];
    }
}
