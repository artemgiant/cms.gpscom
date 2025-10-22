<?php

namespace App\Http\Requests;

use App\Rules\EquipmentTariff;
use Illuminate\Foundation\Http\FormRequest;

class ClientEquipmentStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => ['required'],
            'object' => ['required'],
            'device' => ['required'],
            'imei' => ['required', 'numeric', 'unique:equipments,imei'],
            'phone' => ['required', 'unique:equipments,phone'],
            'phone2' => ['sometimes'],
            'date_start' => ['required'],
            'tariff_id' => ['required', new EquipmentTariff()],
        ];
    }

    public function attributes()
    {
        return [
            'date_start' => 'Дата подключения'
        ];
    }
}
