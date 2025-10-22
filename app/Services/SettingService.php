<?php


namespace App\Services;


use App\Models\Setting;
use Carbon\Carbon;
use DateTime;

class SettingService
{
    public static function getSetting()
    {
        $setting = Setting::select('value', 'name')
            ->get()
            ->pluck('value', 'name');

        if (!$setting) {
            return null;
        }

        return $setting;
    }

    public static function getAccessToken()
    {
        return Setting::where('name', 'access_token')->first();
    }

    /**
     * Різниця в датах
     * @param $fdate
     * @param $tdate
     * @return string
     * @throws \Exception
     */
    public static function diffDays($fdate, $tdate)
    {
        $datetime1 = new DateTime($fdate);
        $datetime2 = new DateTime($tdate);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');

        if ($days <= 0) {
            $days = 1;
        }

        $daysInMonth = Carbon::now()->daysInMonth;
        if ($days > $daysInMonth) {
            $days = $daysInMonth;
        }

        return $days;
    }


    /**
     * Отримати кількість днів
     * @param $equipment
     * @param $filterDate
     * @return int|string
     * @throws \Exception
     */
    public static function getDays($equipment, $filterDate)
    {
        $dateStart = Carbon::parse($equipment->date_start);
        $dateEnd = $equipment->date_end ? Carbon::parse($equipment->date_end) : null;
        $filterDate = Carbon::createFromFormat('Y.m', $filterDate);
        $startOfMonth = $filterDate->copy()->startOfMonth();
        $endOfMonth = $filterDate->copy()->endOfMonth();

        // Умова 1: Якщо dateStart і dateEnd мають однаковий місяць, але не відповідають filterDate, повернути 0
        if ($dateEnd && $dateStart->format('Y-m') === $dateEnd->format('Y-m') && $dateStart->format('Y-m') !== $filterDate->format('Y-m')) {
            return '0';
        }

        // Умова 2: Якщо dateEnd немає, а місяць filterDate рівний місяцю dateStart
        if (!$dateEnd) {
            if ($dateStart->format('Y-m') === $filterDate->format('Y-m')) {
                // Повертаємо кількість днів від dateStart до кінця місяця
                return $endOfMonth->diffInDays($dateStart) + 1;
            } elseif ($dateStart->format('Y-m') >= $filterDate->format('Y-m')){
                return '0';
            } else {
                // Якщо місяць filterDate і dateStart не рівні, повертаємо повну кількість днів у місяці
                return $filterDate->daysInMonth;
            }
        }

        // Умова 3: Повернути кількість днів, які пройшли від початку місяця до dateEnd
        if ($dateStart->format('Y-m') === $dateEnd->format('Y-m')) {
            return $dateEnd->diffInDays($dateStart) + 1;
        }

        // Умова 4: Якщо рік-місяць filterDate не дорівнює рік-місяць dateEnd, віднімаємо дні від початку місяця dateStart
        if ($filterDate->format('Y-m') === $dateStart->format('Y-m') && $filterDate->format('Y-m') !== $dateEnd->format('Y-m')) {
            $daysInMonth = $filterDate->daysInMonth;  // Кількість днів у місяці filterDate
            $daysFromStart = $dateStart->day - 1;  // Кількість днів від початку місяця до dateStart
            return $daysInMonth - $daysFromStart;
        } else {
            if ($dateEnd && $dateEnd->format('Y-m') < $filterDate->format('Y-m')) {
                return '0';
            }

            if ($dateEnd && $dateEnd->format('Y-m') > $filterDate->format('Y-m')) {
                return $filterDate->daysInMonth;
            }
        }

        if ($dateEnd->format('Y-m') === $filterDate->format('Y-m')){
            return $dateEnd->day;
        }

        return '0';
    }





    /**
     * Конвертувати телефон
     * @param $phone
     * @return int
     */
    public static function convertPhone($phone)
    {
        $phone = str_replace('+', '', $phone);
        $phone = str_replace('-', '', $phone);

        return $phone;
    }
}
