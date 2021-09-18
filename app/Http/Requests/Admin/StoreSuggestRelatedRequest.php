<?php

namespace App\Http\Requests\Admin;

use App\Constant\StatusConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestRelatedRequest extends FormRequest
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
        $type = [StatusConstant::SERVICE_SORT_TRUE, StatusConstant::SERVICE_SORT_FALSE];

        return [
            'service_id' => 'required|array|exists:services,id',
            'type' => 'required|in:'.implode(',', $type),
        ];
    }

    public function messages()
    {
        return [
            'service_id.required' => __('api.validation.services_id_required'),
            'service_id.exists' => __('api.validation.services_not_exists'),
            'service_id.array' => __('api.service.service_id_array'),
            'type.required' => __('api.validation.type_required'),
            'type.in' => __('api.validation.type_invalid'),
        ];
    }
}
