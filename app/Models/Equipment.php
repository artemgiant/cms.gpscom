<?php

namespace App\Models;

use App\Services\EquipmentService;
use App\Services\SettingService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'object',
        'device',
        'imei',
        'phone',
        'phone2',
        'date_start',
        'date_end',
        'tariff_id',
        'client_id',
        'status',
        'custom_update',
        'operator',
        'operator_id',
    ];

    /**
     * Статуси
     */
    const DELETE = 0;
    const ACTIVE = 1;
    const DEACTIVE = 2;

    /**
     * Тариф
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tariff()
    {
        return $this->hasOne(Tariff::class, 'id', 'tariff_id');
    }

    /**
     * Клієнт
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function operatorData()
    {
        return $this->hasOne(Operator::class, 'id', 'operator_id');
    }

    public function simCard()
    {
        return $this->hasOne(SimCard::class, 'phone', 'phone');
    }


    public static function getStatus($status)
    {
        return $status == 'Подключено' ? true : false;
    }

    /**
     * Ціна тарифу за день
     * @param $monthDays
     */
    public function getPricePerDay($tariffPrice, $monthDays)
    {
        $price = 0;
        if ($tariffPrice && $monthDays) {
            $price = $tariffPrice / $monthDays;
        }

        return $price;
    }

    /**
     * Отримати кількість робочих днів обладнання за цей місяць
     */
    public function getEquipmentWorkDays($tariffPrice, $days, $filterDate = null)
    {
        if ($filterDate) {
            $filterMount = EquipmentService::explodeDate($filterDate);
            $monthDays = Carbon::now()->month($filterMount)->daysInMonth;
        } else {
            $monthDays = Carbon::now()->daysInMonth;
        }
        if (!$this->date_end) {
            $pricePerDay = self::getPricePerDay($tariffPrice, $monthDays);
            $total = (($days) * $pricePerDay);

        } else {
            $pricePerDay = self::getPricePerDay($tariffPrice, $monthDays);
            $total = ($days * $pricePerDay);
        }

        return round($total, 2);
    }

    public function getEquipmentWorkDaysActive($days, $filterDate)
    {
        $filterMount = EquipmentService::explodeDate($filterDate);
        $monthDays = Carbon::now()->month($filterMount)->daysInMonth;
        if (!$this->date_end) {
            $total = 1;
        } else {
            $total = 0;
        }

        return $total;
    }

    public function getDotStatus()
    {
        switch ($this->status) {
            case self::ACTIVE:
                $title = 'label-success';
                break;
            case self::DELETE:
                $title = 'label-danger';
                break;
            case self::DEACTIVE:
                $title = 'label-secondary';
                break;
        }

        return $title;
    }

    /**
     *
     * @return int
     * @throws \Exception
     */
    public function getPrice($filterDate = null)
    {
        $price = 0;
        $date = date('Y.m');

        $days = $this->getDaysInFilteredMonth($filterDate);


        $clientTariff = ClientTariff::where('client_id', $this->client_id)
            ->where('tariff_id', Tariff::FIXX)
            ->first();

        if (!$clientTariff) {
            $clientTariff = ClientTariff::where('client_id', $this->client_id)
                ->where('tariff_id', $this->tariff_id)
                ->first();
        }

        if ($clientTariff) {
            if ($clientTariff->tariff_id == Tariff::FIXX) {
                return $clientTariff->price;
            } else {
                $price += $this->getEquipmentWorkDays($clientTariff->price, $days, $date);
            }

        } else {
            $tariffPrice = $this->getOperatorTariffPrice();
            $price += $this->getEquipmentWorkDays($tariffPrice, $days, $date);

        }


        return $price > 0 ? $price : 0;
    }

    public function getDaysInFilteredMonth( $filterDate)
    {

        $dateStart = $this->data_start;
        $dateEnd = $this->date_end;

        $start = \Carbon\Carbon::parse($dateStart);
        $filter = \Carbon\Carbon::parse($filterDate);

        // Визначаємо перший і останній день фільтруємого місяця
        $monthStart = $filter->copy()->startOfMonth();
        $monthEnd = $filter->copy()->endOfMonth();

        // Якщо date_end не задано, використовуємо кінець місяця або поточну дату
        if ($dateEnd === null) {
            $end = \Carbon\Carbon::now()->lt($monthEnd) ? \Carbon\Carbon::now() : $monthEnd;
        } else {
            $end = \Carbon\Carbon::parse($dateEnd);
        }

// Якщо period взагалі не перетинається з місяцем фільтра - повертаємо 0
        if ($end->lt($monthStart) || $start->gt($monthEnd)) {
            return 0;
        }

// Визначаємо реальний початок і кінець для підрахунку
        $countStart = $start->gt($monthStart) ? $start : $monthStart;
        $countEnd = $end->lt($monthEnd) ? $end : $monthEnd;

// Рахуємо кількість днів (включно)
        return $countStart->diffInDays($countEnd) + 1;
    }


    public function getTariff()
    {
        if (isset($this->operatorData->tariff)) {
            $tariff = $this->operatorData->tariff->title;
        } else {
            $tariff = 'без сим';
        }

        return $tariff;
    }

    public function getOperatorTariffPrice()
    {
        if (isset($this->operatorData->tariff)) {
            $price = $this->operatorData->tariff->price;
        } else {
            $price = Tariff::where('title', 'like', "%Без сим%")->value('price');
        }

        return $price;
    }


}
