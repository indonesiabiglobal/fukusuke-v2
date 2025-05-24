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

class OrderLpkController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    #[Session('tglMasuk')]
    public $tglMasuk;
    #[Session('tglKeluar')]
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
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();

        // mengambil data dari session terlebih dahulu jika ada
        $this->tglMasuk = session('tglMasuk', Carbon::now()->format('d M Y'));
        $this->tglKeluar = session('tglKeluar', Carbon::now()->format('d M Y'));
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
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
            // $this->searchTerm,
            // $this->idProduct,
            // $this->idBuyer,
            // $this->status,
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
        // dd($this->idBuyer);
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

        if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tod.order_date', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tod.order_date', '<=', $this->tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tod.created_on', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tod.created_on', '<=', $this->tglKeluar);
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
        // paginate
        // $data = $data->when($this->paginate != 'all', function ($query) {
        //     return $query->paginate($this->paginate);
        // }, function ($query) {
        //     $count = $query->count();
        //     return $query->paginate($count);
        // });
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
