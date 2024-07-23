<?php

namespace App\Exports;

use App\Models\TdOrderLpk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LpkEntryImport implements ToModel, WithHeadingRow
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }

    public function model(array $row)
    {
        return new TdOrderLpk([
            'lpk_date' => $row['tg_proses'],
            'lpk_date' => $row['tg_lpk'],
            'lpk_no' => $row['nomor_lpk'],
            'order_id' => $row['po_number'],
            'machine_id' => $row['nomor_mesin'],
            'qty_lpk' => $row['jumlah_lpk'],
            'qty_gentan' => $row['jumlah_gentan'],
            'qty_gulung' => $row['meter_gulung'],
            'remark' => $row['note'],
        ]);
    }
}
