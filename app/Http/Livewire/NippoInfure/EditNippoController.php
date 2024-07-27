<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsLossInfure;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\TdProductAssemblyLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditNippoController extends Component
{
    public $orderId;
    public $production_no;
    public $production_date;
    public $created_on;
    public $lpk_no;
    public $lpk_date;
    public $panjang_lpk;
    public $dimensiinfure;
    public $code;
    public $name;
    public $machineno;
    public $machinename;
    public $empname;
    public $employeeno;
    public $qty_gulung;
    public $qty_gentan;
    public $work_hour;
    public $work_shift;
    public $gentan_no;
    public $nomor_han;
    public $nomor_barcode;
    public $details = [];
    public $orderid;
    public $panjang_produksi;
    public $loss_infure_id;
    public $name_infure;

    public function mount(Request $request)
    {
        $data = DB::table('tdproduct_assembly AS tda')
        ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
        ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
        ->join('msemployee AS mse', 'mse.id', '=', 'tda.employee_id')
        ->join('msproduct AS msp', 'msp.id', '=', 'tda.product_id')
        ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id')
        ->select(
            'tda.id AS id',
            'tda.production_no AS production_no',
            'tda.production_date AS production_date',
            'tda.employee_id AS employee_id',
            'tda.work_shift AS work_shift',
            'tda.work_hour AS work_hour',
            'tda.machine_id AS machine_id',
            'tda.lpk_id AS lpk_id',
            'tda.product_id AS product_id',
            'tda.panjang_produksi AS panjang_produksi',
            'tda.panjang_printing_inline AS panjang_printing_inline',
            'tda.berat_standard AS berat_standard',
            'tda.berat_produksi AS berat_produksi',
            'tda.nomor_han AS nomor_han',
            'tda.gentan_no AS gentan_no',
            'tda.seq_no AS seq_no',
            'tda.status_production AS status_production',
            'tda.status_kenpin AS status_kenpin',
            'tda.infure_cost AS infure_cost',
            'tda.infure_cost_printing AS infure_cost_printing',
            'tda.infure_berat_loss AS infure_berat_loss',
            'tda.kenpin_berat_loss AS kenpin_berat_loss',
            'tda.kenpin_meter_loss AS kenpin_meter_loss',
            'tda.kenpin_meter_loss_proses AS kenpin_meter_loss_proses',
            'tda.created_by AS created_by',
            'tda.created_on AS created_on',
            'tda.updated_by AS updated_by',
            'tda.updated_on AS updated_on',
            'tdol.order_id AS order_id',
            'tdol.lpk_no AS lpk_no',
            'tdol.lpk_date AS lpk_date',
            'tdol.panjang_lpk AS panjang_lpk',
            'tdol.qty_gentan AS qty_gentan',
            'tdol.qty_gulung AS qty_gulung',
            'tdol.qty_lpk AS qty_lpk',
            'tdol.total_assembly_line AS total_assembly_line',
            'tdol.total_assembly_qty AS total_assembly_qty',
            'msm.machineno',
            'msm.machinename',
            'tdo.product_code',
            'mse.employeeno',
            'mse.empname',
            'msp.code',
            'msp.name'
        )
        ->where('tda.id', $request->query('orderId'))
        ->first();
        // dd($orderId);

        $this->orderId = $request->query('orderId');
        $this->production_no = $data->production_no;
        $this->production_date = Carbon::parse($data->production_date)->format('Y-m-d');
        $this->created_on = Carbon::parse($data->created_on)->format('Y-m-d');
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = Carbon::parse($data->lpk_date)->format('Y-m-d');
        $this->panjang_lpk = $data->panjang_lpk;
        $this->machineno = $data->machineno;
        $this->machinename = $data->machinename;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->work_hour = $data->work_hour;
        $this->work_shift = $data->work_shift;
        $this->gentan_no = $data->gentan_no;
        $this->nomor_han = $data->nomor_han;
        $this->panjang_produksi = $data->panjang_produksi;
    }

    public function save()
    {
        $validatedData = $this->validate([
            'production_date' => 'required',
            'created_on' => 'required',
            'lpk_no' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            'nomor_barcode' => 'required'
        ]);

        DB::beginTransaction();
        try {

            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $products = MsProduct::where('code', $this->code)->first();

            $product = TdProductAssembly::findOrFail($this->orderId);
            $product->production_date = $this->production_date;
            $product->created_on = $this->created_on;
            $product->machine_id = $machine->id;
            $product->employee_id = $employe->id;
            $product->work_shift = $this->work_shift;
            $product->work_hour = $this->work_hour;
            $product->lpk_id = $lpkid->id;
            $product->gentan_no = $this->gentan_no;
            $product->nomor_han = $this->nomor_han;
            $product->product_id = $products->id;
            $product->panjang_produksi = $this->panjang_produksi;

            $totalAssembly = DB::select("
                SELECT
                    CASE WHEN x.A1 IS NULL THEN 0 ELSE x.A1 END AS C1
                FROM
                    (
                    SELECT SUM(panjang_produksi) AS A1 
                    FROM
                        tdproduct_assembly AS ta 
                    WHERE
                        lpk_id = $lpkid->id 
                ) AS x
            ");

            // $product->panjang_printing_inline = $this->panjang_printing_inline;
            // $product->berat_standard = $this->berat_standard;
            // $product->berat_produksi = $this->berat_produksi;            
            // $product->status_production = $this->status_production;
            // $product->status_kenpin = $this->status_kenpin;
            // $product->infure_cost = $this->infure_cost;
            // $product->product_id = $this->product_id;
            $product->save();

            TdProductAssemblyLoss::where('lpk_id',$lpkid->id)->update([
                'product_assembly_id' => $product->id,
            ]);    
            
            TdOrderLpk::where('id',$lpkid->id)->update([
                'total_assembly_line' => $totalAssembly[0]->c1,
            ]);

            DB::commit();
            session()->flash('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('nippo-infure');
        } catch (\Exception $e) {            
            DB::rollBack();
            $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }      
    }

    public function addLossInfure()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            // 'panjang_produksi' => 'required',
            // 'qty_gentan' => 'required'
        ]);

        if ($validatedData) {
            $this->emit('showModal');
        }
    }

    public function saveInfure()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();

        $datas = new TdProductAssemblyLoss();
        $datas->loss_infure_id = $this->loss_infure_id;
        $datas->berat_loss = $this->berat_loss;
        $datas->lpk_id = $lpkid->id;
        
        $datas->save();

        $this->emit('closeModal');
    }

    public function deleteInfure($orderId)
    {
        $data = TdProductAssemblyLoss::findOrFail($orderId);
        $data->delete();

        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function cancel()
    {
        return redirect()->route('nippo-infure');
    }

    public function render()
    {
        if(isset($this->lpk_no) && $this->lpk_no != ''){
            $tdorderlpk = DB::table('tdorderlpk as tolp')
            ->select(
                'tolp.id',
                'tolp.lpk_date',
                'tolp.panjang_lpk',                
                'tolp.created_on',
                'mp.code',
                'mp.name',
                'mp.ketebalan',
                'mp.diameterlipat',
                'tolp.qty_gulung',
                'tolp.qty_gentan'
            )
            ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
            ->where('tolp.lpk_no', $this->lpk_no)
            ->first();

            if($tdorderlpk == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_date = Carbon::parse($tdorderlpk->lpk_date)->format('Y-m-d');
                $this->panjang_lpk = $tdorderlpk->panjang_lpk;
                $this->created_on = Carbon::parse($tdorderlpk->created_on)->format('Y-m-d');
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->dimensiinfure = $tdorderlpk->ketebalan.'x'.$tdorderlpk->diameterlipat;
                $this->qty_gulung = $tdorderlpk->qty_gulung;
                $this->qty_gentan = $tdorderlpk->qty_gentan;

                $this->details = DB::table('tdproduct_assembly_loss as tal')
                ->select(
                    'tal.loss_infure_id',
                    'tal.berat_loss',
                    'tal.id',
                    'msi.name as name_infure'
                )
                ->join('mslossinfure as msi', 'msi.id', '=', 'tal.loss_infure_id')
                ->where('tal.lpk_id', $tdorderlpk->id)
                ->get();
            }
        }

        if(isset($this->loss_infure_id) && $this->loss_infure_id != ''){
            $lossinfure=MsLossInfure::where('id', $this->loss_infure_id)->first();

            if($lossinfure == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->loss_infure_id . ' Tidak Terdaftar']);
            } else {
                $this->name_infure = $lossinfure->name;
            }
        }

        if(isset($this->machineno) && $this->machineno != ''){
            $machine=MsMachine::where('machineno', $this->machineno)->first();

            if($machine == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machinename = $machine->machinename;
            }
        }

        if(isset($this->employeeno) && $this->employeeno != ''){
            $msemployee=MsEmployee::where('employeeno', $this->employeeno)->first();

            if($msemployee == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
        }

        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();

        $this->gentan_no = 1;
        if (!empty($lpkid)) {
            $lastGentan = TdProductAssembly::where('lpk_id', $lpkid->lpk_id)
                ->max('gentan_no');

            $nogentan = 1;
            if(!empty($lastGentan)){
                $nogentan = $lastGentan->seq_no + 1;
            }
            $this->gentan_no=$nogentan;
        }

        if(isset($this->nomor_barcode) && $this->nomor_barcode != ''){
            if($this->code != $this->nomor_barcode){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Terdaftar']);
            }
        }

        return view('livewire.nippo-infure.edit-nippo')->extends('layouts.master');
    }
}

