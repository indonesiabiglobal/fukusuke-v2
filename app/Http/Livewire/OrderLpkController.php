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
use Livewire\Attributes\Session;
use Illuminate\Support\Str;

class OrderLpkController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $searchTerm;
    #[Session]
    public $idProduct;
    #[Session]
    public $idBuyer;
    #[Session]
    public $transaksi;
    #[Session]
    public $status;
    #[Session]
    public $sortingTable;
    #[Session]
    public $entriesPerPage = 10;

    use WithFileUploads;
    public $file;

    use WithPagination, WithoutUrlPagination;
    public $searchParam = '';
    public $paginate = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function mount()
    {
        $this->shouldForgetSession();
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();

        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->startOfDay()->format('d M Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->endOfDay()->format('d M Y');
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
        $previousUrl = url()->previous();
        $previousUrl = last(explode('/', $previousUrl));
        if (!(Str::contains($previousUrl, 'add-order') || Str::contains($previousUrl, 'edit-order') || Str::contains($previousUrl, 'order-lpk'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'searchTerm', 'idProduct', 'idBuyer', 'status', 'transaksi', 'sortingTable', 'entriesPerPage');
        }
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
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
                'tod.processseq',
                'tod.updated_by',
                'tod.updated_on',
                'tod.created_by',
                'tod.created_on'
            )
            ->leftjoin('msproduct AS mp', 'mp.id', '=', 'tod.product_id')
            ->leftjoin('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id')
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->transaksi == 1) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tod.processdate', '>=', $this->tglMasuk . " 00:00");
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tod.processdate', '<=', $this->tglKeluar . " 23:59:59");
            }
        } else if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tod.order_date', '>=', $this->tglMasuk  . " 00:00:00");
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tod.order_date', '<=', $this->tglKeluar . " 23:59:59");
            }
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function ($query) {
                $query->where('mp.name', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('mbu.name', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tod.product_code', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
            });
        }

        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('mp.id', $this->idProduct['value']);
        }

        if (isset($this->idBuyer) && $this->idBuyer['value'] != "" && $this->idBuyer != "undefined") {
            $data = $data->where('tod.buyer_id', $this->idBuyer['value']);
        }

        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tod.status_order', $this->status['value']);
        }
        $data = $data->get();

        return view('livewire.order-lpk.order-lpk', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
