<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetArticlesRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'storeId' => $this->storeId,
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
            'storeId' => 'required|exists:stores,id',
        ];
    }

    public function messages()
    {
        return [
            'storeId.required' => __('api.store.not_found'),
            'storeId.exists' => __('api.store.store_not_exists'),
        ];
    }
}
