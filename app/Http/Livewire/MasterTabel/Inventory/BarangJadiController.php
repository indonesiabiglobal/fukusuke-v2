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

class BarangJadiController extends Component
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
        // Build the dynamic WHERE clause based on conditions
        // $strqW = '';
        // if ($item_code != '') {
        //     $strqW .= " AND a.item_code LIKE '%$item_code%' ";
        // }
        // if ($item_name != '') {
        //     $strqW .= " AND a.item_name LIKE '%$item_name%' ";
        // }

        // // Fetch the initial date value for `$tgl_inv_awal`
        // $tgl_inv_awal = DB::table('tr_inv_produk_harian_head')
        //     ->where('trans_date', '<=', DB::raw("DATE_SUB('$tgl_awal', INTERVAL 1 DAY)"))
        //     ->select(DB::raw("IFNULL(MAX(trans_date), '2000-01-01') as trans_date"))
        //     ->value('trans_date');

        // // Fetch the initial date value for `$tgl_inv_akhir`
        // $tgl_inv_akhir = DB::table('tr_inv_produk_harian_head')
        //     ->where('trans_date', '<=', $tgl_akhir)
        //     ->select(DB::raw("IFNULL(MAX(trans_date), '2000-01-01') as trans_date"))
        //     ->value('trans_date');

        // $data = DB::table(DB::raw("with b as (SELECT b.item_code, SUM(b.wh2+b.wh1+b.mesin+b.qc) as awal FROM tr_inv_produk_harian_head a INNER JOIN tr_inv_produk_harian_det b ON a.trans_no=b.trans_no WHERE a.trans_date=date_sub('$tgl_awal', interval 1 day) GROUP BY b.item_code)
        //     , c as (SELECT no_produk, SUM(isi_palet) as masuk FROM tr_produk_in_head WHERE tgl_proses BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY no_produk)
        //     ,d as (SELECT b.no_produk, SUM(isi_palet) as keluar FROM tr_export_head a INNER JOIN tr_export_det b ON a.trans_no=b.trans_no WHERE a.tgl_ekspor BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY b.no_produk),
        //     e as (SELECT b.item_code, SUM(qty) as peny FROM tr_inv_adjust_head a INNER JOIN tr_inv_adjust_det b ON a.trans_no=b.trans_no LEFT JOIN ms_item c ON b.item_code=c.item_code WHERE a.trans_date BETWEEN '$tgl_awal' AND '$tgl_akhir' AND c.item_group='PRODUCT' GROUP BY b.item_code),
        //     f as (SELECT b.item_code, SUM(b.wh2+b.wh1+b.mesin+b.qc) as opname FROM tr_inv_produk_harian_head a INNER JOIN tr_inv_produk_harian_det b ON a.trans_no=b.trans_no WHERE a.trans_date='$tgl_akhir' GROUP BY b.item_code),
        //     a as(
        //     SELECT a.item_code, a.item_name, a.unit_code, a.item_type_code, a.item_group, '' as location_code
        //     , format(IFNULL(b.awal,0),0) as awal, IFNULL(c.masuk,0) as masuk, format(IFNULL(d.keluar,0),0) as keluar
        //     , format(IFNULL(e.peny,0),0) as peny, (IFNULL(b.awal,0) + IFNULL(c.masuk,0) - IFNULL(d.keluar,0) + IFNULL(e.peny,0)) as akhir
        //     , format(IFNULL(f.opname,0),0) as opname, 0 selisih FROM ms_item a 
        //     LEFT JOIN  b ON a.item_code=b.item_code 
        //     LEFT JOIN c ON a.item_code=c.no_produk 
        //     LEFT JOIN d ON a.item_code=d.no_produk 
        //     LEFT JOIN e ON a.item_code=e.item_code 
        //     LEFT JOIN f ON a.item_code=f.item_code WHERE a.item_group='PRODUCT' $strqW)
            
        //     select SQL_CALC_FOUND_ROWS a.*, format(opname,0) akhr, format(masuk-(akhir-opname),0) msk from a where a.awal<>0 or a.opname<>0 or a.keluar<>0 or a.peny<>0 or akhir<>0 or opname<>0 "));

        // $data = $data->get();

        return view('livewire.inventory.bahan-baku', [
            // 'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
