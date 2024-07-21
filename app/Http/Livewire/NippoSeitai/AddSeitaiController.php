<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsLossSeitai;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\TdProductAssemblyLoss;
use App\Models\TdProductGoods;
use App\Models\TdProductGoodsAssembly;
use App\Models\TdProductGoodsLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddSeitaiController extends Component
{
    public $production_date;
    public $created_on;
    public $work_hour;
    public $lpk_date;
    public $panjang_lpk;
    public $code;
    public $name;
    public $dimensiinfure;
    public $qty_gulung;
    public $qty_gentan;
    public $machinename;
    public $empname;
    public $gentan_no;
    public $lpk_no;
    public $qty_lpk;
    public $machineno;
    public $employeeno;
    public $employeenoinfure;
    public $empnameinfure;
    public $work_shift;
    public $nomor_lot;
    public $qty_produksi;
    public $nomor_palet;
    public $infure_berat_loss;
    public $berat_produksi;
    public $petugas;
    public $machine_no;
    public $gentan_line;
    public $detailsGentan = [];
    public $detailsLoss = [];
    public $orderid;
    public $loss_seitai_id;
    public $berat_loss;
    public $namaloss;

    public function mount()
    {
        $this->production_date = Carbon::now()->format('Y-m-d');
        $this->created_on = Carbon::now()->format('Y-m-d');
        $this->work_hour = Carbon::now()->format('H:i');
        $workingShift = MsWorkingShift::where('work_hour_from', '<=', $this->work_hour)->where('work_hour_till', '>=', $this->work_hour)->first();
        $this->work_shift = $workingShift->id;
    }

    public function addGentan()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
        ]);

        if ($validatedData) {
            $this->emit('showModalGentan');
        }
    }

    public function addLoss()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
        ]);

        if ($validatedData) {
            $this->emit('showModalLoss');
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
        ]);

        try {
            $lastSeq = TdProductAssembly::whereDate('created_on', Carbon::today())
                    ->orderBy('seq_no', 'desc')
                    ->first();
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $employeinfure = MsEmployee::where('employeeno', $this->employeenoinfure)->first();
            $products = MsProduct::where('code', $this->code)->first();

            $seqno = 1;
            if(!empty($lastSeq)){
                $seqno = $lastSeq->seq_no + 1;
            }
            $today = Carbon::now();
            // dd($today->format('dmy').'-'.$seqno);

            $data = new TdProductGoods();
            $data->production_no = $today->format('dmy').'-'.$seqno;
            $data->production_date = $this->production_date;
            $data->employee_id = $employe->id;
            $data->employee_id_infure = $employeinfure->id;
            $data->work_shift = $this->work_shift;
            $data->work_hour = $this->work_hour;
            $data->machine_id = $machine->id;
            $data->lpk_id = $lpkid->id;
            $data->product_id = $products->id;
            $data->qty_produksi = $this->qty_produksi;
            $data->infure_berat_loss = $this->infure_berat_loss;
            $data->seq_no = $seqno;  
            $data->nomor_palet = $this->nomor_palet;
            $data->nomor_lot = $this->nomor_lot;
            $data->created_on = $this->created_on;            
            
            $data->save();

            TdProductGoodsAssembly::where('lpk_id',$lpkid->id)->update([
                'product_goods_id' => $data->id,
            ]);

            TdProductGoodsLoss::where('lpk_id',$lpkid->id)->update([
                'product_goods_id' => $data->id,
            ]);

            DB::commit();
            session()->flash('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('nippo-seitai');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('nippo-seitai');
    }

    public function saveGentan()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $assembly = TdProductAssembly::where('lpk_id', $lpkid->id)
                    ->first();

        $datas = new TdProductGoodsAssembly();
        // $datas->product_goods_id = $this->product_goods_id;
        $datas->product_assembly_id = $assembly->id;
        $datas->gentan_line = $this->gentan_line;
        $datas->lpk_id = $lpkid->id;
        
        $datas->save();

        $this->emit('closeModalGentan');
        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function saveLoss()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $loss = MsLossSeitai::where('code', $this->loss_seitai_id)
                    ->first();

        $datas = new TdProductGoodsLoss();
        // $datas->product_goods_id = $this->product_goods_id;
        $datas->loss_seitai_id = $loss->id;
        $datas->berat_loss = $this->berat_loss;
        $datas->lpk_id = $lpkid->id;
        
        $datas->save();

        $this->emit('closeModalGentan');
        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function deleteGentan($orderId)
    {
        $data = TdProductGoodsAssembly::findOrFail($orderId);
        $data->delete();

        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function deleteLoss($orderId)
    {
        $data = TdProductGoodsLoss::findOrFail($orderId);
        $data->delete();

        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
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
                'tolp.qty_lpk',
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
                $this->qty_lpk = $tdorderlpk->qty_lpk;

                $this->detailsGentan = DB::table('tdproduct_assembly as tdpa')
                ->join('tdproduct_goods_assembly as tga', 'tga.product_assembly_id', '=', 'tdpa.id')
                ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
                ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
                ->select(
                    'tga.id',
                    'tdpa.gentan_no',
                    'tga.gentan_line',
                    'mm.machineno',
                    'tdpa.work_shift',
                    'mse.empname',
                    'tdpa.production_date',
                    'tdpa.berat_produksi'
                )
                ->where('tdpa.lpk_id', $tdorderlpk->id)
                ->whereNull('tga.product_goods_id')
                ->get();

                $this->detailsLoss = DB::table('tdproduct_goods_loss as tgl')
                ->join('mslossseitai as mss', 'mss.id', '=', 'tgl.loss_seitai_id')
                ->select(
                    'tgl.id',
                    'mss.code',
                    'mss.name',
                    'tgl.berat_loss'
                )
                ->where('tgl.lpk_id', $tdorderlpk->id)
                ->whereNull('tgl.product_goods_id')
                ->get();
            }
        }

        if(isset($this->machineno) && $this->machineno != ''){
            $machine=MsMachine::where('machineno', $this->machineno)->first();
            // dd($machine);
            if($machine == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machinename = $machine->machinename;
            }
        }

        if(isset($this->employeeno) && $this->employeeno != ''){
            $msemployee=MsEmployee::where('employeeno', $this->employeeno)->first();

            if($msemployee == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
        }

        if(isset($this->employeenoinfure) && $this->employeenoinfure != ''){
            $msemployeeinfure=MsEmployee::where('employeeno', $this->employeenoinfure)->first();

            if($msemployeeinfure == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->empnameinfure = $msemployeeinfure->empname;
            }
        }

        if(isset($this->gentan_no) && $this->gentan_no != ''){
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            // $tdProduct=TdProductAssembly::where('gentan_no', $this->gentan_no)->where('lpk_id', $lpkid->id)->first();
            $tdProduct = DB::table('tdproduct_assembly as tdpa')
                        ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
                        ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
                        ->select(
                            'mm.machineno',
                            'mse.empname',
                            'tdpa.berat_produksi'
                        )
                        ->where('lpk_id', $lpkid->id)
                        ->where('gentan_no', $this->gentan_no)
                        ->first();

            if($tdProduct == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->petugas = $tdProduct->empname;
                $this->machine_no = $tdProduct->machineno;
                $this->berat_produksi = $tdProduct->berat_produksi;
            }
        }

        if(isset($this->loss_seitai_id) && $this->loss_seitai_id != ''){
            $lossSeitai = MsLossSeitai::where('code', $this->loss_seitai_id)->first();

            if($lossSeitai == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Kode Loss ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->namaloss = $lossSeitai->name;
            }
        }

        // $this->gentan_no = 1;
        // if (!empty($lpkid)) {
        //     $lastGentan = TdProductAssembly::where('lpk_id', $lpkid->lpk_id)
        //         ->max('gentan_no');

        //     $nogentan = 1;
        //     if(!empty($lastGentan)){
        //         $nogentan = $lastGentan->seq_no + 1;
        //     }
        //     $this->gentan_no=$nogentan;
        // }

        return view('livewire.nippo-seitai.add-seitai')->extends('layouts.master');
    }
}

