<?php

namespace App\Rules;

use App\Models\SimCard;
use App\Models\Tariff;
use Illuminate\Contracts\Validation\Rule;

class EquipmentTariff implements Rule
{

    public function __construct()
    {
        //
    }


    public function passes($attribute, $value)
    {
        $tariff = Tariff::find($value);

        if ($tariff->id == 5) {
            return true;
        } else {

            $simCard = SimCard::where('phone', request()->get('phone'))
                ->first();

            if ($simCard) {
                return true;
            }

            return false;
        }
    }

    public function message()
    {
        return 'Такой сим карты не существует, или она уже подключена к другому оборудованию';
    }
}
