<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'id' => ['required'],
            'name' => ['required'],
            'type' => ['required'],
            'sensor_id' => ['required'],
        ];
    }
}
