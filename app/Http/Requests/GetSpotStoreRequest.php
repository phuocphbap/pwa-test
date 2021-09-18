<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetSpotStoreRequest extends FormRequest
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'regionId' => 'nullable|exists:regions,id',
            'search' => 'nullable',
            'per_page' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'latitude.numeric' => __('api.user.latitude_number'),
            'longitude.numeric' => __('api.user.longitude_number'),
            'regionId.exists' => __('api.region.id_not_exists'),
            'per_page.numeric' => __('api.common.per_page_number'),
        ];
    }
}
