<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'owner_id' => 'required|exists:users,id',
            'consulting_id' => 'required_with:service_id|nullable|exists:request_consultings,id',
            'service_id' => 'required_with:consulting_id|nullable|exists:services,id',
            'room_name' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'owner_id.required' => __('api.validation.user_owner_required'),
            'owner_id.exists' => __('api.validation.user_owner_invalid'),
            'consulting_id.required_with' => __('api.validation.consulting_id_required'),
            'consulting_id.exists' => __('api.validation.consulting_id_exists'),
            'service_id.required' => __('api.validation.services_id_required'),
            'service_id.exists' => __('api.validation.services_not_exists'),
        ];
    }
}
