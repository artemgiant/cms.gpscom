<?php


namespace App\Services;


use App\Models\Installation;

class InstallationService
{
    public static function getType()
    {
        return [
            1 => 'Монтаж',
            2 => 'Демонтаж',
            3 => 'Диагностика',
            4 => 'Ремонт',
            5 => 'Прочее'
        ];
    }
}
