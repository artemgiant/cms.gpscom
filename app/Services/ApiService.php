<?php


namespace App\Services;

use App\Models\Analitic;
use App\Models\Device;
use App\Models\Equipment;
use App\Models\Operator;
use App\Models\SimCard;
use App\Models\Tariff;
use App\Models\SimCardHistory;
use App\Models\User;
use App\Models\Wialon;
use App\Models\WialonError;
use GuzzleHttp\Client;
use \App\Models\ClientTariff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiService
{
    /**
     * Метод авторизації
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function apiLogin()
    {
        try {
            $token = SettingService::getAccessToken();

            if (!$token->status) {
                return null;
            }

            $wialon_api = new Wialon();
            $result = $wialon_api->login($token->value);
            if (isset($result['eid'])) {
                session()->push('eid', $result['eid']);
                return $result['eid'];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }


    public static function getAllResources()
    {
        $client = new Client();
        $sessionId = self::apiLogin();
        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_resource","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":261,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        return $rows;
    }

    /**
     * Отримати інфориацію про клієнта
     * @param $client_id
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getClientById($client_id)
    {
        try {
            $client = \App\Models\Client::where('client_id', $client_id)->first();

            if ($client) {
                $client->client_id = $client_id;
                $client->update();
                return $client;
            } else {
                $client = new Client();
                $sessionId = self::apiLogin();

                $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_item&params={"id":' . $client_id . ',"force":1,"flags":261,"from":0,"to":0}&sid=' . $sessionId;
                $data = $client->post($url);
                $row = json_decode($data->getBody()->getContents());
                $clientData = $row->item;

                $isClient = \App\Models\Client::where('client_id', $client_id)
                    ->where('account', $clientData->nm)
                    ->first();

                if (!$isClient) {

                    $newClient = new \App\Models\Client();
                    $newClient->client_id = $client_id;
                    $newClient->name = $clientData->nm;
                    $newClient->account = $clientData->nm;
                    $newClient->person = $clientData->nm;
                    $newClient->phone = isset($clientData->ph) ? SettingService::convertPhone($clientData->ph) : null;
                    $newClient->manager = User::ADMIN;
                    $newClient->status = true;
                    $newClient->client_type = 'too';
                    $newClient->filling_status = false;

                    if ($newClient->save()) {

                        /** Збереження повідомлення */
                        NotificationService::addNotification(User::ADMIN, null, $newClient->id, null, 'Добавлен новый клиент ' . $clientData->nm);
                        return $newClient;
                    }
                }
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Отримання типу девайсу
     * @param $type_id
     * @return null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getDriversType($type_id)
    {
        try {
            $device = Device::where('device_id', $type_id)->first();
            if ($device) {
                return $device->name;
            } else {
                $items = Device::getDevices();

                foreach ($items as $item) {
                    if ($item->hw == $type_id) {
                        $deviceName = $item->nm;
                        if ($deviceName) {
                            $existingDevice = Device::where('device_id', $type_id)->first();
                            if ($existingDevice) {
                                $existingDevice->name = $deviceName;
                                $existingDevice->save();
                                return $existingDevice->name;
                            } else {
                                $newDevice = new Device();
                                $newDevice->device_id = $type_id;
                                $newDevice->name = $deviceName;
                                $newDevice->save();
                                return $deviceName;
                            }
                        }
                    }
                }

                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Відключити всі видалені з Wialon обєкти
     */
    public static function turnOffEquipments($imeis)
    {
        if ($imeis) {
            $equipments = Equipment::whereNotIn('imei', $imeis)
                ->whereNull('date_end')
                ->get();

            if ($equipments) {
                foreach ($equipments as $equipment) {
                    $equipment->update([
                        'status' => false,
                        'date_end' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    /**
     * Видалити всі видалені з Wialon обєкти
     */
    public static function deleteOffEquipments($imeis)
    {
        $equipments = Equipment::whereNotIn('imei', $imeis)
            ->get();

        if ($equipments) {
            foreach ($equipments as $equipment) {
                $histories = SimCardHistory::where('equipment_id', $equipment->id)->get();
                foreach ($histories as $history) {
                    $history->delete();
                }
                $equipment->delete();
            }
        }
    }


    /**
     * Отримати та оновити інформацію про обєкти
     */
    public static function updateEquipments()
    {
        $client = new Client();
        $sessionId = self::apiLogin();

        if (!$sessionId) {
            return true;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        if (isset($rows->items)) {
            $items = $rows->items;
            $imeis = [];
            foreach ($items as $k => $item) {
                $imeis[] = $item->uid;

                $client = ApiService::getClientById($item->bact);
                $device = ApiService::getDriversType($item->hw);
                $equipment = Equipment::where('imei', $item->uid)->first();

                if ($equipment) {
                    if ($item->dactt) {
                        $equipment->status = Equipment::DEACTIVE;
                        $equipment->date_end = gmdate("Y-m-d H:i:s ", $item->dactt);
                    } else {
                        $equipment->status = Equipment::ACTIVE;

                        if ($item->ct) {
                            if ($equipment->custom_update == 0) {
                                $equipment->date_start = gmdate("Y-m-d H:i:s ", $item->ct);
                            }

                            $equipment->date_end = null;
                        }
                    }
                    /** Деактивація обєкта */
                    if ($client) {
                        $equipment->client_id = $client->id;
                    }

                    if ($device) {
                        $equipment->device = $device;
                    }

                    $equipment->phone = SettingService::convertPhone($item->ph);
                    $equipment->phone2 = SettingService::convertPhone($item->ph2);
                    $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph)], ['phone', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph2)], ['phone', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone = null;
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipments->tariff_id = $tariff->id;

                        }
                        $equipments->save();
                    }
                    $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph2)], ['phone2', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph)], ['phone2', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone2 = null;
                        $equipments->save();
                    }
                    $equipment->object = $item->nm;


                    $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->first();
                    if ($simCard) {
                        $date = Carbon::parse($simCard->updated_at);
                        $isToday = $date->isToday();
                        $equipment->operator = $simCard->operator;

                        if ($equipment->tariff_id == Tariff::BEZ_SIM || empty($equipment->tariff_id)) {
                            if ($simCard->operator == "m2m" || $simCard->operator == "M2m") {
                                $tariff = Tariff::where('title', 'like', "%Весь мир%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else if ($simCard->operator == "Beeline" || $simCard->operator == "beeline" || $simCard->operator == "kcell") {
                                $tariff = Tariff::where('title', 'like', "%Казахстан%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else {
                                $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                }
                            }
                        }

                    } else {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipment->tariff_id = $tariff->id;
                        }
                    }
                    if (empty($item->ph)) {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipment->tariff_id = $tariff->id;
                        }
                    }
                    $equipment->update();
                    $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->first();

                    if ($simCard) {
                        $simCard->status = true;
                        $simCard->update();

                        if ($simCard) {
                            if (!SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'equipment_id' => $equipment->id,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => Auth::id(),
                                    'operation' => 'Подключение',

                                ]);
                            }
                        } else {
                            if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'equipment_id' => $equipment->id,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => Auth::id(),
                                    'operation' => 'Подключение',
                                ]);
                            }
                        }
                    }
                } else {
                    /** Сім карта */
                    $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->first();

                    if ($simCard) {
                        $simCard->status = true;
                        $simCard->update();
                        if ($simCard) {
                            if (!SimCardHistory::where([['sim_card_id', $simCard->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => Auth::id(),
                                    'operation' => 'Подключение',

                                ]);
                            }
                        } else {
                            if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'equipment_id' => $equipment->id,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => Auth::id(),
                                    'operation' => 'Подключение',
                                ]);
                            }
                        }
                    } else {
                        $tariff = Tariff::BEZ_SIM;
                    }

                    /** Обєкт */
                    $newEquipment = new Equipment();
                    $newEquipment->object = $item->nm;
                    $newEquipment->device = $device;
                    $newEquipment->imei = $item->uid;

                    $newEquipment->phone = SettingService::convertPhone($item->ph);
                    $newEquipment->phone2 = SettingService::convertPhone($item->ph2);
                    $equipments = Equipment::where([['id', '!=', $newEquipment->id], ['phone', SettingService::convertPhone($item->ph)], ['phone', '!=', 0]])->orWhere([['id', '!=', $newEquipment->id], ['phone', SettingService::convertPhone($item->ph2)], ['phone', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone = null;
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipments->tariff_id = $tariff->id;
                        }
                        $equipments->save();
                    }
                    $equipments = Equipment::where([['id', '!=', $newEquipment->id], ['phone2', SettingService::convertPhone($item->ph)], ['phone2', '!=', 0]])->orWhere([['id', '!=', $newEquipment->id], ['phone2', SettingService::convertPhone($item->ph2)], ['phone2', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone2 = null;
                        $equipments->save();
                    }

                    $newEquipment->client_id = $client ? $client->id : null;
                    $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->orWhere('phone', $item->ph)->first();
                    if ($simCard) {
                        $date = \Carbon\Carbon::parse($simCard->updated_at);
                        $isToday = $date->isToday();
                        $newEquipment->operator = $simCard->operator;

                        if ($newEquipment->tariff_id == 5 || empty($newEquipment->tariff_id)) {
                            if ($simCard->operator == "m2m" || $simCard->operator == "M2m") {
                                $tariff = Tariff::where('title', 'like', "%Весь мир%")->first();
                                if ($tariff) {
                                    $newEquipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $newEquipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else if ($simCard->operator == "Beeline" || $simCard->operator == "beeline" || $simCard->operator == "kcell") {
                                $tariff = Tariff::where('title', 'like', "%Казахстан%")->first();
                                if ($tariff) {
                                    $newEquipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $newEquipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else {
                                $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                if ($tariff) {
                                    $newEquipment->tariff_id = $tariff->id;
                                }
                            }
                        }

                    } else {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $newEquipment->tariff_id = $tariff->id;
                        }
                    }
                    if (empty($item->ph)) {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $newEquipment->tariff_id = $tariff->id;
                        }
                    }
                    if ($item->dactt) {
                        $newEquipment->status = Equipment::DEACTIVE;
                        $newEquipment->date_start = null;
                        $newEquipment->date_end = gmdate("Y-m-d H:i:s ", $item->dactt);
                    } else {
                        $newEquipment->status = Equipment::ACTIVE;
                        $newEquipment->date_start = gmdate("Y-m-d H:i:s ", $item->ct);
                        $newEquipment->date_end = null;
                    }

                    if ($newEquipment->save()) {
                        NotificationService::addNotification(User::ADMIN, $newEquipment->id, null, null, 'Добавлен новый объект ' . $item->nm);
                    }
                }
                $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph2))
                    ->orWhere('phone', $item->ph2)->first();

                if ($simCard) {
                    $simCard->status = true;
                    $simCard->update();
                }
            }

            ApiService::turnOffEquipments($imeis);
        }

        return true;
    }

    /**
     * Оновлення наявного обладнання
     */
    public static function updateAllEquipments()
    {
        $client = new Client();
        $sessionId = self::apiLogin();

        if (!$sessionId) {
            return true;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        if (isset($rows->items)) {

            foreach ($rows->items as $k => $item) {
                $client = ApiService::getClientById($item->bact);
                $device = ApiService::getDriversType($item->hw);
                $equipment = Equipment::where('imei', $item->uid)->first();
                if ($equipment) {

                    if ($item->dactt) {
                        $equipment->status = Equipment::DEACTIVE;
                        $equipment->date_start = null;
                        $equipment->date_end = gmdate("Y-m-d H:i:s ", $item->dactt);

                    } else {
                        $equipment->status = Equipment::ACTIVE;

                        if ($simCard) {
                            if (!SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'equipment_id' => $equipment->id,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => User::ADMIN,
                                    'operation' => 'Подключение',

                                ]);
                            }
                        } else {
                            if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                $sim_card_history = new SimCardHistory();
                                $sim_card_history->create([
                                    'sim_card_id' => $simCard ? $simCard->id : null,
                                    'equipment_id' => $equipment->id,
                                    'operation_date_time' => date('Y-m-d H:i:s'),
                                    'user_id' => User::ADMIN,
                                    'operation' => 'Подключение',
                                ]);
                            }
                        }
                        if ($item->ct) {
                            if ($equipment->custom_update == 0) {
                                $equipment->date_start = gmdate("Y-m-d H:i:s ", $item->ct);
                            }
                            $equipment->date_end = null;
                        }
                    }

                    /** Деактивація обєкта */

                    if ($client) {
                        $equipment->client_id = $client->id;
                    }

                    if ($device) {
                        $equipment->device = $device;
                    }

                    $equipment->object = $item->nm;

                    $equipment->phone = SettingService::convertPhone($item->ph);
                    $equipment->phone2 = SettingService::convertPhone($item->ph2);
                    $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph)], ['phone', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph2)], ['phone', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone = null;
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipments->tariff_id = $tariff->id;

                        }
                        $equipments->save();
                    }
                    $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph2)], ['phone2', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph)], ['phone2', '!=', 0]])->first();
                    if (!empty($equipments)) {
                        $equipments->phone2 = null;
                        $equipments->save();
                    }

                    $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->orWhere('phone', $item->ph)->first();
                    if ($simCard) {
                        $date = \Carbon\Carbon::parse($simCard->updated_at);
                        $isToday = $date->isToday();
                        $equipment->operator = $simCard->operator;

                        if ($equipment->tariff_id == 5 || empty($equipment->tariff_id)) {
                            if ($simCard->operator == "m2m" || $simCard->operator == "M2m") {
                                $tariff = Tariff::where('title', 'like', "%Весь мир%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else if ($simCard->operator == "Beeline" || $simCard->operator == "beeline" || $simCard->operator == "kcell") {
                                $tariff = Tariff::where('title', 'like', "%Казахстан%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    }
                                }
                            } else {
                                $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                if ($tariff) {
                                    $equipment->tariff_id = $tariff->id;
                                }
                            }
                        }
                    } else {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipment->tariff_id = $tariff->id;
                        }
                    }
                    if (empty($item->ph)) {
                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                        if ($tariff) {
                            $equipment->tariff_id = $tariff->id;
                        }
                    }
                    $equipment->update();
                }
                $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->orWhere('phone', $item->ph)->orWhere('phone', SettingService::convertPhone($item->ph2))->first();

                if ($simCard) {
                    $simCard->status = true;
                    $simCard->update();
                }

            }

        }

        return true;
    }

    /**
     * Деактивація обєктів
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deactiveEquipments()
    {
        $client = new Client();
        $sessionId = self::apiLogin();

        if (!$sessionId) {
            return true;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        $items = $rows->items;

        $imeis = [];
        foreach ($items as $k => $item) {
            $imeis[] = $item->uid;
        }

        ApiService::turnOffEquipments($imeis);

        return true;
    }

    /**
     * Оновлення кієнтів в обєкті
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function updateEquipmentsClients()
    {
        $client = new Client();
        $sessionId = self::apiLogin();

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


    public static function getClients()
    {
        $client = new Client();
        $sessionId = self::apiLogin();

        if (!$sessionId) {
            return true;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"user","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $clients = json_decode($data->getBody()->getContents());

        foreach ($clients->items as $clientData) {

            $client = \App\Models\Client::where('account', $clientData->nm)->first();
            if ($client) {
                $client->client_id = $clientData->id;
                $client->update();
            } else {

                $newClient = new \App\Models\Client();
                $newClient->client_id = $clientData->id;
                $newClient->name = $clientData->nm;
                $newClient->account = $clientData->nm;
                $newClient->person = $clientData->nm;
                $newClient->phone = isset($clientData->ph) ? SettingService::convertPhone($clientData->ph) : null;
                $newClient->manager = User::ADMIN;
                $newClient->status = true;
                $newClient->client_type = 'too';
                $newClient->filling_status = false;

                if ($newClient->save()) {

                    /** Збереження повідомлення */
                    NotificationService::addNotification(User::ADMIN, null, $newClient->id, null, 'Добавлен новый клиент ' . $clientData->nm);

                }
            }
        }

    }

    public static function updateClients()
    {
        try {

            $client = new Client();
            $sessionId = self::apiLogin();

            if (!$sessionId) {
                return true;
            }

            $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
            $data = $client->post($url);
            $rows = json_decode($data->getBody()->getContents());

            if (isset($rows->items)) {
                foreach ($rows->items as $k => $item) {
                    $clientUser = \App\Models\Client::where('client_id', $item->bact)->first();

                    if ($clientUser) {
                        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_item&params={"id":' . $item->bact . ',"force":1,"flags":261,"from":0,"to":0}&sid=' . $sessionId;
                        $data = $client->post($url);
                        $row = json_decode($data->getBody()->getContents());
                        $clientData = $row->item;

                        $clientUser->client_id = $clientData->bact;
                        $clientUser->account = $clientData->nm;
                        $clientUser->update();
                    }
                }
            }

        } catch (\Exception $e) {
            return true;
        }
    }

    public static function getEquipments()
    {
        try {

            $client = new Client();
            $sessionId = self::apiLogin();

            if (!$sessionId) {
                return true;
            }

            $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"rel_creation_time"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
            $data = $client->post($url);
            $rows = json_decode($data->getBody()->getContents());
            $items = $rows->items;

            $imeis = [];
            foreach ($items as $k => $item) {
                $imeis[] = $item->uid;

                if ($item->uid == '350317176283780') {
                    $client = ApiService::getClientById($item->bact);
                    $device = ApiService::getDriversType($item->hw);
                    $equipment = Equipment::where('imei', $item->uid)->first();
                    if ($equipment) {

                        /** Деактивація обєкта */
                        if ($item->dactt) {
                            $equipment->status = Equipment::DELETE;
                            $equipment->date_end = gmdate("Y-m-d H:i:s ", $item->dactt);
                        } else {
                            $equipment->status = Equipment::ACTIVE;

                            if ($item->ct) {
                                if ($equipment->custom_update == 0) {
                                    $equipment->date_start = gmdate("Y-m-d H:i:s ", $item->ct);
                                }
                                $equipment->date_end = null;
                            }
                        }

                        if ($client) {
                            $equipment->client_id = $client->id;
                        }

                        if ($device) {
                            $equipment->device = $device;
                        }

                        $equipment->object = $item->nm;

                        $equipment->phone = SettingService::convertPhone($item->ph);
                        $equipment->phone2 = SettingService::convertPhone($item->ph2);

                        $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph)], ['phone', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone', SettingService::convertPhone($item->ph2)], ['phone', '!=', 0]])->first();
                        if (!empty($equipments)) {
                            $equipments->phone = null;
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $equipments->tariff_id = $tariff->id;

                            }
                            $equipments->save();
                        }
                        $equipments = Equipment::where([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph2)], ['phone2', '!=', 0]])->orWhere([['id', '!=', $equipment->id], ['phone2', SettingService::convertPhone($item->ph)], ['phone2', '!=', 0]])->first();
                        if (!empty($equipments)) {
                            $equipments->phone2 = null;
                            $equipments->save();
                        }

                        $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->first();
                        if ($simCard) {
                            $date = \Carbon\Carbon::parse($simCard->updated_at);
                            $isToday = $date->isToday();
                            $equipment->operator = $simCard->operator;
                            if ($equipment->tariff_id == 5 || empty($equipment->tariff_id)) {
                                if ($simCard->operator == "m2m" || $simCard->operator == "M2m") {
                                    $tariff = Tariff::where('title', 'like', "%Весь мир%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    } else {
                                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                        if ($tariff) {
                                            $equipment->tariff_id = $tariff->id;
                                        }
                                    }
                                } else if ($simCard->operator == "Beeline" || $simCard->operator == "beeline" || $simCard->operator == "kcell") {
                                    $tariff = Tariff::where('title', 'like', "%Казахстан%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    } else {
                                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                        if ($tariff) {
                                            $equipment->tariff_id = $tariff->id;
                                        }
                                    }
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $equipment->tariff_id = $tariff->id;
                                    }
                                }
                            }
                        } else {
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $equipment->tariff_id = $tariff->id;
                            }
                        }
                        if (empty($item->ph)) {
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $equipment->tariff_id = $tariff->id;
                            }
                        }
                        $equipment->update();
                    } else {
                        /** Сім карта */
                        $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->first();

                        if ($simCard) {
                            $simCard->status = true;
                            $simCard->update();

                            if ($simCard) {
                                if (!SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                    $sim_card_history = new SimCardHistory();
                                    $sim_card_history->create([
                                        'sim_card_id' => $simCard ? $simCard->id : null,
                                        'equipment_id' => $equipment->id,
                                        'operation_date_time' => date('Y-m-d H:i:s'),
                                        'operation' => 'Подключение',

                                    ]);
                                }
                            } else {
                                if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                                    $sim_card_history = new SimCardHistory();
                                    $sim_card_history->create([
                                        'sim_card_id' => $simCard ? $simCard->id : null,
                                        'equipment_id' => $equipment->id,
                                        'operation_date_time' => date('Y-m-d H:i:s'),
                                        'operation' => 'Подключение',
                                    ]);
                                }
                            }
                        } else {
                            $tariff = Tariff::BEZ_SIM;
                        }

                        /** Обєкт */
                        $newEquipment = new Equipment();
                        $newEquipment->object = $item->nm;
                        $newEquipment->device = $device;
                        $newEquipment->imei = $item->uid;

                        $newEquipment->phone = SettingService::convertPhone($item->ph);
                        $newEquipment->phone2 = SettingService::convertPhone($item->ph2);

                        $equipments = Equipment::where([['id', '!=', $newEquipment->id], ['phone', SettingService::convertPhone($item->ph)], ['phone', '!=', 0]])->orWhere([['id', '!=', $newEquipment->id], ['phone', SettingService::convertPhone($item->ph2)], ['phone', '!=', 0]])->first();
                        if (!empty($equipments)) {
                            $equipments->phone = null;
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $equipments->tariff_id = $tariff->id;
                            }
                            $equipments->save();
                        }
                        $equipments = Equipment::where([['id', '!=', $newEquipment->id], ['phone2', SettingService::convertPhone($item->ph)], ['phone2', '!=', 0]])->orWhere([['id', '!=', $newEquipment->id], ['phone2', SettingService::convertPhone($item->ph2)], ['phone2', '!=', 0]])->first();
                        if (!empty($equipments)) {
                            $equipments->phone2 = null;
                            $equipments->save();
                        }
                        $newEquipment->client_id = $client ? $client->id : null;
                        $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->orWhere('phone', $item->ph)->first();
                        if ($simCard) {
                            $date = \Carbon\Carbon::parse($simCard->updated_at);
                            $isToday = $date->isToday();
                            $equipment->operator = $simCard->operator;
                            if ($newEquipment->tariff_id == 5 || empty($newEquipment->tariff_id)) {
                                if ($simCard->operator == "m2m" || $simCard->operator == "M2m") {
                                    $tariff = Tariff::where('title', 'like', "%Весь мир%")->first();
                                    if ($tariff) {
                                        $newEquipment->tariff_id = $tariff->id;
                                    } else {
                                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                        if ($tariff) {
                                            $newEquipment->tariff_id = $tariff->id;
                                        }
                                    }
                                } else if ($simCard->operator == "Beeline" || $simCard->operator == "beeline" || $simCard->operator == "kcell") {
                                    $tariff = Tariff::where('title', 'like', "%Казахстан%")->first();
                                    if ($tariff) {
                                        $newEquipment->tariff_id = $tariff->id;
                                    } else {
                                        $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                        if ($tariff) {
                                            $newEquipment->tariff_id = $tariff->id;
                                        }
                                    }
                                } else {
                                    $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                                    if ($tariff) {
                                        $newEquipment->tariff_id = $tariff->id;
                                    }
                                }
                            }
                        } else {
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $newEquipment->tariff_id = $tariff->id;
                            }
                        }
                        if (empty($item->ph)) {
                            $tariff = Tariff::where('title', 'like', "%Без сим%")->first();
                            if ($tariff) {
                                $newEquipment->tariff_id = $tariff->id;
                            }
                        }

                        if ($item->dactt) {
                            $newEquipment->status = Equipment::DELETE;
                            $newEquipment->date_start = null;
                            $newEquipment->date_end = gmdate("Y-m-d H:i:s ", $item->dactt);
                        } else {
                            $newEquipment->status = Equipment::ACTIVE;
                            $newEquipment->date_start = gmdate("Y-m-d H:i:s ", $item->ct);
                            $newEquipment->date_end = null;
                        }

                        if ($newEquipment->save()) {
                            NotificationService::addNotification(User::ADMIN, $newEquipment->id, null, null, 'Добавлен новый объект ' . $item->nm);
                        }
                    }
                }
                $simCard = SimCard::where('phone', SettingService::convertPhone($item->ph))->orWhere('phone', $item->ph)->orWhere('phone', SettingService::convertPhone($item->ph2))->first();

                if ($simCard) {
                    $simCard->status = true;
                    $simCard->update();
                }
            }

            ApiService::turnOffEquipments($imeis);

        } catch (\Exception $e) {
            return $e;
        }
    }

    // analitycs
    public static function saveAnalitycs()
    {
        $a_date = date("Y-m-d");
        $latest_day = date("t", strtotime($a_date));
        $current_day = date("d");
        $date_m = date('m');
        if ($current_day == $latest_day && ((Analitic::orderBy('id', 'DESC')->first() && Analitic::orderBy('id', 'DESC')->first()->month != $date_m) || !Analitic::orderBy('id', 'DESC')->first())) {
            $client_types = ["ip", "fl", "too"];

            foreach ($client_types as $key => $client_type) {
                $query = \App\Models\Client::query();
                $query->select('clients.*');
                $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
                $query->groupBy('clients.id');

                $query->where('clients.client_type', $client_type);

                $rows = $query->get();
                for ($i = 12; $i >= 0; $i--) {
                    ${"total_" . $i} = 0;
                    $total = 0;

                    foreach ($rows as $row) {
                        $price = $row->getPerMountAnalytic(date('Y.m', strtotime(\Carbon\Carbon::now()->subMonth($i))));
                        $total += $price;

                    }

                    ${"total_" . $i} = round($total / 1000, 2);

                }

                ${"graphic_total_" . $client_type} = [$total_6, $total_5, $total_4, $total_3, $total_2, $total_1];


                $query = \App\Models\Client::query();
                $query->select('clients.*');
                $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
                $query->groupBy('clients.id');

                $query->where('clients.client_type', $client_type);
                ${"total_" . $client_type} = ${"total_" . $client_type . "_act"} = ${"total_" . $client_type . "_deact"} = 0;
                $rows = $query->get();
                foreach ($rows as $row) {
                    ${"total_" . $client_type} += $row->getEquipments();
                    ${"total_" . $client_type . "_act"} += $row->getReportingEquipment();
                    ${"total_" . $client_type . "_deact"} += $row->getDeactiveEquipments();
                }
                for ($i = 12; $i >= 0; $i--) {
                    ${"active_count_" . $i} = 0;
                    $total = 0;
                    $query = \App\Models\Client::query();
                    $query->select('clients.*');
                    $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
                    $query->groupBy('clients.id');
                    $query->where('clients.client_type', $client_type);
                    $query->whereNull('equipments.date_end');
                    $query->where('equipments.status', true);
                    // $query->whereBetween('equipments.created_at', [Carbon::now()->subMonth($i), Carbon::now()->subMonth($i-1)]);
                    $rows = $query->get();
                    foreach ($rows as $row) {
                        $price = $row->getPerMountActive(date('Y.m', strtotime(\Carbon\Carbon::now()->subMonth($i))));
                        $total += $price;
                    }
                    ${"active_count_" . $i} = round($total, 2);
                }
                ${"active_counts_" . $client_type} = $active_count_6;
            }

            $client_types = ["ip", "fl", "too"];
            foreach ($client_types as $key => $client_type) {

                $query = \App\Models\Client::query();
                $query->select('clients.*', 'equipments.date_end');
                $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
                $query->groupBy('clients.id');
                $query->where('clients.client_type', $client_type);
                $query->orderBy('clients.id', 'asc');
                $rows = $query->get();

                $total = 0;

                foreach ($rows as $row) {
                    $price = $row->getPerMountPrice();
                    $total += $price;
                }
                ${"earnings_" . $client_type} = round($total / 1000, 2);
            }

            $analytic = new Analitic();
            $analytic->create([
                'active_counts_ip' => json_encode($active_counts_ip),
                'active_counts_fl' => json_encode($active_counts_fl),
                'active_counts_too' => json_encode($active_counts_too),
                'graphic_total_ip' => json_encode($graphic_total_ip),
                'graphic_total_fl' => json_encode($graphic_total_fl),
                'graphic_total_too' => json_encode($graphic_total_too),
                'earnings_ip' => json_encode($earnings_ip),
                'earnings_fl' => json_encode($earnings_fl),
                'earnings_too' => json_encode($earnings_too),
                'month' => $date_m
            ]);

        }
    }

    public static function deleteEquipment()
    {
        $items = EquipmentService::getAllEquipments();

        $uIds = [];
        foreach ($items as $item) {
            $uIds[] = $item->uid;
        }

        Equipment::whereNotIn('imei', $uIds)
            ->where('status', Equipment::DEACTIVE)
            ->update([
                'status' => Equipment::DELETE
            ]);
    }

    public static function updateOperatorId()
    {
        $equipments = Equipment::whereNotNull('operator')
            ->whereNull('operator_id')
            ->get();

        foreach ($equipments as $equipment) {
            $operator = Operator::where('slug', $equipment->operator)->first();

            if (isset($operator)) {
                $equipment->update([
                    'operator_id' => $operator->id
                ]);
            }
        }

        /* Оновлення всіх обладнень в яких немає sim_cards */
        Equipment::leftJoin('sim_cards', 'sim_cards.phone', 'equipments.phone')
            ->whereNull('sim_cards.phone')
            ->update([
                'equipments.operator' => null,
                'equipments.operator_id' => Tariff::BEZ_SIM
            ]);

        return true;
    }
}
