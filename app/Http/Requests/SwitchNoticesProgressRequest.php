<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SwitchNoticesProgressRequest extends ApiRequest
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
            'type' => 'required|in:'.implode(',', $step),
        ];
    }

    public function messages()
    {
        return [
            'type.required' => __('api.validation.type_required'),
            'type.in' => __('api.validation.type_invalid'),
        ];
    }
}
