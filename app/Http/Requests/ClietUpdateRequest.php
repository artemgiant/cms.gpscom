<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClietUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'id' => ['required'],
            'client_type' => ['required'],
            'name' => ['required'],
            'account' => ['required'],
            'contract_number' => ['sometimes'],
            'contract_date' => ['sometimes'],
            'person' => ['required'],
            'phone' => ['required'],
            'manager' => ['required'],
            'accountant_phone' => ['sometimes'],
            'email' => ['sometimes'],
        ];
    }
}
