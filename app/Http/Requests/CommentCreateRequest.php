<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class commentCreateRequest extends FormRequest
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
            'service_id' => 'required|exists:services,id',
            'message' => 'required|max:1001',
        ];
    }

    public function messages()
    {
        return [
            'service_id.required' => __('api.validation.services_id_required'),
            'message.required' => __('api.comment.message_required'),
            'message.max' => __('api.comment.message_max'),
        ];
    }
}
