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
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Str;

class LpkEntryController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $lpkColors;
    public $buyer;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $searchTerm;
    #[Session]
    public $transaksi;
    #[Session]
    public $idBuyer;
    #[Session]
    public $status;
    #[Session]
    public $lpk_no;
    #[Session]
    public $idProduct;
    #[Session]
    public $idLPKColor;
    public $checkListLPK = [];
    #[Session]
    public $entriesPerPage = 10;
    #[Session]
    public $sortingTable;

    use WithFileUploads, WithoutUrlPagination;
    public $file;

    public function mount()
    {
        $this->shouldForgetSession();
        $this->products = MsProduct::get();
        $this->lpkColors = DB::table('mswarnalpk')->select('id', 'name', 'code')->get();
        $this->buyer = MsBuyer::get();

        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->startOfDay()->format('d M Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->endOfDay()->format('d M Y');
        }
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
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
        if (!(Str::contains($previousUrl, 'add-lpk') || Str::contains($previousUrl, 'edit-lpk') || Str::contains($previousUrl,'lpk-entry'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'searchTerm', 'idProduct', 'idBuyer', 'status', 'transaksi', 'lpk_no', 'sortingTable', 'entriesPerPage');
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
            // $this->searchTerm,
            // $this->idProduct,
            // $this->idBuyer,
            // $this->status,
        ), 'LPKList.xlsx');
    }

    public function printLPK()
    {
        $this->dispatch('redirectToPrint', $this->checkListLPK);
    }

    public function render()
    {
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

        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $tglMasuk = Carbon::parse($this->tglMasuk)->startOfDay()->format('Y-m-d H:i:s');
        }

        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $tglKeluar = Carbon::parse($this->tglKeluar)->endOfDay()->format('Y-m-d H:i:s');
        }

        if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tolp.lpk_date', '>=', $tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tolp.lpk_date', '<=', $tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                // $tglMasuk = Carbon::createFromFormat('d M Y', $this->tglMasuk)->startOfDay();
                $data = $data->where('tolp.created_on', '>=', $tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                // $tglKeluar = Carbon::createFromFormat('d M Y', $this->tglKeluar)->endOfDay();
                $data = $data->where('tolp.created_on', '<=', $tglKeluar);
            }
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function ($query) {
                $query->where('mp.name', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
            });
        }

        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tolp.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }

        if (isset($this->idBuyer) && $this->idBuyer['value'] != "" && $this->idBuyer != "undefined") {
            $data = $data->where('tod.buyer_id', $this->idBuyer['value']);
        }
        if (isset($this->idProduct) && $this->idProduct != "" && $this->idProduct != "undefined") {
            $data = $data->where('mp.id', $this->idProduct);
        }

        if (isset($this->idLPKColor) && $this->idLPKColor['value'] != "" && $this->idLPKColor != "undefined") {
            $data = $data->where('mp.warnalpkid', $this->idLPKColor['value']);
        }

        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            if ($this->status['value'] == 0) {
                $data = $data->where('tolp.reprint_no', $this->status['value']);
            } else if ($this->status['value'] == 1) {
                $data = $data->where('tolp.reprint_no', $this->status['value']);
            } else if ($this->status['value'] == 2) {
                $data = $data->where('tolp.reprint_no', '>', 1);
            } else if ($this->status['value'] == 3) {
                $data = $data->where('tolp.status_lpk', 0);
            } else if ($this->status['value'] == 4) {
                $data = $data->where('tolp.status_lpk', 1);
            }
        }

        $data = $data->get();

        return view('livewire.order-lpk.lpk-entry', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
