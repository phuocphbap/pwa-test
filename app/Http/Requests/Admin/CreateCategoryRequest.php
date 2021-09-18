<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Admin\CreateCategoryRequestRule;

class CreateCategoryRequest extends FormRequest
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
            'parent_id' => ['required', 'numeric', new CreateCategoryRequestRule()],
            'name' => 'required|max:255',
            'description' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'parent_id.required' => __('api.category.parent_id_required'),
            'parent_id.numeric' => __('api.category.parent_id_number'),
            'name.required' => __('api.category.name_required'),
            'name.max' => __('api.category.name_max'),
        ];
    }
}
