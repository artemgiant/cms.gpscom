<?php


namespace App\Services;


use App\Models\Client;
use App\Models\ClientTariff;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Sensor;
use App\Models\SimCard;
use App\Models\SimCardHistory;
use Illuminate\Support\Facades\DB;

class DbService
{

    /**
     * Клієнти
     */
    public static function updateClients()
    {
        DB::connection('mysql')->table('clients')->truncate();
        $clients = DB::connection('mysql2')->table('clients')->get();
        foreach ($clients as $item) {

            $client = new Client();
            $client->id = $item->Id;
            $client->setConnection('mysql');
            $client->client_type = $client->getDbClientType($item->Type);
            $client->name = $item->NameClient;
            $client->account = $item->Account ?? '';
            $client->contract_number = $item->NumberTreaty;
            $client->contract_date = date('Y-m-d H:i:s', strtotime($item->DateTreaty));
            $client->person = $item->ContactFace;
            $client->phone = (int)$item->NumberClient;
            $client->manager = $client->getManagerId($item->ResponsibleManager);
            $client->save();
        }
    }

    /**
     * Тарифи клієнтів
     */
    public static function updateTariffs()
    {
        DB::connection('mysql')->table('clients_tariffs')->truncate();
        $clientTariffs = DB::connection('mysql2')->table('clienttariffs')->get();

        foreach ($clientTariffs as $item) {
            $clientTariff = new ClientTariff();
            $clientTariff->id = $item->Id;
            $clientTariff->setConnection('mysql');
            $clientTariff->client_id = $item->ClientId;
            $clientTariff->tariff_id = ClientTariff::getTariffId($item->TariffName);
            $clientTariff->price = $item->Price ?? null;
            $clientTariff->save();
        }
    }

    /**
     * Обєкти
     */
    public static function updateEquipments()
    {
        DB::connection('mysql')->table('equipments')->truncate();
        $equipments = DB::connection('mysql2')->table('equipments')->get();
        foreach ($equipments as $item) {
            $equipment = new Equipment();
            $equipment->setConnection('mysql');
            $equipment->id = $item->Id;
            $equipment->object = $item->Object;
            $equipment->device = $item->Device;
            $equipment->imei = (int)$item->IMEI;
            $equipment->phone = (int)$item->NumberGadget;
            $equipment->phone2 = (int)$item->NumberGadget2;

            if ($item->DateConnect) {
                $equipment->date_start = date('Y-m-d H:i:s', strtotime($item->DateConnect));
            }

            if ($item->DateDisconnect) {
                $equipment->date_end = date('Y-m-d H:i:s', strtotime($item->DateDisconnect));
            }
            $equipment->tariff_id = ClientTariff::getTariffId($item->Tariff);
            $equipment->client_id = $item->ClientId;
            $equipment->status = Equipment::getStatus($item->Status);
            $equipment->save();

        }
    }

    /**
     * Сім карти
     */
    public static function updateSimCards()
    {
        DB::connection('mysql')->table('sim_cards')->truncate();
        $simcards = DB::connection('mysql2')->table('simcards')->get();

        foreach ($simcards as $item) {
            $simCard = new SimCard();
            $simCard->id = $item->Id;
            $simCard->setConnection('mysql');
            $simCard->phone = (int)$item->Number;
            $simCard->status = $item->Status == 'Подключена' ? true : false;
            $simCard->operator = SimCard::getOperator($item->operator);
            $simCard->equipment_id = $item->EquipId;
            $simCard->save();

            if ($item->Status == 'Подключена') {
              if (!SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first() || (SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first() && SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first()->operation != "Подключение")) {
                $sim_card_history = new SimCardHistory();
                $sim_card_history->create([
                    'sim_card_id'=>$simCard?$simCard->id:null,
                    'equipment_id'=>$equipment->id,
                    'operation_date_time'=>date('Y-m-d H:i:s'),
                    'operation' => 'Подключение',

                ]);
              }
            } else {
              if (!SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first() || (SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first() && SimCardHistory::where([['sim_card_id',$simCard->id]])->orderBy('id','DESC')->first()->operation != "Деактивирование")) {
                $sim_card_history = new SimCardHistory();
                $sim_card_history->create([
                    'sim_card_id'=>$simCard?$simCard->id:null,
                    'equipment_id'=>$equipment->id,
                    'operation_date_time'=>date('Y-m-d H:i:s'),
                    'operation' => 'Деактивирование',
                ]);
              }
            }
        }
    }

    /**
     * Монтажні роботи
     */
    public static function updateInstallations()
    {
        DB::connection('mysql')->table('installations')->truncate();
        $installations = DB::connection('mysql2')->table('installations')->get();

        foreach ($installations as $item) {
            $installation = new Installation();
            $installation->setConnection('mysql');
            $installation->id = $item->Id;
            $installation->user_id = $item->UserId;
            $installation->equipment_id = $item->EquipmentId;
            $installation->comment = $item->Comment;
            $installation->price = $item->Price;
            $installation->type = Installation::getType($item->Type);
            $installation->date_create = date('Y-m-d', strtotime($item->DateChange));
            $installation->save();
        }
    }

    /**
     * Девайси
     */
    public static function updateSensors()
    {
        DB::connection('mysql')->table('sensors')->truncate();
        $sensors = DB::connection('mysql2')->table('sensors')->get();

        foreach ($sensors as $item) {
            $sensor = new Sensor();
            $sensor->setConnection('mysql');
            $sensor->id = $item->Id;
            $sensor->name = $item->Name;
            $sensor->type = $item->Type;
            $sensor->sensor_id = $item->SensorId;
            $sensor->equipment_id = $item->EquipId;
            $sensor->save();
        }
    }
}
