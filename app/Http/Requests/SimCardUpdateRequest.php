<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimCardUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'phone' => ['required', 'numeric', 'exists:sim_cards,phone'],
            'operator' => 'required'
        ];
    }
}
