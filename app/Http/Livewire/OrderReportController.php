<?php

namespace App\Http\Livewire;

use App\Exports\OrderReportExport;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\ProductsExport;
use App\Models\MsBuyer;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $buyer;
    public $workingShiftHour;
    public $buyer_id;
    public $filter;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::get();
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function export()
    {
        return Excel::download(new OrderReportExport(
            $this->tglAwal,
            $this->tglAkhir,
            $this->buyer_id,
            $this->filter,
        ), 'order_report.xlsx');
    }

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
