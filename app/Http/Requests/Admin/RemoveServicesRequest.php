<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RemoveServicesRequest extends FormRequest
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
        return [
            'serviceId' => 'required|exists:services,id',
            'reason' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'serviceId.required' => __('api.validation.services_id_required'),
            'serviceId.exists' => __('api.validation.services_not_exists'),
            'reason.required' => __('api.request-consulting.reason_required'),
        ];
    }
}
