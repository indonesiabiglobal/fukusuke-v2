<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Exports\SeitaiExport;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use Livewire\Component;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CheckListSeitaiController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $machine;
    public $noprosesawal;
    public $noprosesakhir;
    public $lpk_no;
    public $code;
    public $department;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        $this->machine = MsMachine::where('machineno',  'LIKE', '00S%')->get();
        $this->department = MsDepartment::where('division_code', 20)->get();
    }

    public function export()
    {
        // return Excel::download(new SeitaiExport(
        //     $this->tglMasuk,
        //     $this->tglKeluar,
        // ), 'checklist-infure.xlsx');
        $tglMasuk = $this->tglMasuk;
        $tglKeluar = $this->tglKeluar;

        $this->dispatch('redirectToPrint', "'$tglMasuk 00:00' and tdpa.created_on <= '$tglKeluar 23:59'");
    }

    public function render()
    {
        return view('livewire.nippo-seitai.check-list-seitai')->extends('layouts.master');
    }
}
