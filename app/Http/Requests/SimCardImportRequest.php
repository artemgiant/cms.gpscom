<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimCardImportRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'operator' => 'required',
            'sim_cards_file' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'operator' => 'Оператор',
            'sim_cards_file' => 'Файл импорта',
        ];
    }
}
