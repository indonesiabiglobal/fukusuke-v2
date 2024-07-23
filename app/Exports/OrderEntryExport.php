<?php

namespace App\Exports;

use App\Models\TdOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderEntryExport implements FromCollection, WithHeadings
{
    // public function __construct()
    // {

    // }

    public function collection()
    {
        return new Collection([
            [
                'TG_PROSES' => 15032018, 
                'PO_NUMBER' => 'PO001', 
                'TG_ORDER' => '14032018',
                'NO_ORDER' => 'UH244R1',
                'QTY_ORDER' => '12',
                'UNIT' => '1',
                'TG_STUFING' => '16032018',
                'TG_ETD' => '16032018',
                'TG_ETA' => '16032018',
                'KODE_BUYER' => '1001',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'TG_PROSES','PO_NUMBER','TG_ORDER','NO_ORDER','QTY_ORDER','UNIT','TG_STUFING','TG_ETD',
            'TG_ETA','KODE_BUYER'
        ];
    }
}

