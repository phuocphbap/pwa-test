<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateMediaAdvertisingRequest extends FormRequest
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
            'file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'link_path' => 'nullable',
            'block_id' => 'required|exists:advertising_blocks,id',
        ];
    }

    public function messages()
    {
        return [
            'file_name.required' => __('api.store_article.file_name_required'),
            'file_name.image' => __('api.store_article.file_name_image'),
            'file_name.mimes' => __('api.store_article.file_name_mimes'),
            'file_name.max' => __('api.store_article.file_name_max'),
            // 'link_path.required' => __('api.advertising.link_path_required'),
            'block_id.required' => __('api.advertising.block_id_required'),
            'block_id.exists' => __('api.advertising.block_id_not_exists'),
        ];
    }
}
