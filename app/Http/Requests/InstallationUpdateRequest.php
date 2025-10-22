<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallationUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'installation_id' => ['required'],
            'date_create' => ['required'],
            'type' => ['required'],
            'comment' => ['required'],
            'price' => ['required'],
            'equipment_id' => ['required'],
        ];
    }
}
