<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimCard extends Model
{
    use HasFactory;

    protected $table = 'sim_cards';

    protected $fillable = [
        'phone',
        'status',
        'operator',
        'operator_id',
        'equipment_id',
    ];

    public static function getOperator($operator)
    {
        switch ($operator) {
            case 'Beeline':
                $title = 'beeline';
                break;
            case 'beeline':
                $title = 'beeline';
                break;
            case 'KCELL':
                $title = 'kcell';
                break;
            case 'kcell':
                $title = 'kcell';
                break;
            case 'M2M':
                $title = 'm2m';
                break;
            case 'm2m':
                $title = 'm2m';
                break;
            default:
                $title = 'без сим';
                break;
        }

        return $title;
    }

    public function getOperatorTitle()
    {
        if ($this->operator) {
            return self::getOperator($this->operator);
        } else {
            return $this->operatorData->title;
        }
    }

    public function operatorData()
    {
        return $this->hasOne(Operator::class, 'id', 'operator_id');
    }

    public function getStatus()
    {
        return $this->status ? 'Подключена' : 'Свободна';
    }

    public function changePhone($phone)
    {
        return str_replace('+', '', $phone);
    }


}
