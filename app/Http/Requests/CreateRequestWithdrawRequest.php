<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequestWithdrawRequest extends FormRequest
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
            'amount' => 'required|numeric|min:50',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => __('api.withdraw.amount_required'),
            'amount.numeric' => __('api.withdraw.amount_numeric'),
            'amount.min' => __('api.withdraw.amount_min'),
        ];
    }
}
