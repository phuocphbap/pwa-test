<?php

namespace App\Http\Requests\Admin;

use App\Constant\StatusConstant;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyTermsRequest extends FormRequest
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
        $type = [
            StatusConstant::TYPE_TERMS_OF_USE, StatusConstant::TYPE_SYMBOL, StatusConstant::TYPE_PRIVACY_POLICY
        ];
        return [
            'file_name' => 'required|mimetypes:application/pdf',
            'type' => 'required|in:'. implode(',', $type),
        ];
    }

    public function messages()
    {
        return [
            'file_name.required' => __('api.store_article.file_name_required'),
            'file_name.mimetypes' => __('api.validation.file_name_mimetypes'),
            'type.required' => __('api.validation.type_required'),
            'type.in' => __('api.validation.type_invalid'),
        ];
    }
}
