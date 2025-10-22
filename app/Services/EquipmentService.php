<?php


namespace App\Services;


use App\Models\Equipment;
use App\Models\SimCard;
use App\Models\SimCardHistory;
use App\Models\Tariff;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EquipmentService
{
    /**
     * Отримати список девайсів
     * @return mixed
     */
    public static function getDevice()
    {
        return Equipment::groupBy('device')->select('id', 'device')->get();
    }

    public static function explodeDate($date)
    {
        $data = explode('.', $date);
        $year = (int)$data[0];
        $mount = (int)$data[1];

        return $mount;
    }

    /**
     * Список обладнання
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getAllEquipments()
    {
        $client = new Client();
        $sessionId = ApiService::apiLogin();

        if (!$sessionId) {
            return [];
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"rel_creation_time"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());
        $items = $rows->items;

        return $items;
    }

    /**
     * Інформація про обєкт по Imei
     * @param $imei
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getEquipmentByImei($imei)
    {
        $client = new Client();
        $sessionId = ApiService::apiLogin();

        if (!$sessionId) {
            return false;
        }

        try {
            $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_unique_id","propValueMask":"' . $imei . '","sortType":"rel_creation_time"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;

            $response = $client->post($url);
            $data = json_decode($response->getBody()->getContents());

            if (isset($data->items) && !empty($data->items)) {
                return $data->items[0];
            } else {
                return [];
            }

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Отримати та оновити інформацію про обєкти
     */
    public static function updateEquipments()
    {
        $items = self::getAllEquipments();

        if (empty($items)) {
            return true;
        }

        $simCardHistories = [];
        if (isset($items)) {
            foreach ($items as $k => $item) {
                $client = ApiService::getClientById($item->bact);
                $device = ApiService::getDriversType($item->hw);
                $equipment = Equipment::where('imei', $item->uid)->first();

                $convertedPhone = SettingService::convertPhone($item->ph);
                $convertedPhone2 = SettingService::convertPhone($item->ph2);

                if (isset($equipment)) {
                    self::updateExistingEquipment($equipment, $item, $client, $device, $convertedPhone, $convertedPhone2);
                } else {
                    self::createNewEquipment($item, $client, $device, $convertedPhone, $convertedPhone2);
                }

                self::updateSimCardStatus($convertedPhone, $convertedPhone2, $simCardHistories, $equipment);

                Log::info($k);
            }
        }

        SimCardHistory::insert($simCardHistories);
        self::setTariffToEmptyEquipment();

        return true;
    }

    public static function updateExistingEquipment($equipment, $item, $client, $device, $convertedPhone, $convertedPhone2)
    {
        $equipment->status = $item->dactt ? Equipment::DEACTIVE : Equipment::ACTIVE;
        $equipment->date_start = self::getDateStart($item);
        $equipment->date_end = self::getDateEnd($item);
        $equipment->client_id = $client->id ?? $equipment->client_id;
        $equipment->device = $device ?? $equipment->device;
        $equipment->phone = $convertedPhone;
        $equipment->phone2 = $convertedPhone2;
        $equipment->object = $item->nm;
        $equipment->operator = self::getOperator($convertedPhone, $equipment);
        $equipment->operator_id = self::getOperatorId($convertedPhone, null);
        $equipment->tariff_id = self::getTariffId($convertedPhone, $item, $equipment->tariff_id);

        $equipment->update();
    }

    public static function updateSimCardStatus($convertedPhone, $convertedPhone2, &$simCardHistories, $equipment)
    {
        if (isset($equipment)) {
            foreach ([$convertedPhone, $convertedPhone2] as $phone) {
                $simCard = SimCard::where('phone', $phone)->first();
                if ($simCard) {
                    $simCardHistoryStatus = SimCardHistory::where('sim_card_id', $simCard->id)
                        ->where('equipment_id', $equipment->id)
                        ->where('operation', 'Подключение')
                        ->exists();

                    $simCard->status = true;
                    $simCard->update();

                    if (!$simCardHistoryStatus) {
                        $simCardHistories[] = [
                            'sim_card_id' => $simCard->id,
                            'equipment_id' => $equipment->id,
                            'operation_date_time' => now(),
                            'user_id' => Auth::id(),
                            'operation' => 'Подключение',
                        ];
                    }
                }
            }
        }
    }


    public static function createNewEquipment($item, $client, $device, $convertedPhone, $convertedPhone2)
    {
        $newEquipment = new Equipment();
        $newEquipment->fill([
            'object' => $item->nm,
            'device' => $device,
            'imei' => $item->uid,
            'phone' => $convertedPhone,
            'phone2' => $convertedPhone2,
            'client_id' => $client->id ?? null,
            'status' => $item->dactt ? Equipment::DEACTIVE : Equipment::ACTIVE,
            'date_start' => self::getDateStart($item),
            'date_end' => self::getDateEnd($item),
            'operator' => self::getOperator($convertedPhone, null),
            'operator_id' => self::getOperatorId($convertedPhone, null),
            'tariff_id' => self::getTariffId($convertedPhone, $item)
        ]);

        if ($newEquipment->save()) {
            NotificationService::addNotification(User::ADMIN, $newEquipment->id, null, null, 'Добавлен новый объект ' . $item->nm);
        }
    }


    private static function getOperator($convertedPhone, $equipment)
    {
        $simCard = SimCard::where('phone', $convertedPhone)->first();
        return $simCard ? $simCard->operator : $equipment->operator ?? null;
    }

    public static function getOperatorId($convertedPhone, $equipment)
    {
        $simCard = SimCard::where('phone', $convertedPhone)->first();
        return $simCard ? $simCard->operator_id : null;
    }

    private static function getTariffId($convertedPhone, $item, $currentTariffId = null)
    {
        $simCard = SimCard::where('phone', $convertedPhone)
            ->where('phone', '!=', 0)
            ->first();

        if (!$simCard) {
            return Tariff::where('title', 'like', "%Без сим%")->value('id') ?: $currentTariffId;
        }

        $operatorTariffMapping = [
            'm2m' => 'Весь мир',
            'M2m' => 'Весь мир',
            'Beeline' => 'Казахстан',
            'beeline' => 'Казахстан',
            'kcell' => 'Казахстан'
        ];

        foreach ($operatorTariffMapping as $operator => $tariffTitle) {
            if ($simCard->operator === $operator) {
                return Tariff::where('title', 'like', "%{$tariffTitle}%")->value('id') ?: $currentTariffId;
            }
        }

        return Tariff::where('title', 'like', "%Без сим%")->value('id') ?: $currentTariffId;
    }


    public static function getDateStart($item)
    {

        return gmdate("Y-m-d H:i:s ", $item->ct);
    }

    public static function getDateEnd($item)
    {
        if (empty($item->dactt)) {
            return null;
        }

        if ($item->dactt == 0) {
            return null;
        }

        if (isset($item->dactt)) {
            return gmdate("Y-m-d H:i:s", $item->dactt);
        }
    }

    public static function setTariffToEmptyEquipment()
    {
        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();

        Equipment::where('phone', '0')
            ->update([
                'tariff_id' => $tariff->id,
                'operator' => null,
                'operator_id' => $tariff->operator_id,
            ]);
    }

}
