<?php

namespace App\Models;

use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    /**
     * Report Type
     */
    const REPORT_PER_MONTH = 1;
    const REPORT_ADVANCE = 2;
    const REPORT_MOUNTING = 3;

    /**
     * Client Type
     */
    const IP = 'ip';
    const FL = 'fl';
    const Тoo = 'too';

    protected $table = 'clients';

    protected $fillable = [
        'client_id',
        'name',
        'account',
        'contract_number',
        'contract_date',
        'person',
        'phone',
        'manager',
        'status',
        'client_type',
        'filling_status',
        'accountant_phone',
        'email'
    ];

    public function getClientType()
    {
        $tape = $this->client_type;

        switch ($tape) {
            case 'ip':
                $title = 'ИП';
                break;
            case 'fl':
                $title = 'ФЛ';
                break;
            case 'too':
                $title = 'ТОО';
                break;
        }

        return $title;
    }

    public function getDbClientType($tape)
    {
        switch ($tape) {
            case 'ИП':
                $title = 'ip';
                break;
            case 'ФЛ':
                $title = 'fl';
                break;
            case 'ТОО':
                $title = 'too';
                break;
        }

        return $title;
    }

    public function clientEquipment()
    {
        return $this->hasMany(Equipment::class, 'id', 'client_id');
    }

    public function getManager()
    {
        return $this->hasOne(User::class, 'id', 'manager');
    }

    public function getEquipments()
    {
        return Equipment::where('client_id', $this->id)->count();
    }

    public function getReportingEquipment()
    {
        return Equipment::where('client_id', $this->id)
            ->where('status', true)
            ->whereNull('date_end')
            ->count();
    }

    public function getActiveEquipments()
    {
        return Equipment::where('client_id', $this->id)->where('status', true)->count();
    }

    public function getDeactiveEquipments()
    {
        return Equipment::where('client_id', $this->id)->where('status', 2)->count();
    }

    /**
     * Отримати список татафів клієнта
     * @return mixed
     */
    public function getClientTariffs()
    {
        $tariffs = Tariff::select('tariffs.*', 'clients_tariffs.client_id', 'clients_tariffs.id as clients_tariff_id')
            ->leftJoin('clients_tariffs', 'tariffs.id', 'clients_tariffs.tariff_id')
            ->where('clients_tariffs.client_id', $this->id)
            ->get();

        return $tariffs;
    }

    /**
     * Отримати Id менеджера
     * @param $title
     * @return null
     */
    public function getManagerId($title)
    {
        $manager = DB::connection('mysql')->table('users')
            ->where('name', 'LIKE', '%' . trim($title) . '%')
            ->first();

        if (!$manager) {
            return 3;
        }

        return $manager->id;
    }

    /**
     * Вартість тарифів
     */
    public function getPerMountPrice()
    {
        $price = 0;
        $rows = Equipment::where('client_id', $this->id)->get();
        $filterDate = request()->get('mount') ? request()->get('mount') : date('Y.m');

        foreach ($rows as $row) {
            $days = SettingService::getDays($row, $filterDate);

            $clientTariff = ClientTariff::where('client_id', $row->client_id)
                ->where('tariff_id', Tariff::FIXX)
                ->first();

            if (!$clientTariff) {
                $clientTariff = ClientTariff::where('client_id', $row->client_id)
                    ->where('tariff_id', $row->tariff_id)
                    ->first();
            }

            if ($clientTariff) {

                if ($clientTariff->tariff_id == Tariff::FIXX) {
                    $price = $clientTariff->price;
                } else {
                    $price += $row->getEquipmentWorkDays($clientTariff->price, $days, $filterDate);
                }
            } else {
                $price += $row->getPrice();
            }
        }

        return round($price, 2);
    }


    public function getPerMountAnalytic($date)
    {
        $price = 0;
        $clientEquipment = Equipment::where('client_id', $this->id)->get();

        foreach ($clientEquipment as $equipment) {

            $days = SettingService::getDays($equipment, $date);

            $clientTariff = ClientTariff::where('client_id', $this->id)
                ->where('tariff_id', Tariff::FIXX)
                ->first();

            if (!$clientTariff) {
                $clientTariff = ClientTariff::where('client_id', $this->id)
                    ->where('tariff_id', $equipment->tariff_id)
                    ->first();
            }

            if ($clientTariff) {

                if ($clientTariff->tariff_id == Tariff::FIXX) {
                    return $clientTariff->price;
                } else {
                    $price += $equipment->getEquipmentWorkDays($clientTariff->price, $days, $date);
                }

            } else {
                $price += $equipment->getEquipmentWorkDays($equipment->tariff->price, $days, $date);
            }

        }

        return $price > 0 ? $price : 0;
    }

    public function getPerMountActive($date)
    {
        $count = 0;
        $clientEquipment = Equipment::where('client_id', $this->id)->get();

        foreach ($clientEquipment as $equipment) {
            $days = SettingService::getDays($equipment, $date);
            $count += $equipment->getEquipmentWorkDaysActive($days, $date);
        }

        return $count > 0 ? $count : 0;
    }

    public static function getClientRows($name)
    {
        $rows = [
            'client_id' => 'Wialon id',
            'name' => 'Компания',
            'account' => 'Аккаунт',
            'contract_number' => 'Номер договора',
            'contract_date' => 'Дата договора',
            'person' => 'Контактное лицо',
            'phone' => 'Контакты',
            'manager' => 'Ответственный менеджер',
            'client_type' => 'Тип',
            'accountant_phone' => 'Номер бухгалтера',
            'email' => 'Email'
        ];

        return $rows[$name];
    }

    public static function getCols($params)
    {
        $rows = [];

        if (isset($params['rows'])) {
            foreach ($params['rows'] as $name => $val) {
                $rows[$name] = self::getClientRows($name);
            }
        }

        return $rows;
    }

    public static function getCol($name, $row)
    {
        switch ($name) {
            case 'client_id':
                $item = $row->client_id;
                break;
            case 'name':
                $item = $row->name;
                break;
            case 'account':
                $item = $row->account;
                break;
            case 'contract_number':
                $item = $row->contract_number;
                break;
            case 'contract_date':
                $item = $row->contract_date ? date('Y-m-d H:i:s', strtotime($row->contract_date)) : '';
                break;
            case 'person':
                $item = $row->person;
                break;
            case 'phone':
                $item = $row->phone;
                break;
            case 'manager':
                $item = $row->manager;
                break;
            case 'client_type':
                $item = $row->client_type;
                break;
            case 'accountant_phone':
                $item = $row->accountant_phone;
                break;
            case 'email':
                $item = $row->email;
                break;
        }

        return $item;
    }

    public function getFixedEquipmentPrice()
    {
        $clientTariff = ClientTariff::where('client_id', $this->id)
            ->where('tariff_id', Tariff::FIXX)
            ->first();

        if (empty($clientTariff)) {
            return null;
        }

        $equipments = Equipment::where('client_id', $this->id)->count();

        return round($clientTariff->price / $equipments, 2);
    }
}
