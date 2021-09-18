<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestConsultingCreateRequest extends FormRequest
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
            'service_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'service_id.required' => __('api.validation.services_id_required'),
        ];
    }
}
