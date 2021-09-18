<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageCreateRequest extends FormRequest
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
            'caption' => 'required|max:255',
            'file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'caption.required' => __('api.store_image.caption_required'),
            'caption.max' => __('api.store_image.caption_max'),
            'file_name.required' => __('api.store_article.file_name_required'),
            'file_name.image' => __('api.store_article.file_name_image'),
            'file_name.mimes' => __('api.store_article.file_name_mimes'),
            'file_name.max' => __('api.store_article.file_name_max'),
        ];
    }
}
