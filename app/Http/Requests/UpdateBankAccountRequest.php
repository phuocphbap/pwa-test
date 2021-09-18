<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
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
            'category_id' => 'nullable|exists:category_bank_accounts,id',
            'account_number' => 'nullable|max:50',
            'account_owner' => 'nullable|max:255',
            'account_definition' => 'nullable|max:255',
            'bank_name' => 'nullable|max:255',
            'branch_name' => 'nullable|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.exists' => __('api.bank_account.category_not_exists'),
            'account_number.max' => __('api.bank_account.account_number_max'),
            'account_owner.max' => __('api.bank_account.account_owner_max'),
            'bank_name.max' => __('api.bank_account.bank_name_max'),
            'branch_name.max' => __('api.bank_account.branch_name_max'),
        ];
    }
}
