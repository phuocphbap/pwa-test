<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BonusIndecatedRequest extends FormRequest
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
            'user_id' => 'array|exists:users,id',
            'amount' => 'required|numeric',
            'check_all' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => __('api.validation.user_id_not_exits'),
            'user_id.array' => __('api.validation.user_id_array'),
            'amount.required' => __('api.payment.amount_required'),
            'amount.numeric' => __('api.withdraw.amount_numeric'),
            'check_all.boolean' => __('api.bonus.check_all_boolean'),
        ];
    }
}
