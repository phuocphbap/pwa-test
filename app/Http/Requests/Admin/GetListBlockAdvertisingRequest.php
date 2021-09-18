<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GetListBlockAdvertisingRequest extends FormRequest
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
            'categoryId' => $this->categoryId,
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
            'categoryId' => 'required|exists:advertising_categories,id',
        ];
    }

    public function messages()
    {
        return [
            'categoryId.required' => __('api.bank_account.category_id_required'),
            'categoryId.exists' => __('api.bank_account.category_not_exists'),
        ];
    }
}
