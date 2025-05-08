<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Exports\NippoSeitaiExport;
use App\Helpers\phpspreadsheet;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NippoSeitaiController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    public $machine;
    #[Session]
    public $transaksi;
    #[Session]
    public $gentan_no;
    #[Session]
    public $machineId;
    #[Session]
    public $searchTerm;
    #[Session]
    public $lpk_no;
    #[Session]
    public $idProduct;
    #[Session]
    public $status;
    #[Session]
    public $sortingTable;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        // menghapus session kondisi bukan dari nippo seitai
        $this->shouldForgetSession();

        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::where('machineno',  'LIKE', '00S%')->orderBy('machineno')->get();
        $this->machine = $this->machine->map(function ($item) {
            $item->machinenumber = str_replace('00S', '', $item->machineno);
            return $item;
        });
        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d M Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d M Y');
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

    protected function shouldForgetSession()
    {
        // Periksa jika URL saat ini bukan 'nippo-seitai/edit-seitai' atau 'nippo-seitai/add-seitai'
        $previousUrl = url()->previous();
        $previousUrl = last(explode('/', $previousUrl));
        if (!(Str::contains($previousUrl, 'edit-seitai') || $previousUrl === 'add-seitai' || $previousUrl === 'nippo-seitai')) {
            $this->reset('tglMasuk', 'tglKeluar', 'gentan_no', 'machineId', 'searchTerm', 'lpk_no', 'idProduct', 'status', 'transaksi', 'sortingTable');
        }
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function search()
    {
        $this->render();
    }

    public function print()
    {
        return Excel::download(new NippoSeitaiExport(
            $this->tglMasuk,
            $this->tglKeluar,
            // $this->searchTerm,
            // $this->idProduct,
            // $this->idBuyer,
            // $this->status,
        ), 'NippoSeitai-CheckList.xlsx');
    }

    public function export()
    {
        $filter = [
            'tglAwal' => Carbon::parse($this->tglMasuk)->format('d-m-Y'),
            'tglAkhir' => Carbon::parse($this->tglKeluar)->format('d-m-Y'),
            'jamAwal' => '00:00:00',
            'jamAkhir' => '23:59:59',
            'transaksi' => $this->transaksi == 1 ? 'proses' : 'produksi',
            'lpk_no' => $this->lpk_no ?? null,
            'machineId' => $this->machineId['value'] ?? null,
            'idProduct' => $this->idProduct['value'] ?? null,
            'status' => $this->status['value'] ?? null,
            'gentan_no' => $this->gentan_no ?? null,
            'jenisReport' => 'CheckList',
            'searchTerm' => $this->searchTerm ?? null,
        ];

        $checklistInfure = new CheckListSeitaiController();
        $response = $checklistInfure->checklist(true, $filter);
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function render()
    {
        // $tglAwal = $this->tglMasuk;
        $tglAwal = Carbon::parse($this->tglMasuk)->format('d-m-Y 00:00:00');
        $tglAkhir = Carbon::parse($this->tglKeluar)->format('d-m-Y 23:59:59');

        if ($this->transaksi == 2) {
            // produksi
            $data = DB::table('tdproduct_goods AS tdpg')
                ->select(
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.employee_id AS employee_id',
                    'tdpg.employee_id_infure AS employee_id_infure',
                    'tdpg.work_shift AS work_shift',
                    'tdpg.work_hour AS work_hour',
                    'tdpg.machine_id AS machine_id',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.seitai_berat_loss AS seitai_berat_loss',
                    'tdpg.infure_berat_loss AS infure_berat_loss',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdpg.seq_no AS seq_no',
                    'tdpg.status_production AS status_production',
                    'tdpg.status_warehouse AS status_warehouse',
                    'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                    'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                    'tdpg.created_by AS created_by',
                    'tdpg.created_on AS created_on',
                    'tdpg.updated_by AS updated_by',
                    'tdpg.updated_on AS updated_on',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    'tdol.panjang_lpk AS panjang_lpk',
                    'tdol.qty_gentan AS qty_gentan',
                    'tdol.qty_gulung AS qty_gulung',
                    'tdol.qty_lpk AS qty_lpk',
                    'tdol.total_assembly_qty AS total_assembly_qty',
                    DB::raw('tdol.qty_lpk - tdol.total_assembly_qty AS selisih'),
                    'mp.name AS product_name',
                    'mp.code',
                    'mc.machineno',
                )
                ->distinct()
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->leftJoin('tdproduct_goods_assembly AS tga', 'tga.product_goods_id', '=', 'tdpg.id')
                ->leftJoin('msmachine AS mc', 'mc.id', '=', 'tdpg.machine_id')
                ->leftJoin('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id');

            if (!empty($this->tglMasuk)) {
                $data = $data->whereRaw(
                    "(tdpg.production_date::date + tdpg.work_hour::time) >= ?",
                    [$tglAwal]
                );
            }

            if (!empty($this->tglKeluar)) {
                $data = $data->whereRaw(
                    "(tdpg.production_date::date + tdpg.work_hour::time) <= ?",
                    [$tglAkhir]
                );
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function ($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_palet', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_lot', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }
            if (isset($this->machineId) && $this->machineId['value'] != "" && $this->machineId != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineId['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != "" && $this->gentan_no != "undefined") {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                        ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }
            // $data = $data->paginate(8);
        } else {
            // proses
            $data = DB::table('tdproduct_goods AS tdpg')
                ->select([
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.employee_id AS employee_id',
                    'tdpg.employee_id_infure AS employee_id_infure',
                    'tdpg.work_shift AS work_shift',
                    'tdpg.work_hour AS work_hour',
                    'tdpg.machine_id AS machine_id',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.seitai_berat_loss AS seitai_berat_loss',
                    'tdpg.infure_berat_loss AS infure_berat_loss',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdpg.seq_no AS seq_no',
                    'tdpg.status_production AS status_production',
                    'tdpg.status_warehouse AS status_warehouse',
                    'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                    'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                    'tdpg.created_by AS created_by',
                    'tdpg.created_on AS created_on',
                    'tdpg.updated_by AS updated_by',
                    'tdpg.updated_on AS updated_on',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    'tdol.panjang_lpk AS panjang_lpk',
                    'tdol.qty_gentan AS qty_gentan',
                    'tdol.qty_gulung AS qty_gulung',
                    'tdol.qty_lpk AS qty_lpk',
                    'tdol.total_assembly_qty AS total_assembly_qty',
                    DB::raw('tdol.qty_lpk - tdol.total_assembly_qty AS selisih'),
                    'mp.name AS product_name',
                    'mp.code',
                    'mc.machineno',
                ])
                ->distinct()
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->leftJoin('tdproduct_goods_assembly AS tga', 'tga.product_goods_id', '=', 'tdpg.id')
                ->leftJoin('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id')
                ->leftJoin('msmachine AS mc', 'mc.id', '=', 'tdpg.machine_id');

            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $data = $data->where('tdpg.created_on', '>=', $tglAwal);
            }
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $data = $data->where('tdpg.created_on', '<=', $tglAkhir);
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function ($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_palet', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_lot', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }
            if (isset($this->machineId) && $this->machineId['value'] != "" && $this->machineId != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineId['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != '') {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                        ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }

            // $data = $data->paginate(8);
        }
        $data = $data->get();
        return view('livewire.nippo-seitai.nippo-seitai', [
            'data' => $data,
        ])->extends('layouts.master');
    }


    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
