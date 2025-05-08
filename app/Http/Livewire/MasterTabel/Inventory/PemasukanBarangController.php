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

class PemasukanBarangController extends Component
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
        $data = DB::table('tr_pemasukan_barang')
        ->select(
            'jenis_pabean',
            'no_pabean',
            'tgl_pabean',
            'trans_no',
            'vend_dlv_no',
            'trans_date',
            'vendor_code',
            'vendor_name',
            'item_code',
            'item_name',
            DB::raw('TO_CHAR(rcv_qty, \'FM999999999999990.00\') AS rcv_qty'),
            'pch_unit',
            'curr_code',
            DB::raw('TO_CHAR(net_price, \'FM999999999999990.0000\') AS net_price'),
            DB::raw('TO_CHAR(net_amount, \'FM999999999999990.0000\') AS net_amount'),
            DB::raw("'' AS Ket")
        )
        ->whereBetween('tgl_pabean', [$this->tglMasuk, $this->tglKeluar])
        ->orderBy('tgl_pabean', 'DESC');

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
    
    // if ($jenis_pabean != '') {
    //     $query->where('jenis_pabean', 'like', '%' . $jenis_pabean . '%');
    // }
    // if ($no_pabean != '') {
    //     $query->where('no_pabean', 'like', '%' . $no_pabean . '%');
    // }
    // if ($item_code != '') {
    //     $query->where('item_code', 'like', '%' . $item_code . '%');
    // }
    // if ($item_name != '') {
    //     $query->where('item_name', 'like', '%' . $item_name . '%');
    // }
    
    // $result = $query->orderBy('tgl_pabean', 'DESC')
        // ->offset($offset)
        // ->limit($rows)
        $data = $data->get();
    

        return view('livewire.inventory.pemasukan-barang', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
