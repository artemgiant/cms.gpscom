<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallationStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'date_create' => ['required'],
            'type' => ['required'],
            'comment' => ['required'],
            'price' => ['required'],
            'equipment_id' => ['required'],
        ];
    }
}
