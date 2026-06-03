<?php

namespace App\Http\Livewire\Kenpin;

use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class KenpinSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $status;
    #[Session] public $searchTerm;
    #[Session] public $idProduct;
    #[Session] public $lpk_no;
    #[Session] public $nomor_palet;
    #[Session] public $nomor_lot;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tdkg.kenpin_date';
    #[Session] public $sortDirection = 'desc';

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

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
            'tdkg.kenpin_date', 'tdkg.kenpin_no', 'pg.nomor_palet',
            'msp.name', 'msp.code', 'mse.empname',
            'tdkg.qty_loss', 'tdkg.status_kenpin', 'msd.name', 'tdkg.updated_on',
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
            return view('livewire.kenpin.kenpin-seitai', [
                'data'     => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products' => $products,
            ])->extends('layouts.master');
        }

        try {
            $pg = DB::table('tdkenpin_goods_detail AS tdkgd')
                ->join('tdproduct_goods AS tdpg', 'tdkgd.product_goods_id', '=', 'tdpg.id')
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->select(
                    'tdkgd.kenpin_id',
                    DB::raw('MIN(tdpg.nomor_palet) AS nomor_palet'),
                    DB::raw('MIN(tdpg.nomor_lot) AS nomor_lot'),
                    DB::raw('MIN(tdol.lpk_no) AS lpk_no')
                )
                ->groupBy('tdkgd.kenpin_id');

            $data = DB::table('tdkenpin AS tdkg')
                ->leftJoinSub($pg, 'pg', fn($join) => $join->on('pg.kenpin_id', '=', 'tdkg.id'))
                ->join('msdepartment AS msd', 'msd.id', '=', 'tdkg.department_id')
                ->join('msproduct AS msp', 'tdkg.product_id', '=', 'msp.id')
                ->join('msemployee AS mse', 'mse.id', '=', 'tdkg.employee_id')
                ->select(
                    'tdkg.id',
                    'tdkg.kenpin_no',
                    'tdkg.kenpin_date',
                    'tdkg.qty_loss',
                    'tdkg.status_kenpin',
                    'tdkg.updated_by',
                    'tdkg.updated_on',
                    'msp.code',
                    'msp.name AS namaproduk',
                    'mse.empname AS namapetugas',
                    'msd.name AS nama_department',
                    'pg.nomor_palet',
                    'pg.nomor_lot'
                );

            if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
                $data->where('tdkg.kenpin_date', '>=', $this->tglMasuk . ' 00:00:00');
            }
            if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
                $data->where('tdkg.kenpin_date', '<=', $this->tglKeluar . ' 23:59:59');
            }
            if (!empty($this->lpk_no) && $this->lpk_no !== 'undefined') {
                $data->where('pg.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
                $data->where('tdkg.kenpin_no', 'ilike', "%{$this->searchTerm}%");
            }
            if (!empty($this->idProduct) && $this->idProduct !== 'undefined') {
                $data->where('tdkg.product_id', $this->idProduct);
            }
            if (!empty($this->nomor_palet) && $this->nomor_palet !== 'undefined') {
                $data->where('pg.nomor_palet', 'ilike', "%{$this->nomor_palet}%");
            }
            if (!empty($this->nomor_lot) && $this->nomor_lot !== 'undefined') {
                $data->where('pg.nomor_lot', 'ilike', "%{$this->nomor_lot}%");
            }
            if (isset($this->status) && $this->status !== '' && $this->status !== null && $this->status !== 'undefined') {
                $data->where('tdkg.status_kenpin', $this->status);
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.kenpin.kenpin-seitai', [
            'data'     => $data,
            'products' => $products,
        ])->extends('layouts.master');
    }
}
