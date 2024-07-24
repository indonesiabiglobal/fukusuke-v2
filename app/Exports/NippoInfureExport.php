<?php

namespace App\Exports;

use App\Models\MsProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class NippoInfureExport implements FromCollection, WithHeadings
{
    // use Exportable;
    protected $tglMasuk;
    protected $tglKeluar;
    protected $noprosesawal;
    protected $noprosesakhir;
    protected $lpk_no;
    protected $code;
    protected $departemenId;
    protected $machineId;
    protected $nomor_han;

    public function __construct($tglMasuk, $tglKeluar, $noprosesawal, $noprosesakhir, $lpk_no, $code, $departemenId, $machineId, $nomor_han)
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
        $this->noprosesawal = $noprosesawal;
        $this->noprosesakhir = $noprosesakhir;
        $this->lpk_no = $lpk_no;
        $this->code = $code;
        $this->departemenId = $departemenId;
        $this->machineId = $machineId;
        $this->nomor_han = $nomor_han;
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
                tdpa.production_date,
                tdpa.seq_no,
                tdpa.production_date,
                tdpa.work_shift, 
                tdpa.work_hour,
                mse.nik, 
                mse.empname,
                msm.machineno,
                tdol.lpk_no,
                msp.code,
                msp.name,
                tdpa.gentan_no,
                tdpa.nomor_han,
                tdpa.panjang_produksi,
                tdpa.berat_standard,
                tdpa.berat_produksi,
                tdol.total_assembly_line AS total_assembly_line, 
                tdol.total_assembly_qty AS total_assembly_qty                
            FROM tdproduct_assembly AS tdpa
            INNER JOIN tdorderlpk AS tdol ON tdpa.lpk_id = tdol.id
            INNER JOIN msproduct AS msp ON tdpa.product_id = msp.id
            INNER JOIN msmachine AS msm ON msm.id = tdpa.machine_id 
            LEFT JOIN msdepartment AS msd ON msd.id = msm.department_id
            LEFT JOIN msemployee AS mse ON mse.id = tdpa.employee_id
            WHERE tdpa.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
                -- AND tdpa.machine_id = 1
                -- AND tdpa.seq_no BETWEEN $noprosesawal AND $noprosesakhir;
        "));
    }

    public function headings(): array
    {
        return [
            'Tanggal Proses', 'No', 'Tanggal Produksi', 'Shift', 'Jam', 'NIK', 'Petugas', 'Nomor Mesin', 'Nomor LPK', 'Nomor Order',
            'Nama Produk', 'Nomor Gentan', 'Nomor Hand', 'Panjang Infure (meter)', 'Berat Standard (kg)', 'Berat Produksi (kg)',
            'Nama Loss', 'Berat Loss (kg)' 
        ];
    }
}

