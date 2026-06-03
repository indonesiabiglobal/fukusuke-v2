<?php

namespace App\Http\Livewire;

use App\Exports\OrderEntryExport;
use App\Exports\OrderEntryImport;
use App\Exports\OrderLpkExport;
use App\Models\MsBuyer;
use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;

class OrderLpkController extends Component
{
    use WithFileUploads, WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $searchTerm;
    #[Session] public $idProduct;
    #[Session] public $idBuyer;
    #[Session] public $transaksi;
    #[Session] public $status;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tod.order_date';
    #[Session] public $sortDirection = 'desc';

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public $file;

    public function sortBy(string $column): void
    {
        $allowed = [
            'tod.po_no', 'mp.name', 'tod.product_code', 'mbu.name',
            'tod.order_qty', 'tod.order_date', 'tod.stufingdate',
            'tod.etddate', 'tod.etadate', 'tod.processdate',
            'tod.updated_on', 'tod.created_on',
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

    public function loadData(): void
    {
        $this->isLoaded = true;
    }

    public function mount(): void
    {
        $this->shouldForgetSession();

        if (is_array($this->idProduct)) { $this->idProduct = $this->idProduct['value'] ?? null; }
        if (is_array($this->idBuyer))   { $this->idBuyer   = $this->idBuyer['value']   ?? null; }
        if (is_array($this->status))    { $this->status    = $this->status['value']    ?? null; }

        if (empty($this->tglMasuk) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->tglKeluar) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }
    }

    protected function shouldForgetSession(): void
    {
        $previousUrl = last(explode('/', url()->previous()));
        if (!(Str::contains($previousUrl, 'add-order') || Str::contains($previousUrl, 'edit-order') || Str::contains($previousUrl, 'order-lpk'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'searchTerm', 'idProduct', 'idBuyer', 'status', 'transaksi', 'perPage', 'sortColumn', 'sortDirection');
        }
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function download()
    {
        return Excel::download(new OrderEntryExport, 'Template_Order.xlsx');
    }

    public function print()
    {
        return Excel::download(new OrderLpkExport(
            $this->tglMasuk,
            $this->tglKeluar,
        ), 'OrderList.xlsx');
    }

    public function updatedFile()
    {
        $this->import();
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        try {
            Excel::import(new OrderEntryImport, $this->file->path());

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Excel imported successfully.']);
        } catch (\Exception  $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $products = Cache::remember('ms_products_order_lpk', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
        );
        $buyer = Cache::remember('ms_buyers_order_lpk', 3600, fn() =>
            MsBuyer::select(['id', 'name'])->orderBy('name')->get()
        );

        if (!$this->isLoaded) {
            return view('livewire.order-lpk.order-lpk', [
                'data'     => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products' => $products,
                'buyer'    => $buyer,
            ])->extends('layouts.master');
        }

        try {
            $data = DB::table('tdorder AS tod')
                ->select(
                    'tod.id',
                    'tod.po_no',
                    'mp.name AS produk_name',
                    'tod.product_code',
                    'mbu.name AS buyer_name',
                    'tod.order_qty',
                    'tod.order_date',
                    'tod.stufingdate',
                    'tod.etddate',
                    'tod.etadate',
                    'tod.processdate',
                    'tod.updated_by',
                    'tod.updated_on',
                    'tod.created_on'
                )
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tod.product_id')
                ->leftJoin('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id');

            $dateColumn = $this->transaksi == 2 ? 'tod.order_date' : 'tod.processdate';
            if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
                $data->where($dateColumn, '>=', $this->tglMasuk . ' 00:00:00');
            }
            if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
                $data->where($dateColumn, '<=', $this->tglKeluar . ' 23:59:59');
            }
            if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
                $data->where(function ($q) {
                    $q->where('mp.name',          'ilike', "%{$this->searchTerm}%")
                        ->orWhere('mbu.name',       'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tod.product_code', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tod.po_no',       'ilike', "%{$this->searchTerm}%");
                });
            }
            if (!empty($this->idProduct) && $this->idProduct !== 'undefined') {
                $data->where('mp.id', $this->idProduct);
            }
            if (!empty($this->idBuyer) && $this->idBuyer !== 'undefined') {
                $data->where('tod.buyer_id', $this->idBuyer);
            }
            if (isset($this->status) && $this->status !== '' && $this->status !== null && $this->status !== 'undefined') {
                $data->where('tod.status_order', $this->status);
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.order-lpk.order-lpk', [
            'data'     => $data,
            'products' => $products,
            'buyer'    => $buyer,
        ])->extends('layouts.master');
    }
}
