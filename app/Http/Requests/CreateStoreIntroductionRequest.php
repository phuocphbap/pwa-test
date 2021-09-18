<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreIntroductionRequest extends FormRequest
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
            'title' => 'required|max:255',
            'contents' => 'required',
            'file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('api.store_intro.title_required'),
            'title.max' => __('api.store_intro.title_max'),
            'contents.required' => __('api.store_intro.contents_required'),
            'file_name.required' => __('api.store_intro.file_name_required'),
            'file_name.image' => __('api.store_intro.file_name_image'),
            'file_name.mimes' => __('api.store_intro.file_name_mimes'),
            'file_name.max' => __('api.store_intro.file_name_max'),
        ];
    }
}
