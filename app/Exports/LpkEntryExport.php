<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class LpkEntryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return new Collection([
            [
                'TG_PROSES' => '05102018', 
                'TG_LPK' => '05102018', 
                'Nomor_LPK' => '181005-001',
                'PO_NUMBER' => 'N17JSZ21osa-1',
                'Nomor_Mesin' => '00I07',
                'Jumlah_LPK' => '468000',
                'Jumlah_Gentan' => '18',
                'Meter_Gulung' => '9360',
                'Note' => 'Desain Case baru',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'TG_PROSES','TG_LPK','Nomor_LPK','PO_NUMBER','Nomor_Mesin','Jumlah_LPK','Jumlah_Gentan','Meter_Gulung',
            'Note'
        ];
    }
}
