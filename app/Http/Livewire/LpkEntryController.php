<?php

namespace App\Http\Livewire;

use App\Exports\LpkEntryExport;
use App\Exports\LpkEntryImport;
use App\Exports\LpkListExport;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LpkEntryController extends Component
{
    use WithFileUploads, WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $searchTerm;
    #[Session] public $transaksi;
    #[Session] public $idBuyer;
    #[Session] public $status;
    #[Session] public $lpk_no;
    #[Session] public $idProduct;
    #[Session] public $idLPKColor;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tolp.lpk_date';
    #[Session] public $sortDirection = 'desc';

    public $checkListLPK = [];
    public $file;

    public function sortBy(string $column): void
    {
        $allowed = [
            'tolp.lpk_no', 'tolp.lpk_date', 'tolp.panjang_lpk',
            'tolp.qty_lpk', 'tolp.qty_gentan', 'tolp.qty_gulung',
            'tolp.total_assembly_line', 'tolp.total_assembly_qty',
            'tod.po_no', 'mp.name', 'mp.code', 'mm.machineno',
            'mbu.name', 'tolp.created_on', 'tolp.updated_on', 'tolp.seq_no',
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

        if (is_array($this->idBuyer))    { $this->idBuyer    = $this->idBuyer['value']    ?? null; }
        if (is_array($this->idProduct))  { $this->idProduct  = $this->idProduct['value']  ?? null; }
        if (is_array($this->idLPKColor)) { $this->idLPKColor = $this->idLPKColor['value'] ?? null; }
        if (is_array($this->status))     { $this->status     = $this->status['value']     ?? null; }

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
        $previousUrl = url()->previous();
        if (!(Str::contains($previousUrl, 'add-lpk') || Str::contains($previousUrl, 'edit-lpk') || Str::contains($previousUrl, 'lpk-entry'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'searchTerm', 'idProduct', 'idBuyer', 'status', 'transaksi', 'lpk_no', 'idLPKColor', 'perPage', 'sortColumn', 'sortDirection');
        }
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function download()
    {
        return Excel::download(new LpkEntryExport, 'Template_LPK.xlsx');
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
            Excel::import(new LpkEntryImport, $this->file->path());
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Excel imported successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function print()
    {
        return Excel::download(new LpkListExport(
            $this->tglMasuk,
            $this->tglKeluar,
        ), 'LPKList.xlsx');
    }

    public function printLPK()
    {
        $this->dispatch('redirectToPrint', $this->checkListLPK);
    }

    public function render()
    {
        $products = Cache::remember('ms_products_lpk_entry', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
        );
        $lpkColors = Cache::remember('ms_lpkcolors_lpk_entry', 3600, fn() =>
            DB::table('mswarnalpk')->select('id', 'name', 'code')->get()
        );
        $buyer = Cache::remember('ms_buyers_lpk_entry', 3600, fn() =>
            MsBuyer::select(['id', 'name'])->orderBy('name')->get()
        );

        if (!$this->isLoaded) {
            return view('livewire.order-lpk.lpk-entry', [
                'data'      => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'products'  => $products,
                'lpkColors' => $lpkColors,
                'buyer'     => $buyer,
            ])->extends('layouts.master');
        }

        try {
            $data = DB::table('tdorderlpk AS tolp')
                ->selectRaw("
                    tolp.id,
                    tolp.lpk_no,
                    tolp.lpk_date,
                    tolp.panjang_lpk,
                    tolp.qty_lpk,
                    tolp.qty_gentan,
                    tolp.qty_gulung,
                    tolp.total_assembly_line AS infure,
                    tolp.panjang_lpk - (tolp.qty_lpk * mp.productlength / 1000) AS selisih,
                    tolp.total_assembly_qty,
                    tod.po_no,
                    mp.NAME AS product_name,
                    mp.code as product_code,
                    mm.machineno AS machine_no,
                    mbu.NAME AS buyer_name,
                    tolp.created_on,
                    tolp.seq_no,
                    tolp.updated_by,
                    tolp.updated_on AS updatedt,
                    mwl.name as warna_lpk
                ")
                ->join('tdorder AS tod', 'tod.id', '=', 'tolp.order_id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tolp.product_id')
                ->join('msmachine AS mm', 'mm.id', '=', 'tolp.machine_id')
                ->leftJoin('mswarnalpk AS mwl', 'mwl.id', '=', 'mp.warnalpkid')
                ->join('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id');

            $dateColumn = $this->transaksi == 2 ? 'tolp.lpk_date' : 'tolp.created_on';

            if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
                $data->where($dateColumn, '>=', $this->tglMasuk . ' 00:00:00');
            }
            if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
                $data->where($dateColumn, '<=', $this->tglKeluar . ' 23:59:59');
            }
            if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
                $data->where(function ($q) {
                    $q->where('mp.name', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
                });
            }
            if (!empty($this->lpk_no) && $this->lpk_no !== 'undefined') {
                $data->where('tolp.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (!empty($this->idBuyer) && $this->idBuyer !== 'undefined') {
                $data->where('tod.buyer_id', $this->idBuyer);
            }
            if (!empty($this->idProduct) && $this->idProduct !== 'undefined') {
                $data->where('mp.id', $this->idProduct);
            }
            if (!empty($this->idLPKColor) && $this->idLPKColor !== 'undefined') {
                $data->where('mp.warnalpkid', $this->idLPKColor);
            }
            if (isset($this->status) && $this->status !== '' && $this->status !== null && $this->status !== 'undefined') {
                if ($this->status == 0) {
                    $data->where('tolp.reprint_no', 0);
                } elseif ($this->status == 1) {
                    $data->where('tolp.reprint_no', 1);
                } elseif ($this->status == 2) {
                    $data->where('tolp.reprint_no', '>', 1);
                } elseif ($this->status == 3) {
                    $data->where('tolp.status_lpk', 0);
                } elseif ($this->status == 4) {
                    $data->where('tolp.status_lpk', 1);
                }
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.order-lpk.lpk-entry', [
            'data'      => $data,
            'products'  => $products,
            'lpkColors' => $lpkColors,
            'buyer'     => $buyer,
        ])->extends('layouts.master');
    }
}
