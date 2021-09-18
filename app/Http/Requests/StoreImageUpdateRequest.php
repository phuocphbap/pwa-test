<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageUpdateRequest extends FormRequest
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
            'id' => $this->image,
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
            'id' => 'required|exists:store_images,id',
            'caption' => 'nullable|max:255',
            'file_name' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('api.store_image.id_required'),
            'id.exists' => __('api.store_image.id_exists'),
            'caption.max' => __('api.store_image.caption_max'),
            'file_name.image' => __('api.store_article.file_name_image'),
            'file_name.mimes' => __('api.store_article.file_name_mimes'),
            'file_name.max' => __('api.store_article.file_name_max'),
        ];
    }
}
