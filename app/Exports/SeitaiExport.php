<?php

namespace App\Exports;

use App\Models\MsProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SeitaiExport implements FromCollection, WithHeadings
{
    // use Exportable;
    protected $tglMasuk;
    protected $tglKeluar;

    public function __construct($tglMasuk, $tglKeluar)
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
    }

    public function collection()
    {
        // if($this->filter == 2){
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tdpg.created_on >= '" . $this->tglMasuk . "'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tdpg.created_on <= '" . $this->tglKeluar . "'";
            }
        // } else {
        //     $tglMasuk = '';
        //     if (isset($this->tglMasuk) && $this->tglMasuk != '') {
        //         $tglMasuk = "WHERE tod.processdate >= '" . $this->tglMasuk . "'";
        //     }
        //     $tglKeluar = '';
        //     if (isset($this->tglKeluar) && $this->tglKeluar != '') {
        //         $tglKeluar = "AND tod.processdate <= '" . $this->tglKeluar . "'";
        //     }
        // }
        // $noprosesawal = '';
        // if (isset($this->noprosesawal) && $this->noprosesawal != '') {
        //     $noprosesawal = "AND tdpa.seq_no >= '" . $this->noprosesawal . "'";
        // }
        // $noprosesakhir = '';
        // if (isset($this->noprosesakhir) && $this->noprosesakhir != '') {
        //     $noprosesakhir = "AND tdpa.seq_no <= '" . $this->noprosesakhir . "'";
        // }
        // $lpk_no = '';
        // if (isset($this->lpk_no) && $this->lpk_no != '') {
        //     $lpk_no = "AND tdol.lpk_no = '" . $this->lpk_no . "'";
        // }
        // $code = '';
        // if (isset($this->code) && $this->code != '') {
        //     $code = "AND tdol.lpk_no = '" . $this->code . "'";
        // }

        return collect(DB::select("
            SELECT
                tdpg.production_date,
                tdpg.seq_no,
                tdpg.created_on,
                msw.work_shift,
                mse.nik,
                mse.empname,
                msm.machineno,
                tdol.lpk_no,
                msp.name,
                tdpg.qty_produksi,
                mse2.nik,
                mse2.empname,
                tdpg.nomor_palet,
                tdpg.nomor_lot
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN tdorderlpk AS tdol ON tdpg.lpk_id = tdol.id
                INNER JOIN msworkingshift AS msw ON msw.id = tdpg.work_shift 
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id 
                INNER JOIN msproduct AS msp ON msp.id = tdpg.product_id 
                LEFT JOIN msemployee AS mse ON mse.id = tdpg.employee_id 
                LEFT JOIN msemployee AS mse2 ON mse2.id = tdpg.employee_id_infure 
                $tglMasuk
                $tglKeluar
                AND tdpg.seq_no >= 1 
                AND tdpg.seq_no <= 1000
        "));
    }

    public function headings(): array
    {
        return [
            'Tanggal Proses', 'No Proses', 'Tanggal Produksi', 'Shift', 'NIK', 
            'Petugas', 'Nomor Mesin', 'Nomor LPK', 'Nama Produk', 
            'Quantity (Lembar)', 'Loss Infure', 'NIK', 'Nomor Palet', 'Nomor LOT'
        ];
    }
}

