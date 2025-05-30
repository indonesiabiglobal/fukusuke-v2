<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\TdOrderLpk;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Livewire\Attributes\Session;

class NippoInfureController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $tdOrderLpk;
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
    #[Session]
    public $entriesPerPage = 10;

    use WithFileUploads;
    public $file;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        // menghapus session kondisi bukan dari nippo infure
        $this->shouldForgetSession();

        $this->products = MsProduct::get();
        $this->tdOrderLpk = TdOrderLpk::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::whereIn('department_id', [10, 12, 15, 2, 4, 10])->orderBy('machineno')->get();
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
        if (empty($this->entriesPerPage)) {
            $this->entriesPerPage = 10;
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function updateEntriesPerPage($value)
    {
        $this->entriesPerPage = $value;
        $this->skipRender();
    }

    protected function shouldForgetSession()
    {
        // Periksa jika URL saat ini bukan 'nippo-infure/edit-infure' atau 'nippo-infure/add-infure'
        $previousUrl = url()->previous();
        $previousUrl = last(explode('/', $previousUrl));
        // dd($previousUrl, (Str::contains($previousUrl, 'edit-nippo') || Str::contains($previousUrl, 'add-nippo') || Str::contains($previousUrl,'nippo-infure')));
        if (!(Str::contains($previousUrl, 'edit-nippo') || Str::contains($previousUrl, 'add-nippo') || Str::contains($previousUrl,'nippo-infure'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'transaksi', 'machineId', 'status', 'lpk_no', 'searchTerm', 'idProduct', 'sortingTable', 'entriesPerPage');
        }
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
        $filter = [
            'lpk_no' => $this->lpk_no ?? null,
            'machineId' => $this->machineId['value'] ?? null,
            'idProduct' => $this->idProduct['value'] ?? null,
            'status' => $this->status['value'] ?? null,
            'searchTerm' => $this->searchTerm ?? null,
        ];

        $checklistInfure = new CheckListInfureController();
        $response = $checklistInfure->checklistInfure($tglMasuk, $tglKeluar, 'Checklist', true, $filter);
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function render()
    {
        try {
            $data = DB::table('tdproduct_assembly AS tda')
                ->select([
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
                    DB::raw('CASE WHEN tda.berat_standard = 0 THEN 0 ELSE (tda.berat_produksi / tda.berat_standard * 100) END AS rasio'),
                    DB::raw('tdol.total_assembly_line - tdol.panjang_lpk AS selisih'),
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
                    'tdo.product_code',
                    'mp.name AS product_name'
                ])
                ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
                ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id');

            if ($this->transaksi == 2) {
                if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                    $data = $data->where('tda.created_on', '>=', $this->tglMasuk . " 00:00:00");
                }

                if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                    $data = $data->where('tda.created_on', '<=', $this->tglKeluar . " 23:59:59");
                }
            } else {
                if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                    $data = $data->where('tda.production_date', '>=', $this->tglMasuk . " 00:00:00");
                }

                if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                    $data = $data->where('tda.production_date', '<=', $this->tglKeluar . " 23:59:59");
                }
            }

            if (isset($this->machineId) && $this->machineId['value'] != "" && $this->machineId != "undefined") {
                $data = $data->where('msm.id', $this->machineId['value']);
            }

            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined" && strlen($this->lpk_no) == 10) {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }

            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tda.product_id', $this->idProduct['value']);
            }

            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tda.status_production', 0)
                        ->where('tda.status_kenpin', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tda.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tda.status_kenpin', 1);
                }
            }

            if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
                $data = $data->where(function ($query) {
                    $query->where('tda.production_no', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('mp.name', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('mp.code', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tda.machine_id', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tda.nomor_han', 'ilike', "%{$this->searchTerm}%");
                });
            }
            $data->orderBy('tda.created_on', 'desc');
            $data = $data->get();
        } catch (\Exception $e) {
            $data = [];
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()]);
        }

        return view('livewire.nippo-infure.nippo-infure', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
