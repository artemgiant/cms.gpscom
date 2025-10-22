<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClietStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'client_type' => ['required'],
            'name' => ['required'],
            'account' => ['required'],
            'contract_number' => ['sometimes'],
            'contract_date' => ['sometimes'],
            'person' => ['required'],
            'phone' => ['required', 'unique:clients,phone'],
            'manager' => ['required'],
            'accountant_phone' => ['sometimes'],
            'email' => ['sometimes'],
        ];
    }
}
