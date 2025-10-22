<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $table = 'tariffs';

    /**
     * Тарифи
     */
    const BEZ_SIM = 5;
    const FIXX = 7;

    protected $fillable = [
        'title',
        'price',
        'operator_id'
    ];

    public function operator()
    {
        return $this->hasOne(Operator::class, 'id', 'operator_id');
    }

    /**
     * Отримати ціну тарифу
     * @return mixed
     */
    public function getTariffPrice()
    {
        $clientTariff = ClientTariff::find($this->clients_tariff_id);

        $price = 0;
        if ($clientTariff) {

            if ($clientTariff->tariff_id == Tariff::FIXX) {
                return $clientTariff->price;
            } else {
                $price += $clientTariff->price;
            }
        } else {
            $price += $this->price;
        }

        return $price;
    }
}
