<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimCardStoreRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'phone' => ['required', 'numeric', 'unique:sim_cards,phone'],
            'operator' => 'required'
        ];
    }
}
