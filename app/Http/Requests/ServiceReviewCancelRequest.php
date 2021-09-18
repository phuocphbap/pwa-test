<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceReviewCancelRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'consulting_id.required' => __('api.request-consulting.consulting_id_required'),
            'consulting_id.exists' => __('api.request-consulting.consulting_not_exists'),
        ];
    }
}
