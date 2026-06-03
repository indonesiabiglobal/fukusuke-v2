<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Carbon\Carbon;
use App\Models\MsProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Session;

class KenpinInfureController extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $searchTerm;
    #[Session] public $idProduct;
    #[Session] public $lpk_no;
    #[Session] public $status;
    #[Session] public $no_han;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tdka.kenpin_date';
    #[Session] public $sortDirection = 'desc';

    public function loadData(): void
    {
        $this->isLoaded = true;
    }

    public function mount(): void
    {
        if (is_array($this->idProduct)) { $this->idProduct = $this->idProduct['value'] ?? null; }
        if (is_array($this->status))    { $this->status    = $this->status['value']    ?? null; }

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
            'tdka.kenpin_date', 'tdka.kenpin_no', 'tdol.lpk_no', 'tdol.lpk_date',
            'tdol.qty_lpk', 'tdol.panjang_lpk', 'msp.name', 'msp.code',
            'mse.empname', 'tdka.total_berat_loss', 'tdka.updated_on',
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

    public function search(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Cache::remember('ms_products_kenpin', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code', 'code_alias'])
                ->active()->orderBy('code_alias', 'ASC')->orderBy('name', 'ASC')->get()
        );

        if (!$this->isLoaded) {
            return view('livewire.kenpin.kenpin-infure', [
                'data'     => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products' => $products,
            ])->extends('layouts.master');
        }

        try {
            $data = DB::table('tdkenpin AS tdka')
                ->join('tdorderlpk AS tdol', 'tdka.lpk_id', '=', 'tdol.id')
                ->join('msproduct AS msp', 'tdol.product_id', '=', 'msp.id')
                ->join('msemployee AS mse', 'mse.id', '=', 'tdka.employee_id')
                ->select(
                    'tdka.id',
                    'tdka.kenpin_no',
                    'tdka.kenpin_date',
                    'mse.empname',
                    'tdka.lpk_id',
                    'tdka.total_berat_loss',
                    DB::raw("CASE WHEN tdka.status_kenpin = 1 THEN 'Proses' ELSE 'Finish' END AS status_kenpin"),
                    'tdka.updated_by',
                    'tdka.updated_on',
                    'tdol.order_id',
                    'tdol.lpk_no',
                    'tdol.lpk_date',
                    'tdol.qty_lpk',
                    'tdol.panjang_lpk',
                    'msp.id AS id1',
                    'msp.code',
                    'msp.name AS namaproduk',
                )
                ->distinct();

            if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
                $data->where('tdka.kenpin_date', '>=', $this->tglMasuk . ' 00:00:00');
            }
            if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
                $data->where('tdka.kenpin_date', '<=', $this->tglKeluar . ' 23:59:59');
            }
            if (!empty($this->lpk_no) && $this->lpk_no !== 'undefined') {
                $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
                $data->where('tdka.kenpin_no', 'ilike', "%{$this->searchTerm}%");
            }
            if (!empty($this->idProduct) && $this->idProduct !== 'undefined') {
                $data->where('tdol.product_id', $this->idProduct);
            }
            if (!empty($this->no_han) && $this->no_han !== 'undefined') {
                $data->where('tdka.no_han', 'ilike', "%{$this->no_han}%");
            }
            if (isset($this->status) && $this->status !== '' && $this->status !== null && $this->status !== 'undefined') {
                $data->where('tdka.status_kenpin', $this->status);
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.kenpin.kenpin-infure', [
            'data'     => $data,
            'products' => $products,
        ])->extends('layouts.master');
    }
}
