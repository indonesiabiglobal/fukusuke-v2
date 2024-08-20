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
        try {
            // mengubah format tanggal excel ke format tanggal yang bisa dibaca oleh laravel
            // $row['tg_lpk'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_lpk']);
            // $row['tg_proses'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_proses']);

            // mengambil data order berdasarkan po number
            $order = TdOrders::where('po_no', $row['po_number'])->first();

            // mengambil data mesin berdasarkan nomor mesin
            $machine = MsMachine::where('machineno', $row['nomor_mesin'])->first();

            // mengecek apakah lpk sudah ada
            $lpk = TdOrderLpk::where('lpk_no', $row['nomor_lpk'])->first();
            if ($lpk) {
                throw new \Exception('LPK dengan nomor ' . $row['nomor_lpk'] . ' sudah ada');
            }

            // perhitungan
            $total_assembly_line = (int)str_replace(',', '', $row['jumlah_lpk']) * ((int)str_replace(',', '', $order->productlength) / 1000);
            $qty_gentan = $row['jumlah_gentan'];
            $qty_gulung = $row['meter_gulung'];
            $panjang_lpk = (int)str_replace(',', '', $qty_gentan) * (int)str_replace(',', '', (int)$qty_gulung);

            $lastSeq = TdOrderLpk::whereDate('lpk_date', Carbon::today())
                ->orderBy('seq_no', 'desc')
                ->first();

            $seqno = 1;
            if (!empty($lastSeq)) {
                $seqno = $lastSeq->seq_no + 1;
            }

            // simpan data ke dalam tabel td_order_lpk
            return new TdOrderLpk([
                'lpk_no' => $row['nomor_lpk'],
                'lpk_date' => $row['tg_lpk'],
                'order_id' => $order->id,
                'product_id' => $order->product_id,
                'machine_id' => $machine->id,
                'qty_lpk' => $row['jumlah_lpk'],
                'remark' => $row['note'],
                'qty_gentan' => $qty_gentan,
                // 'qty_gentan' => $row['jumlah_gentan'],
                'panjang_lpk' => $panjang_lpk,
                'total_assembly_line' => $total_assembly_line,
                'seq_no' => $seqno,
                'qty_gulung' => $qty_gulung,
                // 'qty_gulung' => $row['meter_gulung'],
                'created_on' => $row['tg_proses'],
                'created_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
