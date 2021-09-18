<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTwoFaRequest extends FormRequest
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
        $step = ['0', '1'];
        return [
            'is_two_fa' => 'required|in:'.implode(',', $step),
        ];
    }
    public function messages()
    {
        return [
            'is_two_fa.required' => __('api.validation.type_required'),
            'is_two_fa.in' => __('api.validation.type_invalid'),
        ];
    }
}
