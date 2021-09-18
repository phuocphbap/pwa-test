<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AnswerContactRequest extends FormRequest
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
            'id' => 'required|exists:contacts,id',
            'answer' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('api.common.id_required'),
            'id.exists' => __('api.common.id_not_exists'),
            'answer.required' => __('api.contact.answer_required'),
        ];
    }
}
