<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiTrait;

class ApiRequest extends FormRequest
{
    use ApiTrait;

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->first();
        $result = $this->respondError($errors);

        throw new HttpResponseException($result);
    }
}
