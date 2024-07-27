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
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;

class LpkEntryController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    public $tglMasuk;
    public $tglKeluar;
    public $searchTerm;
    public $transaksi;
    public $idBuyer;
    public $status;
    public $lpk_no;
    public $idProduct;

    use WithFileUploads, WithoutUrlPagination;
    public $file;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->tglMasuk = Carbon::now()->format('d-m-Y');
        $this->tglKeluar = Carbon::now()->format('d-m-Y');
    }

    public function search()
    {
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

        Excel::import(new LpkEntryImport, $this->file->path());

        // $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Excel imported successfully.']);
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
                tolp.total_assembly_qty,
                tod.po_no,
                mp.NAME AS product_name,
                mp.code as product_code,
                mm.machineno AS machine_no,
                mbu.NAME AS buyer_name,
                tolp.created_on AS tglproses,
                tolp.seq_no,
                tolp.updated_by,
                tolp.updated_on AS updatedt
            ")
            ->join('tdorder AS tod', 'tod.id', '=', 'tolp.order_id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tolp.product_id')
            ->join('msmachine AS mm', 'mm.id', '=', 'tolp.machine_id')
            ->join('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id');

        if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tolp.lpk_date', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tolp.lpk_date', '<=', $this->tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $tglMasuk = Carbon::createFromFormat('d-m-Y', $this->tglMasuk)->startOfDay();
                $data = $data->where('tolp.created_on', '>=', $tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $tglKeluar = Carbon::createFromFormat('d-m-Y', $this->tglKeluar)->endOfDay();
                $data = $data->where('tolp.created_on', '<=', $tglKeluar);
            }
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function ($query) {
                $query->where('mp.name', 'ilike', "%{$this->searchTerm}%")
                    // ->orWhere('tolp.lpk_no', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
            });
        }

        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tolp.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }

        if (isset($this->idBuyer) && $this->idBuyer['value'] != "" && $this->idBuyer != "undefined") {
            $data = $data->where('tod.buyer_id', $this->idBuyer['value']);
        }
        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('mp.id', $this->idProduct['value']);
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

        $data = $data->paginate(8);

        return view('livewire.order-lpk.lpk-entry', [
            'data' => $data,
        ])->extends('layouts.master');
    }
}
