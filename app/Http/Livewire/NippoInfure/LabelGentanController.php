<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LabelGentanController extends Component
{
    public $lpk_no;
    public $code;
    public $product_name;
    public $product_panjang;
    public $qty_gentan;
    public $product_panjanggulung;
    public $lpk_date;
    public $qty_lpk;

    public function print()
    {
        $data = collect(DB::select("
        SELECT
            tdol.lpk_no,
            msp.name,
            msp.code,
            msp.product_type_code,
            to_char(tdpa.production_date, 'YYYY-MM-DD') AS production_date,
            tdpa.work_hour,
            tdpa.work_shift,
            msm.machineno,
            tdpa.berat_produksi,
            tdpa.panjang_produksi,
            tdpa.nomor_han,
            mse.nik,
            mse.empname
        FROM
            tdproduct_assembly AS tdpa
            INNER JOIN tdorderlpk AS tdol ON tdpa.lpk_id = tdol.ID
            INNER JOIN msproduct as msp on msp.id = tdol.product_id
            LEFT JOIN msworkingshift as msw on msw.id = tdpa.work_shift
            INNER JOIN msmachine as msm on msm.id = tdpa.machine_id
            INNER JOIN msemployee as mse on mse.id = tdpa.employee_id
        WHERE
            tdol.lpk_no = '$this->lpk_no'
        "))->first();

        $this->emit('redirectToPrint', $data);
    }

    public function render()
    {
        if(isset($this->lpk_no) && $this->lpk_no != ''){
            $data = DB::table('tdorderlpk as tod')
            ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->select(
                'tod.lpk_no',
                'mp.code',
                'mp.name as product_name',
                'tod.product_panjang',
                'tod.qty_gentan',
                'tod.product_panjanggulung',
                'tod.qty_lpk',
                'tod.lpk_date',
                'tod.reprint_no as reprint_no'
            )
            ->where('lpk_no', $this->lpk_no)
            ->first();
            if($data == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_no = $data->lpk_no;
                $this->code = $data->code;
                $this->product_name = $data->product_name;
                $this->product_panjang = $data->product_panjang;
                $this->qty_gentan = $data->qty_gentan;
                $this->product_panjanggulung = $data->product_panjanggulung;
                $this->qty_lpk = $data->qty_lpk;
                $this->lpk_date = Carbon::parse($data->lpk_date)->format('Y-m-d');
            }
        }

        return view('livewire.nippo-infure.label-gentan')->extends('layouts.master');
    }
}
