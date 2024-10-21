<?php

namespace App\Http\Livewire\MasterTabel\Inventory;

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

class PengeluaranBarangController extends Component
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
    public $jenis_pabean;
    #[Session]
    public $idBuyer;
    #[Session]
    public $transaksi;
    #[Session]
    public $status;

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
        $data = DB::table('tr_pengeluaran_barang')
        ->select(
            'jenis_pabean', 
            'no_pabean', 
            'tgl_pabean', 
            'trans_no', 
            'trans_date', 
            'cust_code', 
            'cust_name', 
            'item_code', 
            'item_name',
            DB::raw("TO_CHAR(dlv_qty, 'FM999999999.00') as dlv_qty"), 
            'sales_unit', 
            'curr_code', 
            DB::raw("TO_CHAR(net_price, 'FM999999999.00') as net_price"), 
            DB::raw("TO_CHAR(net_amount, 'FM999999999.00') as net_amount"), 
            DB::raw("'' as ket")
        )
        ->whereBetween('tgl_pabean', [$this->tglMasuk, $this->tglKeluar]);    

        if (isset($this->jenis_pabean) && $this->jenis_pabean['value'] != "" && $this->jenis_pabean != "undefined") {
            $data = $data->where('jenis_pabean', 'like', '%' . $this->jenis_pabean['value'] . '%' );
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function ($query) {
                $query->where('no_pabean', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('item_code', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('item_name', 'ilike', "%{$this->searchTerm}%");
            });
        }

        $data = $data->get();
    

        return view('livewire.inventory.pengeluaran-barang', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
