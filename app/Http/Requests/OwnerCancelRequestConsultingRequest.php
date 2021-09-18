<?php

namespace App\Http\Requests;

use App\Entities\RequestConsulting;

class OwnerCancelRequestConsultingRequest extends ApiRequest
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
            'id' => $this->id,
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
            'id' => 'required|exists:request_consultings,id',
            'reason' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('api.request-consulting.consulting_id_required'),
            'id.exists' => __('api.request-consulting.consulting_not_exists'),
            'reason.required' => __('api.request-consulting.reason_required'),
        ];
    }
}
