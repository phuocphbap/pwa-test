<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceReviewCreateRequest extends FormRequest
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
            'consulting_id' => 'required|exists:request_consultings,id',
            'value' => 'required|numeric',
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'consulting_id.required' => __('api.request-consulting.consulting_id_required'),
            'consulting_id.exists' => __('api.request-consulting.consulting_not_exists'),
            'value.required' => __('api.service_review.value_required'),
            'value.numeric' => __('api.service_review.value_numeric'),
            'message.required' => __('api.service_review.message_required'),
        ];
    }
}
