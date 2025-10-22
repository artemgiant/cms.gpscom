<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Installation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportingExportInstalation implements FromCollection, WithColumnWidths, WithStyles
{
    public function collection()
    {
        $params = request()->all();
        $cols = Client::getCols($params);

        $query = Installation::query();
        $query->select('installations.*', 'clients.*');
        $query->leftJoin('clients', 'clients.id', 'installations.user_id');

        $mount = request()->get('mount');
        if ($mount) {
            $data = explode('.', $mount);
            $year = (int)$data[0];
            $mount = (int)$data[1];

            $query->whereMonth('date_create', $mount);
            $query->whereYear('date_create', $year);
        }

        $rows = $query->paginate();

        $installations = collect();

        $total = 0;
        foreach ($rows as $row) {
            $price = $row->price;
            if ($price > 0) {
                $total += $price;

                foreach ($cols as $name => $val) {
                    $rowProperty[$name] = Client::getCol($name, $row);
                }

                $rowProperty['equipments'] = $row->equipment->imei;
                $rowProperty['date_create'] = date('d.m.Y', strtotime($row->date_create));
                $rowProperty['type'] = $row->type;
                $rowProperty['comment'] = $row->comment;
                $rowProperty['price'] = $price ?? '0';

                $installations->push($rowProperty);
            }
        }

        $prepend = $cols;
        $prepend['equipments'] = 'Оборудование';
        $prepend['date_create'] = 'Дата работы';
        $prepend['type'] = 'Тип работы';
        $prepend['comment'] = 'Комментарий';
        $prepend['price'] = 'Стоимость';
        $installations->prepend($prepend);

        foreach ($rowProperty as $name => $val) {
            $totalRow[$name] = '';
        }
        $totalRow['article'] = '';
        $totalRow['equipments'] = '';
        $totalRow['date_create'] = '';
        $totalRow['type'] = '';
        $totalRow['comment'] = '';
        $totalRow['price'] = 'Итого:' . $total;

        $installations[] = $totalRow;

        return $installations;
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
