<?php

namespace App\Http\Requests\Admin;

use App\Entities\WithdrawRequest;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawUpdateStatusRequest extends FormRequest
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
        $type = [WithdrawRequest::PENDING_STATE, WithdrawRequest::ACCEPTED_STATE, WithdrawRequest::REJECTED_STATE, WithdrawRequest::DONE_STATE];

        return [
            'status' => 'required|in:'.implode(',', $type),
            'reason_rejected' => 'required_if:status,'.WithdrawRequest::REJECTED_STATE,
        ];
    }

    public function messages()
    {
        return [
            'status.required' => __('api.withdraw.status_required'),
            'status.in' => __('api.withdraw.status_in'),
            'reason_rejected.required_if' => __('api.withdraw.reason_rejected_required_if'),
        ];
    }
}
