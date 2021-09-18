<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiRequest;
use App\Rules\Admin\StoreServiceSuggestRequestRule;

class StoreServiceSuggestRequest extends ApiRequest
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
            'service_id' => ['array', 'exists:services,id', new StoreServiceSuggestRequestRule()],
        ];
    }

    public function messages()
    {
        return [
            'service_id.exists' => __('api.validation.services_not_exists'),
            'service_id.array' => __('api.service.service_id_array'),
        ];
    }
}
