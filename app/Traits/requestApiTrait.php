<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait requestApiTrait
{
    use response;

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->failed($validator->errors()->first(), 403, 'E03'));
    }
}