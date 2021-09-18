<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GetProgressChatRequest extends FormRequest
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
            'consultingId' => $this->consultingId,
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
            'consultingId' => 'required|exists:request_consultings,id',
        ];
    }

    public function messages()
    {
        return [
            'consultingId.required' => __('api.validation.consulting_id_required'),
            'consultingId.exists' => __('api.validation.consulting_id_exists'),
        ];
    }
}
