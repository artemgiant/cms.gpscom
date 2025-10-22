<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    use HasFactory;

    /**
     * Type
     * @var string
     */
    const ASSEMBLING = 1; //Монтаж
    const DISMANTLING = 2; //Демонтаж
    const DIAGNOSTIC = 3; //Диагностика
    const REPAIR = 4; //Ремонт
    const OTHER = 5; // Прочее

    protected $table = 'installations';

    protected $fillable = [
        'user_id',
        'equipment_id',
        'comment',
        'price',
        'type',
        'date_create'
    ];

    /**
     * Отримати тип
     * @param $type
     * @return int|string
     */
    public static function getType($type)
    {
        switch ($type) {
            case 'Монтаж':
                $title = self::ASSEMBLING;
                break;
            case 'Демонтаж':
                $title = self::DISMANTLING;
                break;
            case 'Диагностика':
                $title = self::DIAGNOSTIC;
                break;
            case 'Ремонт':
                $title = self::REPAIR;
                break;
            case 'Прочее':
                $title = self::OTHER;
                break;
        }

        return $title;
    }

    public function getTypeAttribute($value)
    {
        switch ($value) {
            case self::ASSEMBLING:
                $title = 'Монтаж';
                break;
            case self::DISMANTLING:
                $title = 'Демонтаж';
                break;
            case self::DIAGNOSTIC:
                $title = 'Диагностика';
                break;
            case self::REPAIR:
                $title = 'Ремонт';
                break;
            case self::OTHER:
                $title = 'Прочее';
                break;
        }

        return $title;
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'user_id');
    }

    public function equipment()
    {
        return $this->hasOne(Equipment::class, 'id', 'equipment_id');
    }

}
