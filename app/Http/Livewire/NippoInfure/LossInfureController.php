<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class LossInfureController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    public $machine;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $transaksi;
    #[Session]
    public $machineId;
    #[Session]
    public $status;
    #[Session]
    public $lpk_no;
    #[Session]
    public $searchTerm;
    #[Session]
    public $idProduct;
    #[Session]
    public $sortingTable;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::get();

        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d M Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d M Y');
        }
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function search()
    {
        $this->render();
    }

    public function print()
    {
        $tglMasuk = $this->tglMasuk;
        $tglKeluar = $this->tglKeluar;

        $this->dispatch('redirectToPrint', "'$tglMasuk 00:00' and tdpa.created_on <= '$tglKeluar 23:59'");
    }

    public function export()
    {
        $tglMasuk = Carbon::parse($this->tglMasuk . " 00:00:00");
        $tglKeluar = Carbon::parse($this->tglKeluar . " 23:59:59");

        $checklistInfure = new CheckListInfureController();
        $response = $checklistInfure->checklistInfure($tglMasuk, $tglKeluar, 'Loss');
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    // public function updatedLpkNo($lpk_no)
    // {
    //     dd($lpk_no);
    //     // The rest of your code...
    //     if (strlen($lpk_no) >= 9) {
    //         if (!str_contains($lpk_no, '-')) {
    //             $this->lpk_no = substr_replace($lpk_no, '-', 6, 0);
    //         }
    //         $tdorderlpk = DB::table('tdorderlpk')
    //             ->select('id')
    //             ->where('lpk_no', $this->lpk_no)
    //             ->first();

    //         if ($tdorderlpk == null) {
    //             $this->dispatch('notification', [
    //                 'type' => 'warning',
    //                 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar'
    //             ]);
    //         }
    //     }
    // }

    public function render()
    {
        $data = DB::table('tdproduct_assembly AS tdpa')
            ->select([
                'tdpa.id AS id',
                'tdpa.production_no AS production_no',
                'tdpa.production_date AS production_date',
                'tdpa.employee_id AS employee_id',
                'tdpa.work_shift AS work_shift',
                'tdpa.work_hour AS work_hour',
                'msm.machineno',
                'tdpa.lpk_id AS lpk_id',
                'tdpa.product_id AS product_id',
                'tdpa.panjang_produksi AS panjang_produksi',
                'tdpa.panjang_printing_inline AS panjang_printing_inline',
                'tdpa.berat_standard AS berat_standard',
                'tdpa.berat_produksi AS berat_produksi',
                DB::raw('tdpa.berat_produksi / tdpa.berat_standard * 100 AS rasio'),
                DB::raw('tdol.total_assembly_line - tdol.panjang_lpk AS selisih'),
                'tdpa.nomor_han AS nomor_han',
                'tdpa.gentan_no AS gentan_no',
                'tdpa.seq_no AS seq_no',
                'tdpa.status_production AS status_production',
                'tdpa.status_kenpin AS status_kenpin',
                'tdpa.infure_cost AS infure_cost',
                'tdpa.infure_cost_printing AS infure_cost_printing',
                'tdpa.infure_berat_loss AS infure_berat_loss',
                'tdpa.kenpin_berat_loss AS kenpin_berat_loss',
                'tdpa.kenpin_meter_loss AS kenpin_meter_loss',
                'tdpa.kenpin_meter_loss_proses AS kenpin_meter_loss_proses',
                'tdpa.created_by AS created_by',
                'tdpa.created_on AS created_on',
                'tdpa.updated_by AS updated_by',
                'tdpa.updated_on AS updated_on',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan AS qty_gentan',
                'tdol.qty_gulung AS qty_gulung',
                'tdol.qty_lpk AS qty_lpk',
                'tdol.total_assembly_line AS total_assembly_line',
                'tdol.total_assembly_qty AS total_assembly_qty',
                'mp.name AS product_name',
                'mp.code AS code',
            ])
            ->join('tdorderlpk AS tdol', 'tdpa.lpk_id', '=', 'tdol.id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id');

        if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdpa.created_on', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdpa.created_on', '<=', $this->tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdpa.production_date', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdpa.production_date', '<=', $this->tglKeluar);
            }
        }
        if (isset($this->machineId) && $this->machineId['value'] != "" && $this->machineId != "undefined") {
            $data = $data->where('msm.id', $this->machineId['value']);
        }

        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }

        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('tdpa.product_id', $this->idProduct['value']);
        }

        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            if ($this->status['value'] == 0) {
                $data->where('tdpa.status_production', 0)
                    ->where('tdpa.status_kenpin', 0);
            } elseif ($this->status['value'] == 1) {
                $data->where('tdpa.status_production', 1);
            } elseif ($this->status['value'] == 2) {
                $data->where('tdpa.status_kenpin', 1);
            }
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function ($query) {
                $query->where('tdpa.production_no', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.product_id', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.machine_id', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.nomor_han', 'ilike', "%{$this->searchTerm}%");
            });
        }
        $data->orderBy('tdpa.created_on', 'desc');
        // $data = $data->paginate(8);
        $data = $data->get();

        return view('livewire.nippo-infure.loss-infure', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
