<?php

namespace App\Exports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements FromCollection, WithColumnWidths, WithStyles
{
    public function collection()
    {
        $rows = Equipment::select(
            'equipments.object',
            'equipments.device',
            'equipments.imei',
            'equipments.phone',
            'equipments.phone2',
            'equipments.status',
            'equipments.date_start',
            'equipments.date_end',
            'equipments.tariff_id',
            'clients.name as client_name',
            'tariffs.title as tariff_name'
        )
            ->leftJoin('clients', 'equipments.client_id', '=', 'clients.id')
            ->leftJoin('operators', 'equipments.operator_id', '=', 'operators.id')
            ->leftJoin('tariffs', 'operators.id', '=', 'tariffs.operator_id')
            ->orderBy('equipments.date_start', 'desc')
            ->get();


        $items = $rows->map(function ($row) {
            return [
                'object' => $row->object,
                'device' => $row->device,
                'imei' => $row->imei,
                'phone' => $row->phone,
                'phone_2' => $row->phone2,
                'status' => $row->status == 1 ? 'Подключено' : 'Отключено',
                'date_start' => date('Y-m-d', strtotime($row->date_start)),
                'date_end' => isset($row->date_end) ? date('Y-m-d', strtotime($row->date_end)) : '',
                'tariff_id' => $row->tariff_name ?? 'без сим',
                'client_id' => $row->client_name ?? ''
            ];
        });

        $items->prepend([
            'object' => 'Объект',
            'device' => 'Устройство',
            'imei' => 'IMEI',
            'phone' => 'Номер устройства',
            'phone2' => 'Номер устройства2',
            'status' => 'Статус',
            'date_start' => 'Дата подключения',
            'date_end' => 'Дата отключения',
            'tariff_id' => 'Тариф',
            'client_id' => 'Владелец',
        ]);

        return $items;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal('center');
    }

    public function collection2()
    {
        $rows = Equipment::select(
            'object',
            'device',
            'imei',
            'phone',
            'phone2',
            'status',
            'date_start',
            'date_end',
            'tariff_id',
            'client_id',
            'operator_id'
        )->with([
            'operatorData.tariff',
            'client:id,name'
        ])->orderBy('date_start', 'desc')
            ->get();

        $items = collect();

        foreach ($rows as $row) {
            $items->push([
                'object' => $row->object,
                'device' => $row->device,
                'imei' => $row->imei,
                'phone' => $row->phone,
                'phone_2' => $row->phone2,
                'status' => $row->status == 1 ? 'Подключено' : 'Отключено',
                'date_start' => date('Y-m-d', strtotime($row->date_start)),
                'date_end' => isset($row->date_end) ? date('Y-m-d', strtotime($row->date_end)) : '',
                'tariff_id' => $row->getTariff() ?? '',
                'client_id' => $row->client->name ?? ''
            ]);
        }

        $items->prepend([
            'object' => 'Объект',
            'device' => 'Устройство',
            'imei' => 'IMEI',
            'phone' => 'Номер устройства',
            'phone2' => 'Номер устройства2',
            'status' => 'Статус',
            'date_start' => 'Дата подключения',
            'date_end' => 'Дата отключения',
            'tariff_id' => 'Тариф',
            'client_id' => 'Владелец',
        ]);

        return $items;
    }
}


