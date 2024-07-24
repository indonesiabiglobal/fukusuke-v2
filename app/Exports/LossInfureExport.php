<?php

namespace App\Exports;

use App\Models\MsProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class LossInfureExport implements FromCollection, WithHeadings
{
    // use Exportable;
    protected $tglMasuk;
    protected $tglKeluar;
    protected $noprosesawal;
    protected $noprosesakhir;
    protected $lpk_no;
    protected $code;

    public function __construct($tglMasuk, $tglKeluar, $noprosesawal, $noprosesakhir, $lpk_no, $code)
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
        $this->noprosesawal = $noprosesawal;
        $this->noprosesakhir = $noprosesakhir;
        $this->lpk_no = $lpk_no;
        $this->code = $code;
    }

    public function collection()
    {
        // if($this->filter == 2){
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = $this->tglMasuk . " 00:00:00";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = $this->tglKeluar . " 23:59:59";
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
        $noprosesawal = '';
        if (isset($this->noprosesawal) && $this->noprosesawal != '') {
            $noprosesawal = "AND tdpa.seq_no >= '" . $this->noprosesawal . "'";
        }
        $noprosesakhir = '';
        if (isset($this->noprosesakhir) && $this->noprosesakhir != '') {
            $noprosesakhir = "AND tdpa.seq_no <= '" . $this->noprosesakhir . "'";
        }
        $lpk_no = '';
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            $lpk_no = "AND tdol.lpk_no = '" . $this->lpk_no . "'";
        }
        // $code = '';
        // if (isset($this->code) && $this->code != '') {
        //     $code = "AND tdol.lpk_no = '" . $this->code . "'";
        // }

        return collect(DB::select("
            SELECT 
                tdol.lpk_date AS lpk_date,
                tdpa.gentan_no,
                tdpa.production_date AS production_date,
                tdpa.work_shift AS work_shift,
                tdol.lpk_no AS lpk_no, 
                msp.name,
                msp.code,
                msm.machineno,
                mse.empname,
                mse.nik,
                mli.name as loss,
                mli.code as code_loss, 
                tdpal.berat_loss
            FROM tdproduct_assembly AS tdpa
            INNER JOIN tdorderlpk AS tdol ON tdpa.lpk_id = tdol.id
            INNER JOIN msproduct AS msp ON tdpa.product_id = msp.id
            LEFT JOIN tdproduct_assembly_loss AS tdpal ON tdpa.id = tdpal.product_assembly_id
            LEFT JOIN mslossinfure AS mli ON tdpal.loss_infure_id = mli.id
            INNER JOIN msmachine AS msm ON msm.id = tdpa.machine_id 
            LEFT JOIN msdepartment AS msd ON msd.id = msm.department_id
            LEFT JOIN msemployee AS mse ON mse.id = tdpa.employee_id
            WHERE tdpa.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
                -- AND tdpa.seq_no BETWEEN $noprosesawal AND $noprosesakhir
                -- AND tdpa.machine_id = 1
                -- AND msm.department_id =1;
        "));
    }

    public function headings(): array
    {
        return [
            'Tanggal Proses', 'No Proses', 'Tanggal Produksi', 'Shift', 'Nomor LPK', 'Nama Produk', 'Nomor Order', 'Nomor Mesin',
            'Petugas', 'NIK', 'Nama Loss', 'Kode Loss', 'Berat Loss'
        ];
    }
}

