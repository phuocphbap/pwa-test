<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ServiceUpdateRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'category_id' => 'nullable',
            'region_id' => 'nullable',
            'service_image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'service_title' => 'nullable|max:32',
            'service_detail' => 'nullable|max:4000',
            'price' => ['nullable', ($request->price != 0) ? 'gte:50' : '', 'max:1000000000'],
            'time_required' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'price.gte' => __('api.service.price_gte'),
            'price.max' => __('api.service.price_max'),
            'service_image.mimes' => __('api.service.image_invalid'),
            'service_image.max' => __('api.service.image_max'),
            'service_title.max' => __('api.service.service_title_max'),
            'service_detail.max' => __('api.service.service_detail_max'),
            'time_required.numeric' => __('api.service.time_required_numeric'),
        ];
    }
}
