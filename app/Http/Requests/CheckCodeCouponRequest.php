<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckCodeCouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_code' => 'required',
            'service_id' => 'required|exists:services,id',
        ];
    }

    public function messages()
    {
        return [
            'coupon_code.required' => __('api.coupon.coupon_required'),
            'servicesId.required' => __('api.validation.services_id_required'),
            'service_id.exists' => __('api.validation.services_not_exists'),
        ];
    }
}
