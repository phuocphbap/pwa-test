<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetDetailStoreIntroduction extends FormRequest
{
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
            'id' => 'required|exists:store_introductions,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('api.store.not_found'),
            'id.exists' => __('api.common.id_not_exists'),
        ];
    }
}
