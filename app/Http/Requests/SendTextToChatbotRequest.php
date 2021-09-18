<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class SendTextToChatbotRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $step = [
            '0', '1', '2', '3', '4'
        ];
        return [
            'room_key' => 'required|exists:chats,key_firebase',
            'step' => 'required|numeric|in:'.implode(',', $step),
            'price' => "required_if:step,==,0,1",
            'name_owner' => "required_if:step,0,3,4",   // user_name of services owner
            'name_customer' => "required_if:step,3,4",    // user_name of services customer
            'name_services' => "required",
            'service_id' => 'required|exists:services,id',
            'consulting_id' => 'required|exists:request_consultings,id',
            'text' => "required_if:step,0",
            'user_id' => 'required_if:step,1|exists:users,id',   // step 1: userId is user of services owner,
        ];
    }

    public function messages()
    {
        return [
            'room_key.required' => __('api.validation.room_key_required'),
            'room_key.exists' => __('api.validation.room_key_exists'),
            'step.required' => __('api.validation.step_required'),
            'step.numeric' => __('api.validation.step_numeric'),
            'step.in' => __('api.validation.step_in'),
            'price.required_if' => __('api.validation.price_required'),
            'name_owner.required_if' => __('api.validation.name_owner_required'),
            'name_customer.required_if' => __('api.validation.name_customer_required'),
            'name_services.required' => __('api.validation.name_services_required'),
            'service_id.required' => __('api.validation.services_id_required'),
            'service_id.exists' => __('api.validation.services_not_exists'),
            'consulting_id.required' => __('api.validation.consulting_id_required'),
            'consulting_id.exists' => __('api.validation.consulting_id_exists'),
            'text.required_if' => __('api.validation.text_required'),
            'user_id.required_if' => __('api.validation.user_id_required'),
            'user_id.exists' => __('api.validation.user_id_not_exits'),
        ];
    }
}
