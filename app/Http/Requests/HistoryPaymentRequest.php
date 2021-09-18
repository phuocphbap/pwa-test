<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HistoryPaymentRequest extends FormRequest
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'per_page' => 'nullable|numeric',
            'type' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'start_date.date' => __('api.common.start_date_format'),
            'end_date.date' => __('api.common.end_date_format'),
            'per_page.numeric' => __('api.common.per_page_number'),
        ];
    }
}
