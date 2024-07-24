<?php

namespace App\Http\Livewire\NippoInfure;

use App\Exports\LossInfureExport;
use App\Exports\NippoInfureExport;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use Livewire\Component;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CheckListInfureController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $machine;
    public $noprosesawal;
    public $noprosesakhir;
    public $lpk_no;
    public $code;
    public $department;
    public $jenisReport;
    public $departemenId;
    public $machineId;
    public $nomor_han;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('d/m/Y');
        $this->tglKeluar = Carbon::now()->format('d/m/Y');
        $this->machine = MsMachine::get();
        $this->department = MsDepartment::get();      
    }

    public function export()
    {
        if($this->jenisReport == 2){
            return Excel::download(new LossInfureExport(
                $this->tglMasuk, 
                $this->tglKeluar,
                $this->noprosesawal,
                $this->noprosesakhir,
                $this->lpk_no,
                $this->code,
            ), 'LossInfure-Checklist.xlsx');
        } else {
            return Excel::download(new NippoInfureExport(
                $this->tglMasuk, 
                $this->tglKeluar,
                $this->noprosesawal,
                $this->noprosesakhir,
                $this->lpk_no,
                $this->code,
                $this->departemenId,
                $this->machineId,
                $this->nomor_han,
            ), 'NippoInfure-Checklist.xlsx');
        }
    }

    public function render()
    {
        return view('livewire.nippo-infure.check-list')->extends('layouts.master');
    }
}
