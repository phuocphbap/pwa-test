<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlockContentRequest extends FormRequest
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
            'block.*.block_id' => 'required|exists:advertising_blocks,id',
            'block.*.content' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'block.*.block_id.required' => __('api.advertising.block_id_required'),
            'block.*.block_id.exists' => __('api.advertising.block_id_not_exists'),
        ];
    }
}
