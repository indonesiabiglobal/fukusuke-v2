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

class BahanBakuController extends Component
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
        // $strqW = '';
        // if ($item_code != '') {
        //     $strqW .= " AND a.item_code LIKE '%$item_code%' ";
        // }
        // if ($item_name != '') {
        //     $strqW .= " AND a.item_name LIKE '%$item_name%' ";
        // }

        // // Fetch the initial date values
        // $tgl_inv_awal = DB::select("SELECT IFNULL(MAX(trans_date), '2000-01-01') as trans_date FROM tr_inv_material_harian_head WHERE trans_date <= DATE_SUB(?, INTERVAL 1 DAY)", [$tgl_awal])[0]->trans_date;

        // $tgl_inv_akhir = DB::select("SELECT IFNULL(MAX(trans_date), '2000-01-01') as trans_date FROM tr_inv_material_harian_head WHERE trans_date <= ?", [$tgl_akhir])[0]->trans_date;


        // $data = DB::table(DB::raw("with b as (SELECT b.item_code, SUM(b.wh2+b.wh1+b.mesin) as awal 
        //     FROM tr_inv_material_harian_head a INNER JOIN tr_inv_material_harian_det b ON a.trans_no=b.trans_no 
        //     WHERE a.trans_date=date_sub('$tgl_awal', interval 1 day) GROUP BY b.item_code), c as (SELECT item_code, SUM(qty) as masuk 
        //     FROM tr_ap_inv_head a INNER JOIN tr_ap_inv_det b ON a.trans_no=b.trans_no WHERE a.in_date BETWEEN '$tgl_awal' AND '$tgl_akhir' 
        //     GROUP BY item_code), e as (SELECT b.item_code, SUM(qty) as peny FROM tr_inv_adjust_head a INNER JOIN tr_inv_adjust_det b ON a.trans_no=b.trans_no 
        //     LEFT JOIN ms_item c ON b.item_code=c.item_code WHERE a.trans_date 
        //     BETWEEN '$tgl_awal' AND '$tgl_akhir' AND c.item_group='MATERIAL' GROUP BY b.item_code), f as (SELECT b.item_code, SUM(b.wh2+b.wh1+b.mesin) as opname FROM tr_inv_material_harian_head a 
        //     INNER JOIN tr_inv_material_harian_det b ON a.trans_no=b.trans_no 
        //     WHERE a.trans_date='$tgl_inv_akhir' GROUP BY b.item_code), g as (SELECT item_code, SUM(qty) as movein FROM tr_inv_movein_head a INNER JOIN tr_inv_movein_det b ON a.trans_no=b.trans_no 
        //     WHERE a.trans_date BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY item_code)
            
        //     , z as (SELECT a.item_code, a.item_name, a.unit_code, a.item_type_code, a.item_group, '' as location_code
        //     , format(IFNULL(b.awal,0),0) as awal
        //     , format(IFNULL(c.masuk,0) + IFNULL(g.movein,0),0) as masuk
        //     , format((IFNULL(b.awal,0) + IFNULL(c.masuk,0) + IFNULL(g.movein,0) - 0 + IFNULL(e.peny,0))-ifnull(opname,0),0) as keluar
        //     , format(IFNULL(e.peny,0),0) as peny
        //     , format((IFNULL(b.awal,0) + IFNULL(c.masuk,0) + IFNULL(g.movein,0) - 0 + IFNULL(e.peny,0)),0) as akhir
        //     , format(IFNULL(f.opname,0),0) as opname, 0 as selisih FROM ms_item a 
        //     LEFT JOIN b ON a.item_code=b.item_code 
        //     LEFT JOIN c ON a.item_code=c.item_code 
        //     LEFT JOIN e ON a.item_code=e.item_code 
        //     LEFT JOIN  f ON a.item_code=f.item_code 
        //     LEFT JOIN  g ON a.item_code=g.item_code WHERE a.item_group='MATERIAL'  $strqW) 
            
        //     select SQL_CALC_FOUND_ROWS * from z where z.awal<>0 or z.opname<>0 or z.masuk<>0 or z.akhir<>0 or z.opname<>0 or z.peny<>0"));

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
