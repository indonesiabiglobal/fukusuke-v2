<?php

namespace App\Http\Livewire\Kenpin;

use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class KenpinSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $products;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $status;
    #[Session]
    public $searchTerm;
    #[Session]
    public $idProduct;
    #[Session]
    public $lpk_no;
    #[Session]
    public $nomor_palet;
    #[Session]
    public $nomor_lot;
    #[Session]
    public $sortingTable;


    public function mount()
    {
        $this->products = MsProduct::active()
            ->orderBy('code_alias', 'ASC')
            ->orderBy('name', 'ASC')->get();
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
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
        $this->render();
        // $tglMasuk = '';
        // if (isset($this->tglMasuk) && $this->tglMasuk != '') {
        //     $tglMasuk = "tdkg.kenpin_date >= '" . $this->tglMasuk . " 00:00:00'";
        // }
        // $tglKeluar = '';
        // if (isset($this->tglKeluar) && $this->tglKeluar != '') {
        //     $tglKeluar = "tdkg.kenpin_date <= '" . $this->tglKeluar . " 23:59:59'";
        // }
        // $status = '';
        // if (isset($this->status) && $this->status != '') {
        //     $status = "AND tdkg.status_kenpin = '" . $this->status . "'";
        // }
        // $searchTerm = '';
        // if (isset($this->searchTerm) && $this->searchTerm != '') {
        //     $searchTerm = "WHERE (tdpg.nomor_palet ilike '%" . $this->searchTerm .
        //     "%' OR tdpg.nomor_lot ilike '%" . $this->searchTerm .
        //     "%')";
        // }

        // $this->data = DB::select("
        // SELECT
        //     tdkg.id,
        //     tdkg.kenpin_no,
        //     tdkg.kenpin_date,
        //     tdkg.employee_id,
        //     tdkg.product_id,
        //     tdkg.qty_loss,
        //     tdkg.remark,
        //     tdkg.status_kenpin,
        //     tdkg.created_by,
        //     tdkg.created_on,
        //     tdkg.updated_by,
        //     tdkg.updated_on,
        //     msp.code,
        //     msp.NAME AS namaproduk,
        //     mse.empname AS namapetugas
        // FROM
        //     tdkenpin_goods AS tdkg
        //     INNER JOIN (
        //     SELECT DISTINCT
        //         tdkgd.kenpin_goods_id AS kenpin_goods_id
        //     FROM
        //         tdkenpin_goods_detail AS tdkgd
        //         INNER JOIN tdproduct_goods AS tdpg ON tdkgd.product_goods_id = tdpg.
        //         ID INNER JOIN tdorderlpk AS tdol ON tdpg.lpk_id = tdol.ID
        //     $searchTerm
        //     ) AS distinct1 ON tdkg.ID = distinct1.kenpin_goods_id
        //     INNER JOIN msproduct AS msp ON tdkg.product_id = msp.
        //     ID INNER JOIN msemployee AS mse ON mse.ID = tdkg.employee_id
        // WHERE
        //     $tglMasuk
        //     AND $tglKeluar
        // -- 	AND tdkg.kenpin_no = ''
        // -- 	AND msp.ID =
        //     $status
        // ");
    }

    public function render()
    {
        $data = DB::table('tdkenpin_goods AS tdkg')
            ->join(
                DB::raw("(SELECT DISTINCT tdkgd.kenpin_goods_id AS kenpin_goods_id
                      FROM tdkenpin_goods_detail AS tdkgd
                      INNER JOIN tdproduct_goods AS tdpg ON tdkgd.product_goods_id = tdpg.id
                      INNER JOIN tdorderlpk AS tdol ON tdpg.lpk_id = tdol.id
                      ) AS distinct1"),
                'tdkg.id',
                '=',
                'distinct1.kenpin_goods_id'
            )
            ->join('msproduct AS msp', 'tdkg.product_id', '=', 'msp.id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdkg.employee_id')
            ->select(
                'tdkg.id',
                'tdkg.kenpin_no',
                'tdkg.kenpin_date',
                'tdkg.employee_id',
                'tdkg.product_id',
                'tdkg.qty_loss',
                'tdkg.remark',
                'tdkg.status_kenpin',
                'tdkg.created_by',
                'tdkg.created_on',
                'tdkg.updated_by',
                'tdkg.updated_on',
                'msp.code',
                'msp.name AS namaproduk',
                'mse.empname AS namapetugas'
            );
        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $data = $data->where('tdkg.kenpin_date', '>=', $this->tglMasuk);
        }
        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $data = $data->where('tdkg.kenpin_date', '<=', $this->tglKeluar);
        }
        // if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
        //     $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        // }
        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where('tdkg.kenpin_no', 'ilike', "%{$this->searchTerm}%");
        }
        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('tdkg.product_id', $this->idProduct['value']);
        }
        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tdkg.status_kenpin', $this->status['value']);
        }
        $data = $data->get();

        return view('livewire.kenpin.kenpin-seitai', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
