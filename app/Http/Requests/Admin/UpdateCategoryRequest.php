<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Admin\UpdateCategoryRequestRule;

class UpdateCategoryRequest extends FormRequest
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
            'parent_id' => ['numeric', new UpdateCategoryRequestRule($this->id)],
            'name' => 'max:255',
            'description' => 'nullable',
        ];
    }


    public function messages()
    {
        return [
            'parent_id.numeric' => __('api.category.parent_id_number'),
            'name.max' => __('api.category.name_max'),
        ];
    }
}
