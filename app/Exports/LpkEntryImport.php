<?php

namespace App\Exports;

use App\Models\MsMachine;
use App\Models\TdOrderLpk;
use App\Models\TdOrders;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
            // Pastikan baris tidak kosong
            if (empty(array_filter($row))) {
                return null; // Lewati baris kosong
            }

            // mengambil data order berdasarkan po number
            $order = TdOrders::where('po_no', $row['po_number'])->first();
            if (!$order) {
                throw new \Exception('Order dengan nomor PO ' . $row['po_number'] . ' tidak ditemukan');
            }

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

            // mengecek format tanggal LPK
            if (is_numeric($row['tg_lpk'])) {
                $tanggalLPK = Date::excelToDateTimeObject($row['tg_lpk']);
            } else {
                $tanggalLPK = $row['tg_lpk'];
            }

            // mengecek format tanggal proses
            if (is_numeric($row['tg_proses'])) {
                $tanggalProses = Date::excelToDateTimeObject($row['tg_proses']);
            } else {
                $tanggalProses = $row['tg_proses'];
            }

            // simpan data ke dalam tabel td_order_lpk
            return new TdOrderLpk([
                'lpk_no' => $row['nomor_lpk'],
                'lpk_date' => $tanggalLPK,
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
                'created_on' => $tanggalProses,
                'created_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
