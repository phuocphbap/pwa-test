<?php

namespace App\Http\Requests\Admin;

use App\Constant\StatusConstant;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Contracts\Service\Attribute\Required;

class HandleBlockServiceRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'serviceId' => $this->serviceId,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $type = [StatusConstant::SERVICE_NOT_IS_BLOCKED, StatusConstant::SERVICE_IS_BLOCKED];

        return [
            'serviceId' => 'required|exists:services,id',
            'type' => 'required|in:'.implode(',', $type),
            'reason' => 'required_if:type,'. StatusConstant::SERVICE_IS_BLOCKED,
        ];
    }

    public function messages()
    {
        return [
            'serviceId.required' => __('api.validation.services_id_required'),
            'serviceId.exists' => __('api.validation.services_not_exists'),
            'type.required' => __('api.validation.type_required'),
            'type.in' => __('api.validation.type_invalid'),
            'reason.required_if' => __('api.leave_group.reason_required'),
        ];
    }
}
