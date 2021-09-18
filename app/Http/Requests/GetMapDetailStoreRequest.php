<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMapDetailStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'store_id' => 'required|exists:stores,id',
        ];
    }

    public function messages()
    {
        return [
            'store_id.required' => __('api.store.not_found'),
            'store_id.exists' => __('api.store.not_found'),
        ];
    }
}
