<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoStoreRequest extends FormRequest
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
            'storeId' => 'required|exists:stores,id',
            'phone' => 'nullable|min:8|max:15',
            'address' => 'nullable',
            'address_id' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'place_id' => 'nullable',
        ];
    }
    
    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        return [
            'storeId.required' => __('api.store.id_required'),
            'storeId.exists' => __('api.store.store_not_exists'),
            'phone.min' => __('api.user.phone_min'),
            'phone.max' => __('api.user.phone_max'),
        ];
    }
}
