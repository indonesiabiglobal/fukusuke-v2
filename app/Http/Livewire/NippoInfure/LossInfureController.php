<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use App\Traits\HandlesHeavyJob;

class LossInfureController extends Component
{
    use HandlesHeavyJob, WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $transaksi;
    #[Session] public $machineId;
    #[Session] public $status;
    #[Session] public $lpk_no;
    #[Session] public $searchTerm;
    #[Session] public $idProduct;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tdpa.created_on';
    #[Session] public $sortDirection = 'desc';

    public function loadData(): void
    {
        $this->isLoaded = true;
    }

    public function mount(): void
    {
        if (is_array($this->idProduct)) { $this->idProduct = $this->idProduct['value'] ?? null; }
        if (is_array($this->machineId)) { $this->machineId = $this->machineId['value'] ?? null; }
        if (is_array($this->status))    { $this->status    = $this->status['value']    ?? null; }

        if (empty($this->transaksi)) { $this->transaksi = 1; }
        if (empty($this->tglMasuk) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->tglKeluar) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('Y-m-d');
        }
    }

    public function sortBy(string $column): void
    {
        $allowed = [
            'tdol.lpk_no', 'tdol.lpk_date', 'tdol.panjang_lpk',
            'tdpa.panjang_produksi', 'tdol.qty_gentan', 'tdpa.gentan_no',
            'tdpa.berat_standard', 'mp.name', 'mp.code', 'msm.machineno',
            'tdpa.production_date', 'tdpa.created_on', 'tdpa.work_shift',
            'tdpa.seq_no', 'tdpa.infure_berat_loss', 'tdpa.updated_on',
        ];
        if (!in_array($column, $allowed)) return;
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function print()
    {
        $tglMasuk = $this->tglMasuk;
        $tglKeluar = $this->tglKeluar;

        $this->dispatch('redirectToPrint', "'$tglMasuk 00:00' and tdpa.created_on <= '$tglKeluar 23:59'");
    }

    public function export()
    {
        $this->startHeavyJob();
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
        $products = Cache::remember('ms_products_loss_infure', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
        );
        $buyer = Cache::remember('ms_buyers_loss_infure', 3600, fn() =>
            MsBuyer::select(['id', 'name'])->orderBy('name')->get()
        );
        $machine = Cache::remember('ms_machines_loss_infure', 3600, fn() =>
            MsMachine::select(['id', 'machineno'])->orderBy('machineno')->get()
        );

        if (!$this->isLoaded) {
            return view('livewire.nippo-infure.loss-infure', [
                'data'     => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products' => $products,
                'buyer'    => $buyer,
                'machine'  => $machine,
            ])->extends('layouts.master');
        }

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
                DB::raw('tdpa.berat_produksi / NULLIF(tdpa.berat_standard, 0) * 100 AS rasio'),
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

        $dateColumn = $this->transaksi == 2 ? 'tdpa.created_on' : 'tdpa.production_date';
        if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
            $data->where($dateColumn, '>=', $this->tglMasuk);
        }
        if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
            $data->where($dateColumn, '<=', $this->tglKeluar);
        }
        if (!empty($this->machineId) && $this->machineId !== 'undefined') {
            $data->where('msm.id', $this->machineId);
        }
        if (!empty($this->lpk_no) && $this->lpk_no !== 'undefined') {
            $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }
        if (!empty($this->idProduct) && $this->idProduct !== 'undefined') {
            $data->where('tdpa.product_id', $this->idProduct);
        }
        if (isset($this->status) && $this->status !== '' && $this->status !== null && $this->status !== 'undefined') {
            if ($this->status == 0) {
                $data->where('tdpa.status_production', 0)->where('tdpa.status_kenpin', 0);
            } elseif ($this->status == 1) {
                $data->where('tdpa.status_production', 1);
            } elseif ($this->status == 2) {
                $data->where('tdpa.status_kenpin', 1);
            }
        }
        if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
            $data->where(function ($q) {
                $q->where('tdpa.production_no', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.product_id',  'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.machine_id',  'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tdpa.nomor_han',   'ilike', "%{$this->searchTerm}%");
            });
        }

        try {
            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.nippo-infure.loss-infure', [
            'data'     => $data,
            'products' => $products,
            'buyer'    => $buyer,
            'machine'  => $machine,
        ])->extends('layouts.master');
    }
}
