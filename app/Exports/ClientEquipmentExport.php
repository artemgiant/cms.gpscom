<?php

namespace App\Exports;

use App\Models\ClientTariff;
use App\Models\Equipment;
use App\Models\Tariff;
use App\Services\SettingService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientEquipmentExport implements FromCollection, WithColumnWidths, WithStyles
{

    public $date;
    public $client_id;

    public function __construct($date, $client_id)
    {
        $this->date = $date;
        $this->client_id = $client_id;
    }

    public function collection()
    {
        $query = Equipment::query();
        $query->where('client_id', $this->client_id);
        $rows = $query->get();

        $equipment = collect();

        $totalPrice = 0;
        foreach ($rows as $row) {
            /** Кількість днів */
             $days = $row->getDaysInFilteredMonth($this->date);

            /** Ціна */
            $price = self::getPrice($rows, $row, $days, $this->date);

            $totalPrice += $price;

            if ($row->date_start && $row->date_end) {
                $date_end = date('Y.m', strtotime($row->date_end));

                if ($date_end == $this->date) {
                    $equipment->push([
                        'object' => $row->object,
                        'device' => $row->device,
                        'imei' => $row->imei,
                        'phone' => $row->phone,
                        'phone_2' => $row->phone2,
                        'date_start' => $row->date_start ? date('d.m.Y', strtotime($row->date_start)) : '',
                        'date_end' => $row->date_end ? date('d.m.Y', strtotime($row->date_end)) : '',
                        'days' =>  $days > 0 ? $days : '0',
                        'price' => $price > 0 ? $price : '0',
                    ]);
                } else {
                    $equipment->push([
                        'object' => $row->object,
                        'device' => $row->device,
                        'imei' => $row->imei,
                        'phone' => $row->phone,
                        'phone_2' => $row->phone2,
                        'date_start' => $row->date_start ? date('d.m.Y', strtotime($row->date_start)) : '',
                        'date_end' => $row->date_end ? date('d.m.Y', strtotime($row->date_end)) : '',
                        'days' => $days > 0 ? $days : '0',
                        'price' => $price > 0 ? $price : '0',
                    ]);
                }
            } else {
                $equipment->push([
                    'object' => $row->object,
                    'device' => $row->device,
                    'imei' => $row->imei,
                    'phone' => $row->phone,
                    'phone_2' => $row->phone2,
                    'date_start' => $row->date_start ? date('d.m.Y', strtotime($row->date_start)) : '',
                    'date_end' => $row->date_end ? date('d.m.Y', strtotime($row->date_end)) : '',
                    'days' => $days > 0 ? $days : '0',
                    'price' => $price > 0 ? $price : '0',
                ]);
            }
        }

        $equipment->prepend([
            'object' => 'Объект',
            'device' => 'Оборудование',
            'imei' => 'IMEI',
            'phone' => 'Номер устройства',
            'phone2' => 'Номер устройства 2',
            'date_start' => 'Дата подключения',
            'date_end' => 'Дата отключения',
            'days' => 'Дни',
            'price' => 'Стоимость',
        ]);

        $equipment[] = [
            'object' => '',
            'device' => '',
            'imei' => '',
            'phone' => '',
            'phone2' => '',
            'date_start' => '',
            'date_end' => '',
            'days' => 'Всего',
            'price' =>  $totalPrice > 0 ? $totalPrice : '0',
        ];

        return $equipment;
    }


    public function getPrice($rows, $row, $days, $filterDate)
    {
        $price = 0;
        $clientTariff = ClientTariff::where('client_id', $this->client_id)
            ->where('tariff_id', Tariff::FIXX)
            ->first();

        if(!$clientTariff){
            $clientTariff = ClientTariff::where('client_id', $this->client_id)
                ->where('tariff_id', $row->tariff_id)
                ->first();
        }

        if ($clientTariff) {

            if ($clientTariff->tariff_id == Tariff::FIXX) {
                $price = $clientTariff->price / count($rows);
            } else {
                $price += $row->getEquipmentWorkDays($clientTariff->price, $days, $filterDate);
            }
        } else {

            $price += $row->getPrice($filterDate);

        }

        return $price;
    }





    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'Z' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal('center');
    }


}
