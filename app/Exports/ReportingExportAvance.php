<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportingExportAvance implements FromCollection, WithColumnWidths, WithStyles
{
    public function collection()
    {
        $params = request()->all();
        $cols = Client::getCols($params);

        $query = Client::query();
        $query->select('clients.*');
        $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
        $query->groupBy('clients.id');

        $client_type = request()->get('client_type');
        if ($client_type) {
            $query->where('clients.client_type', $client_type);
        }

        $rows = $query->get();
        $clients = collect();

        $total = 0;
        foreach ($rows as $row) {
            $price = $row->getPerMountPrice();
            if ($price > 0) {
                $total += $price;

                foreach ($cols as $name => $val) {
                    $rowProperty[$name] = Client::getCol($name, $row);
                }
                $rowProperty['count_end'] = $row->getReportingEquipment();
                $rowProperty['price'] = $price ?? '0';

                $clients->push($rowProperty);
            }
        }

        $prepend = $cols;
        $prepend['count_end'] = 'Конечное кол-во';
        $prepend['price'] = 'Стоимость';

        $clients->prepend($prepend);

        foreach ($rowProperty as $name => $val) {
            $totalRow[$name] = '';
        }
        $totalRow['price'] = 'Итого:' . $total;

        $clients[] = $totalRow;

        return $clients;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 30,
            'I' => 30,
            'J' => 30,
            'K' => 30,
            'L' => 30,
            'M' => 30,
            'N' => 30,
            'O' => 30,
            'Z' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal('center');
    }
}
