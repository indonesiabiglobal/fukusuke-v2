<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use App\Traits\HandlesHeavyJob;

class NippoSeitaiController extends Component
{
    use HandlesHeavyJob;
    use WithPagination, WithoutUrlPagination;

    public bool $isLoaded = false;

    public function loadData(): void
    {
        $this->isLoaded = true;
    }

    protected $paginationTheme = 'bootstrap';

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $transaksi;
    #[Session] public $gentan_no;
    #[Session] public $machineId;
    #[Session] public $searchTerm;
    #[Session] public $lpk_no;
    #[Session] public $idProduct;
    #[Session] public $status;
    #[Session] public $perPage = 10;
    #[Session] public $sortColumn = 'tdpg.created_on';
    #[Session] public $sortDirection = 'desc';

    public function mount()
    {
        $this->shouldForgetSession();

        // Normalize legacy {value: x} array format (choices.js) to scalar (select2)
        if (is_array($this->idProduct)) { $this->idProduct = $this->idProduct['value'] ?? null; }
        if (is_array($this->machineId)) { $this->machineId = $this->machineId['value'] ?? null; }
        if (is_array($this->status))    { $this->status    = $this->status['value']    ?? null; }

        if (empty($this->transaksi)) { $this->transaksi = 1; }
        if (empty($this->tglMasuk)  || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglMasuk))  { $this->tglMasuk  = Carbon::now()->format('Y-m-d'); }
        if (empty($this->tglKeluar) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglKeluar)) { $this->tglKeluar = Carbon::now()->format('Y-m-d'); }
    }

    protected function shouldForgetSession()
    {
        $previousUrl = url()->previous();
        if (!(Str::contains($previousUrl, 'edit-seitai') || Str::contains($previousUrl, 'add-seitai') || Str::contains($previousUrl, 'nippo-seitai'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'transaksi', 'gentan_no', 'machineId', 'searchTerm', 'lpk_no', 'idProduct', 'status', 'perPage', 'sortColumn', 'sortDirection');
        }
    }

    public function search()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        $allowed = [
            'tdol.lpk_no', 'tdol.lpk_date', 'tdol.qty_lpk',
            'tdpg.qty_produksi', 'tdpg.seitai_berat_loss', 'tdpg.infure_berat_loss',
            'tdpg.nomor_palet', 'tdpg.nomor_lot', 'tdpg.seq_no',
            'tdpg.work_hour', 'tdpg.work_shift', 'tdpg.production_date',
            'tdpg.created_on', 'tdpg.updated_on',
            'mp.name', 'mp.code', 'mc.machineno',
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

    public function export()
    {
        $this->startHeavyJob();
        $filter = [
            'tglAwal'     => Carbon::parse($this->tglMasuk)->format('d-m-Y'),
            'tglAkhir'    => Carbon::parse($this->tglKeluar)->format('d-m-Y'),
            'jamAwal'     => '00:00:00',
            'jamAkhir'    => '23:59:59',
            'transaksi'   => $this->transaksi == 1 ? 'proses' : 'produksi',
            'lpk_no'      => $this->lpk_no   ?? null,
            'machineId'   => $this->machineId ?? null,
            'idProduct'   => $this->idProduct ?? null,
            'status'      => $this->status    ?? null,
            'gentan_no'   => $this->gentan_no ?? null,
            'jenisReport' => 'CheckList',
            'searchTerm'  => $this->searchTerm ?? null,
        ];

        $checklistSeitai = new CheckListSeitaiController();
        $response = $checklistSeitai->checklist(true, $filter);
        if ($response['status'] === 'success') {
            return response()->download($response['filename']);
        }
        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
    }

    public function render()
    {
        // Return empty paginator immediately on first render (wire:init will trigger loadData)
        if (!$this->isLoaded) {
            $products = Cache::remember('ms_products_seitai', 3600, fn() =>
                MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
            );
            $machine = Cache::remember('ms_machines_seitai', 3600, fn() =>
                MsMachine::select(['id', 'machineno'])
                    ->where('machineno', 'LIKE', '00S%')
                    ->orderBy('machineno')->get()
            );
            return view('livewire.nippo-seitai.nippo-seitai', [
                'data'     => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products' => $products,
                'machine'  => $machine,
            ])->extends('layouts.master');
        }

        $tglAwal  = Carbon::parse($this->tglMasuk)->format('d-m-Y 00:00:00');
        $tglAkhir = Carbon::parse($this->tglKeluar)->format('d-m-Y 23:59:59');

        $columns = [
            'tdpg.id AS id', 'tdpg.production_no AS production_no',
            'tdpg.production_date AS production_date',
            'tdpg.work_shift AS work_shift', 'tdpg.work_hour AS work_hour',
            'tdpg.machine_id AS machine_id', 'tdpg.lpk_id AS lpk_id',
            'tdpg.product_id AS product_id', 'tdpg.qty_produksi AS qty_produksi',
            'tdpg.seitai_berat_loss AS seitai_berat_loss',
            'tdpg.infure_berat_loss AS infure_berat_loss',
            'tdpg.nomor_palet AS nomor_palet', 'tdpg.nomor_lot AS nomor_lot',
            'tdpg.seq_no AS seq_no',
            'tdpg.status_production AS status_production',
            'tdpg.status_warehouse AS status_warehouse',
            'tdpg.created_by AS created_by', 'tdpg.created_on AS created_on',
            'tdpg.updated_by AS updated_by', 'tdpg.updated_on AS updated_on',
            'tdol.order_id AS order_id', 'tdol.lpk_no AS lpk_no',
            'tdol.lpk_date AS lpk_date', 'tdol.qty_lpk AS qty_lpk',
            'tdol.panjang_lpk AS panjang_lpk',
            'tdol.total_assembly_qty AS total_assembly_qty',
            DB::raw('tdol.qty_lpk - tdol.total_assembly_qty AS selisih'),
            'mp.name AS product_name', 'mp.code', 'mc.machineno',
        ];

        try {
            $data = DB::table('tdproduct_goods AS tdpg')
                ->select($columns)
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->leftJoin('msmachine AS mc', 'mc.id', '=', 'tdpg.machine_id');

            if ($this->transaksi == 2) {
                if (!empty($this->tglMasuk)) {
                    $data->whereRaw("(tdpg.production_date::date + tdpg.work_hour::time) >= ?", [$tglAwal]);
                }
                if (!empty($this->tglKeluar)) {
                    $data->whereRaw("(tdpg.production_date::date + tdpg.work_hour::time) <= ?", [$tglAkhir]);
                }
            } else {
                if (!empty($this->tglMasuk))  { $data->where('tdpg.created_on', '>=', $tglAwal); }
                if (!empty($this->tglKeluar)) { $data->where('tdpg.created_on', '<=', $tglAkhir); }
            }

            if (!empty($this->lpk_no) && $this->lpk_no != "undefined") {
                $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (!empty($this->searchTerm)) {
                $data->where(function ($q) {
                    $q->where('tdol.lpk_no',         'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpg.production_no', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpg.nomor_palet',   'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpg.nomor_lot',     'ilike', "%{$this->searchTerm}%")
                      ->orWhere('mp.name',             'ilike', "%{$this->searchTerm}%")
                      ->orWhere('mp.code',             'ilike', "%{$this->searchTerm}%");
                });
            }
            if (!empty($this->idProduct) && $this->idProduct != "undefined") {
                $data->where('tdpg.product_id', $this->idProduct);
            }
            if (!empty($this->machineId) && $this->machineId != "undefined") {
                $data->where('tdpg.machine_id', $this->machineId);
            }
            if (!empty($this->gentan_no) && $this->gentan_no != "undefined") {
                $data->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('tdproduct_goods_assembly AS tga')
                      ->join('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id')
                      ->whereColumn('tga.product_goods_id', 'tdpg.id')
                      ->where('ta.gentan_no', $this->gentan_no);
                });
            }
            if (isset($this->status) && $this->status !== "" && $this->status !== null) {
                if ($this->status == 0) {
                    $data->where('tdpg.status_production', 0)->where('tdpg.status_warehouse', 0);
                } elseif ($this->status == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);

        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        $products = Cache::remember('ms_products_seitai', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
        );
        $machine = Cache::remember('ms_machines_seitai', 3600, fn() =>
            MsMachine::select(['id', 'machineno'])
                ->where('machineno', 'LIKE', '00S%')
                ->orderBy('machineno')->get()
        );

        return view('livewire.nippo-seitai.nippo-seitai', [
            'data'     => $data,
            'products' => $products,
            'machine'  => $machine,
        ])->extends('layouts.master');
    }
}
