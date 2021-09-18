<?php

namespace App\Http\Requests;

use App\Rules\UpdateArticlesRequestRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticlesRequest extends FormRequest
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
            'id' => $this->article,
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
            'id' => 'required|exists:store_articles,id',
            'title' => 'nullable|max:255',
            'contents' => 'nullable',
            'image_id' => ['nullable', new UpdateArticlesRequestRule($this->id)],
            'file_name' => 'nullable',
            'file_name.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'title.max' => __('api.store_article.title_max'),
            'file_name.image' => __('api.store_article.file_name_image'),
            'file_name.mimes' => __('api.store_article.file_name_mimes'),
            'file_name.max' => __('api.store_article.file_name_max'),
        ];
    }
}
