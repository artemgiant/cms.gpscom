<?php

namespace App\Http\Requests;

use App\Rules\EquipmentTariff;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'object' => ['required'],
            'device' => ['required'],
            'imei' => ['required', 'numeric', 'exists:equipments,imei'],
            'phone' => ['required', 'numeric'],
            'phone2' => ['sometimes'],
        ];
    }

    public function attributes()
    {
        return [
            'date_start' => 'Дата подключения',
            'imei' => 'IMEI',
            'tariff_id' => 'Тариф',
        ];
    }
}
