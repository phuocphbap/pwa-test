<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveGroupRequest extends FormRequest
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
            'reason' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => __('api.leave_group.reason_required'),
            'password.required' => __('api.user.password_required'),
        ];
    }
}
