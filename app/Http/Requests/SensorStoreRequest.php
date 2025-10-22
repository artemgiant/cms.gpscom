<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorStoreRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => ['required'],
            'type' => ['required'],
            'sensor_id' => ['required'],
            'equipment_id' => ['required', 'exists:equipments,id'],
        ];
    }
}
