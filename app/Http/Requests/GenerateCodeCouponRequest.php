<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateCodeCouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'coupon_discount' => 'required|numeric|between:0,1',
            'start_date' => 'required|date',
            'expire_date' => 'required|date|after:startDate',
        ];
    }

    public function messages()
    {
        return [
            'service_id.required' => __('api.validation.services_id_required'),
            'service_id.exists' => __('api.validation.services_not_exists'),
            'coupon_discount.required' => __('api.coupon.coupon_discount_required'),
            'coupon_discount.numeric' => __('api.coupon.coupon_discount_numeric'),
            'coupon_discount.between' => __('api.coupon.coupon_discount_numeric'),
            'start_date.required' => __('api.coupon.start_date_required'),
            'start_date.date' => __('api.coupon.start_date_date'),
            'expire_date.required' => __('api.coupon.expire_date_required'),
            'expire_date.date' => __('api.coupon.expire_date_date'),
            'expire_date.after' => __('api.coupon.expire_date_after'),
        ];
    }
}
