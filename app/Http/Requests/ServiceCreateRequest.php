<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ServiceCreateRequest extends FormRequest
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
            'category_id' => 'required',
            'region_id' => 'required',
            'service_image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'service_title' => 'required|max:32',
            'service_detail' => 'required|max:4000',
            'price' => ['required', ($request->price != 0) ? 'gte:50' : '', 'max:1000000000'],
            'time_required' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'price.gte' => __('api.service.price_gte'),
            'price.required' => __('api.service.price_required'),
            'price.max' => __('api.service.price_max'),
            'category_id.required' => __('api.service.category_id_required'),
            'region_id.required' => __('api.service.region_id_required'),
            'service_image.required' => __('api.service.image_required'),
            'service_image.mimes' => __('api.service.image_invalid'),
            'service_image.max' => __('api.service.image_max'),
            'service_title.required' => __('api.service.service_title_required'),
            'service_title.max' => __('api.service.service_title_max'),
            'service_detail.required' => __('api.service.service_detail_required'),
            'service_detail.max' => __('api.service.service_detail_max'),
            'time_required.required' => __('api.service.time_required'),
            'time_required.numeric' => __('api.service.time_required_numeric'),
        ];
    }
}
