<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestroyAdvertisingMediaRequest extends FormRequest
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
            'media_id' => 'required|exists:advertising_media,id',
        ];
    }

    public function messages()
    {
        return [
            'media_id.required' => __('api.advertising.media_id_required'),
            'media_id.array' => __('api.advertising.media_id_array'),
            'media_id.exists' => __('api.advertising.media_id_not_exists'),
        ];
    }
}
