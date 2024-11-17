<?php

namespace App\Exports;

use App\Models\TdOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrderEntryExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    // public function __construct()
    // {

    // }

    public function collection()
    {
        return new Collection([
            [
                'TG_PROSES' => Carbon::now()->format('d/m/Y'),
                'PO_NUMBER' => 'PO001',
                'TG_ORDER' => Carbon::now()->format('d/m/Y'),
                'NO_ORDER' => '(XX)UHS76R1',
                'QTY_ORDER' => '12',
                'UNIT' => '1',
                'TG_STUFING' => Carbon::now()->format('d/m/Y'),
                'TG_ETD' => Carbon::now()->format('d/m/Y'),
                'TG_ETA' => Carbon::now()->format('d/m/Y'),
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

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}

