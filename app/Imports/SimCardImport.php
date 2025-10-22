<?php

namespace App\Imports;

use App\Models\SimCard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class SimCardImport implements ToCollection
{
    public $operator;
    public $file;

    public function __construct($operator)
    {
        $this->operator = $operator;
    }

    public function collection(Collection $collection)
    {
        try {
            foreach ($collection as $item) {

                if (isset($item[0])) {
                    $phone = $item[0];
                } elseif (isset($item[1])) {
                    $phone = $item[1];
                }

                if (isset($phone)) {
                    $simCard = SimCard::where('phone', $phone)->first();
                    if (!$simCard) {
                        $newSimCard = new SimCard();
                        $newSimCard->phone = $newSimCard->changePhone($phone);
                        $newSimCard->status = false;
                        $newSimCard->operator = $this->operator;
                        $newSimCard->save();
                    }
                }
            }

        } catch (\Exception $e) {
            return 1;
        }
    }
}
