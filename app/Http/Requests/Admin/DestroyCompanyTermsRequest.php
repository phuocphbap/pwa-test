<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestroyCompanyTermsRequest extends FormRequest
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
            'id' => 'required|array|exists:company_terms,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('api.common.id_required'),
            'id.array' => __('api.common.id_array'),
            'id.exists' => __('api.common.id_not_exists'),
        ];
    }
}
