<?php

namespace App\Http\Livewire;

use App\Exports\OrderReportExport;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\ProductsExport;
use App\Models\MsBuyer;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OrderReportController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $buyer;
    public $buyer_id;
    public $filter;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::get();      
    }

    public function export()
    {
        return Excel::download(new OrderReportExport(
            $this->tglMasuk, 
            $this->tglKeluar, 
            $this->buyer_id,
            $this->filter,
        ), 'order_report.xlsx');
    }

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
