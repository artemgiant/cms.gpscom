<?php


namespace App\Services;


use App\Models\Equipment;
use App\Models\SimCardHistory;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\Translation\t;

class ClientService
{
    public static function getClientManagers()
    {
        return User::all();
    }

    /**
     * @param $clientUser
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function updateClient($clientUser)
    {
        try {
            $client = new Client();
            $sessionId = ApiService::apiLogin();

            if (!$sessionId) {
                return true;
            }

            if ($clientUser) {
                $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_item&params={"id":' . $clientUser->client_id . ',"force":1,"flags":261,"from":0,"to":0}&sid=' . $sessionId;
                $data = $client->post($url);
                $row = json_decode($data->getBody()->getContents());
                $clientData = $row->item;

                $clientUser->client_id = $clientData->bact;
                $clientUser->account = $clientData->nm;
                $clientUser->update();

                return true;
            }

        } catch (\Exception $e) {
            return true;
        }
    }


    public static function updateEquipmentsClientsData($clientUser)
    {
        $items = EquipmentService::getAllEquipments();

        if (empty($items)) {
            return true;
        }

        $simCardHistories = [];
        if (isset($items)) {
            foreach ($items as $k => $item) {

                if ($clientUser->client_id != $item->bact) {
                    continue;
                }

                $client = ApiService::getClientById($item->bact);
                $device = ApiService::getDriversType($item->hw);
                $equipment = Equipment::where('imei', $item->uid)->first();

                $convertedPhone = SettingService::convertPhone($item->ph);
                $convertedPhone2 = SettingService::convertPhone($item->ph2);

                if (isset($equipment)) {
                    EquipmentService::updateExistingEquipment($equipment, $item, $client, $device, $convertedPhone, $convertedPhone2);
                } else {
                    EquipmentService::createNewEquipment($item, $client, $device, $convertedPhone, $convertedPhone2);
                }

                EquipmentService::updateSimCardStatus($convertedPhone, $convertedPhone2, $simCardHistories, $equipment);

                Log::info($k);
            }
        }

        SimCardHistory::insert($simCardHistories);
        EquipmentService::setTariffToEmptyEquipment();

        return true;
    }

    /**
     * @param $clientUser
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function updateEquipmentsClients($clientUser)
    {
        $client = new Client();
        $sessionId = ApiService::apiLogin();

        if (!$sessionId) {
            return false;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        if (!isset($rows->items)) {
            return false;
        }

        $items = $rows->items;
        foreach ($items as $k => $item) {

            if ($clientUser->client_id != $item->bact) {
                continue;
            }

            $client = ApiService::getClientById($item->bact);
            $equipment = Equipment::where('imei', $item->uid)->first();
            if ($equipment) {
                if ($client) {
                    $equipment->client_id = $client->id;
                    $equipment->update();
                }
            }
        }

        return true;
    }
}
