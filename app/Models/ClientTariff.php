<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClientTariff extends Model
{
    use HasFactory;

    protected $table = 'clients_tariffs';

    protected $fillable = [
        'client_id',
        'tariff_id',
        'price',
    ];

    public static function getTariffId($title)
    {
        $tariff = DB::connection('mysql')->table('tariffs')
            ->where('title', $title)
            ->first();

        if (!$tariff) {
            return 0;
        }

        return $tariff->id;
    }
}
