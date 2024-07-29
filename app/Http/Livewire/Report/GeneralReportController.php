<?php

namespace App\Http\Livewire\Report;

use App\Exports\GeneralReportExport;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class GeneralReportController extends Component
{
    public $jenisreport;
    public $tglMasuk;
    public $tglKeluar;
    public $nipon='1';


    public function mount()
    {
       $this->tglMasuk = Carbon::now()->format('Y-m-d') . ' 00:00';
        $this->tglKeluar = Carbon::now()->format('Y-m-d') . ' 23:59';      
    }

    public function export()
    {
        // dd($this->jenisreport);
        switch($this->jenisreport) {
            case '1':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin.xlsx');
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin.xlsx');

                }
               
                break;

            case '2':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin dan Type.xlsx');
                } else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin dan Type.xlsx');
                }
                
                break;

            case '3':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Jenis.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Jenis.xlsx'); 
                }
                              
                break;
            case '4':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Tipe.xlsx');
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Tipe.xlsx');
                }
                  
                break;   
            case '5':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Produk.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Produk.xlsx'); 
                }
                 
                break;
            case '6':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen Per Jenis.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen Per Jenis.xlsx'); 
                }
               
                break;         
            case '7':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Tipe.xlsx');
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Tipe.xlsx');
                }
                 
                break; 
            case '8':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Petugas.xlsx');
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Petugas.xlsx');    
                }
                
                break; 
            case '9':
                // dd($this->nipon);
                if ($this->nipon==2){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Palet.xlsx');
                }
                
                break;    
            case '10':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen.xlsx'); 
                }
                
                break;
            case '11':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen & Jenis.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen & Jenis.xlsx'); 
                }
                 
                 break;
             case '12':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Petugas.xlsx'); 
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Petugas.xlsx'); 
                }
                 
                break;            
            case '13':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Mesin.xlsx');
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Mesin.xlsx');
                }
                   
                break;            
            case '14':
                if ($this->nipon==1){
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Kapasitas Produksi.xlsx');   
                }else{
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk, 
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Kapasitas Produksi.xlsx');   
                }
                
                    break;
            default:
            // dd('ini percobaan');
            session()->flash('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
                
        }
    }

    public function render()
    {
        return view('livewire.report.general-report')->extends('layouts.master');
    }
}
