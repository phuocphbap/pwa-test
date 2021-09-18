<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountBankRequest extends FormRequest
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
            'category_id' => 'required|exists:category_bank_accounts,id',
            'account_number' => 'required|max:50',
            'account_owner' => 'required|max:255',
            'bank_name' => 'required|max:255',
            'branch_name' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => __('api.bank_account.category_id_required'),
            'category_id.exists' => __('api.bank_account.category_not_exists'),
            'account_number.required' => __('api.bank_account.account_number_required'),
            'account_number.max' => __('api.bank_account.account_number_max'),
            'account_owner.required' => __('api.bank_account.account_owner_required'),
            'account_owner.max' => __('api.bank_account.account_owner_max'),
            'bank_name.required' => __('api.bank_account.bank_name_required'),
            'bank_name.max' => __('api.bank_account.bank_name_max'),
            'branch_name.required' => __('api.bank_account.branch_name_required'),
            'branch_name.max' => __('api.bank_account.branch_name_max'),
        ];
    }
}
