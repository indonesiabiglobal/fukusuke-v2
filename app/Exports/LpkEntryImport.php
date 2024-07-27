<?php

namespace App\Exports;

use App\Models\MsMachine;
use App\Models\TdOrderLpk;
use App\Models\TdOrders;
use Carbon\Carbon;
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
        // mengubah format tanggal excel ke format tanggal yang bisa dibaca oleh laravel
        $row['tg_lpk'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_lpk']);
        $row['tg_proses'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_proses']);

        // mengambil data order berdasarkan po number
        $order = TdOrders::where('po_no', $row['po_number'])->first();

        // mengambil data mesin berdasarkan nomor mesin
        $machine = MsMachine::where('machineno', $row['nomor_mesin'])->first();

        // simpan data ke dalam tabel td_order_lpk
        return new TdOrderLpk([
            'lpk_date' => $row['tg_proses'],
            'lpk_date' => $row['tg_lpk'],
            'lpk_no' => $row['nomor_lpk'],
            'order_id' => $order->id,
            'machine_id' => $machine->id,
            'qty_lpk' => $row['jumlah_lpk'],
            'qty_gentan' => $row['jumlah_gentan'],
            'qty_gulung' => $row['meter_gulung'],
            'remark' => $row['note'],
            'created_on' => Carbon::now(),
            'created_by' => auth()->user()->username,
            'updated_on' => Carbon::now(),
            'updated_by' => auth()->user()->username,
        ]);
    }
}
