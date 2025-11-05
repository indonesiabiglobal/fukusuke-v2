<?php

namespace App\Http\Livewire\Kenpin;

use App\Helpers\phpspreadsheet;
use App\Http\Livewire\Kenpin\Report\DetailReportKenpinSeitaiController;
use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsProduct;
use App\Models\TdKenpin;
use App\Models\TdKenpinGoods;
use App\Models\TdKenpinGoodsDetail;
use App\Models\TdProductGoods;
use App\Models\MsMachinePartDetail;
use App\Models\MsMasalahKenpin;
use App\Models\TdKenpinAssembly;
use App\Models\TdKenpinGoodsDetailBox;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EditKenpinSeitaiController extends Component
{
    public $idKenpinGoods;
    public $kenpin_id;
    public $kenpin_no;
    public $kenpin_date;
    public $department_id = 7; // default ke seitai
    public $kode_produk;
    public $nama_produk;
    public $product_id;
    public $name;
    public $code;
    public $code_alias;
    public $empname;
    public $employeeno;
    public $kode_ng;
    public $nama_ng;
    public $penyebab;
    public $keterangan_penyebab;
    public $penanggulangan;
    public $bagian_mesin_id;
    public $bagianMesinListInfure;
    public $bagianMesinListSeitai;
    public $details;
    public $nomor_palet_old;
    public $nomor_palet;
    public $orderid;
    public $no_palet;
    public $no_lot;
    public $no_lpk;
    public $quantity;
    public $qty_loss;
    public $remark;
    public $status = 1;
    public $status_kenpin_old;
    public $idKenpinGoodDetailUpdate;
    public $qtyLossTotal = 0;
    public $qtyProduksiTotal = 0;
    public $is_kasus;
    public $nomor_box_dari;
    public $nomor_box_sampai;
    public $waktu_kenpin_dari;
    public $waktu_kenpin_sampai;

    // Master data for NG codes
    public $masalahKenpin;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    public function mount(Request $request)
    {
        $this->idKenpinGoods = $request->orderId;
        $data = TdKenpin::where('id', $request->orderId)->first();
        $employee = MsEmployee::where('id', $data->employee_id)->first();
        $product = MsProduct::where('id', $data->product_id)->first();

        $this->kenpin_id = $data->id;
        $this->kenpin_no = $data->kenpin_no;
        $this->kenpin_date = Carbon::parse($data->kenpin_date)->format('d-m-Y');
        $this->department_id = $data->department_id;
        $this->nomor_palet_old = $data->nomor_palet;
        $this->nomor_palet = $data->nomor_palet;
        $this->kode_produk = $product->code;
        $this->nama_produk = $product->name;
        $this->name = $product->name;
        $this->product_id = $product->id;
        $this->code_alias = $product->code_alias;
        $this->empname = $employee->empname;
        $this->employeeno = $employee->employeeno;
        $this->qty_loss = $data->qty_loss;
        $this->remark = $data->remark;
        $this->status = $data->status_kenpin;
        $this->status_kenpin_old = $data->status_kenpin;
        $this->bagian_mesin_id = $data->machine_part_detail_id;
        $this->penyebab = $data->penyebab;
        $this->keterangan_penyebab = $data->keterangan_penyebab;
        $this->penanggulangan = $data->penanggulangan;
        $this->is_kasus = $data->is_kasus;

        // Load masalah kenpin if exists
        if ($data->masalah_kenpin_id) {
            $this->masalahKenpin = MsMasalahKenpin::find($data->masalah_kenpin_id);
            if ($this->masalahKenpin) {
                $this->kode_ng = $this->masalahKenpin->code;
                $this->nama_ng = $this->masalahKenpin->name;
            }
        }

        // Load bagian mesin lists
        $bagianMesinList = MsMachinePartDetail::with('machinePart')->get();
        $this->bagianMesinListInfure = $bagianMesinList->filter(function ($item) {
            return $item->machinePart && $item->machinePart->department_id == 2;
        });
        $this->bagianMesinListSeitai = $bagianMesinList->filter(function ($item) {
            return $item->machinePart && $item->machinePart->department_id == 7;
        });

        $this->details = DB::table('tdproduct_goods AS tdpg')
            ->select(
                'tdpg.id AS id',
                'tdpg.production_no AS production_no',
                'tdpg.production_date AS production_date',
                'tdpg.lpk_id AS lpk_id',
                'tdpg.product_id AS product_id',
                'tdpg.work_shift AS work_shift',
                'msp.code AS code',
                'msp.name AS namaproduk',
                'tdpg.qty_produksi AS qty_produksi',
                'tdpg.nomor_palet AS nomor_palet',
                'tdpg.nomor_lot AS nomor_lot',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tgd.qty_loss',
                'tgd.nomor_box_dari',
                'tgd.nomor_box_sampai',
                'tgd.waktu_kenpin_dari',
                'tgd.waktu_kenpin_sampai'
            )
            ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
            ->join('msproduct AS msp', 'tdpg.product_id', '=', 'msp.id')
            ->leftJoin('tdkenpin_goods_detail AS tgd', function ($join) {
                $join->on('tgd.product_goods_id', '=', 'tdpg.id')
                    ->where('tgd.kenpin_id', '=', $this->idKenpinGoods);
            })
            ->where(function ($query) {
                $query->whereExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('tdkenpin_goods_detail')
                        ->whereRaw('product_goods_id = tdpg.id')
                        ->where('kenpin_id', $this->idKenpinGoods);
                });
            })
            ->get()
            ->map(function ($item) {
                $item->qty_loss = $item->qty_loss ?? 0;
                $item->nomor_box_dari = $item->nomor_box_dari ?? null;
                $item->nomor_box_sampai = $item->nomor_box_sampai ?? null;
                return $item;
            });

        $this->qtyLossTotal = $this->details->sum('qty_loss');
        $this->qtyProduksiTotal = $this->details->sum('qty_produksi');
    }

    public function rules()
    {
        return [
            'kode_produk' => 'required',
            'employeeno' => 'required',
            'kode_ng' => 'required',
            'penyebab' => 'required_if:status,2',
            'keterangan_penyebab' => 'required_if:status,2',
            'penanggulangan' => 'required_if:status,2',
            'bagian_mesin_id' => 'required_if:status,2'
        ];
    }

    public function messages()
    {
        return [
            'kode_produk.required' => 'Kode Produk tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
            'kode_ng.required' => 'Kode NG tidak boleh kosong',
            'penyebab.required' => 'Penyebab tidak boleh kosong',
            'keterangan_penyebab.required' => 'Keterangan penyebab tidak boleh kosong',
            'penanggulangan.required' => 'Penanggulangan tidak boleh kosong',
            'bagian_mesin_id.required' => 'Bagian mesin tidak boleh kosong'
        ];
    }

    public function edit($idKenpinGoodDetailUpdate)
    {
        $this->resetSeitai();
        $this->idKenpinGoodDetailUpdate = $idKenpinGoodDetailUpdate;
        array_map(function ($detail) use ($idKenpinGoodDetailUpdate) {
            if ($detail->id == $idKenpinGoodDetailUpdate) {
                $this->orderid = $detail->id;
                $this->no_palet = $detail->nomor_palet;
                $this->no_lot = $detail->nomor_lot;
                $this->no_lpk = $detail->lpk_no;
                $this->quantity = number_format($detail->qty_produksi);
                $this->qty_loss = number_format($detail->qty_loss);
                $this->nomor_box_dari = $detail->nomor_box_dari ?? '';
                $this->nomor_box_sampai = $detail->nomor_box_sampai ?? '';
                $this->waktu_kenpin_dari = $detail->waktu_kenpin_dari ?? '';
                $this->waktu_kenpin_sampai = $detail->waktu_kenpin_sampai ?? '';
            }
        }, $this->details->toArray());

        $this->dispatch('showModal');
    }

    public function deleteSeitai($id)
    {
        $data = TdKenpinGoodsDetail::where('product_goods_id', $id)->where('kenpin_id', $this->kenpin_id)->first();
        if ($data) {
            // delete from details array
            $this->details = $this->details->filter(function ($detail) use ($id) {
                return $detail->id != $id;
            })->values();

            // update total qty loss
            $this->qtyLossTotal = $this->details->sum('qty_loss');

            $data->delete();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    public function updatedEmployeeno()
    {
        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 2) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->active()->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->employeeno = '';
                $this->empname = '';
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
                $this->resetValidation('employeeno');
            }
        }
    }

    public function updatedKodeNg()
    {
        if (!empty($this->kode_ng)) {
            $this->masalahKenpin = MsMasalahKenpin::where('code', $this->kode_ng)
                ->whereHas('departmentGroup', function ($query) {
                    $query->where('department_id', $this->department_id);
                })
                ->first();

            if ($this->masalahKenpin) {
                $this->nama_ng = $this->masalahKenpin->name;
                $this->resetValidation('kode_ng');
            } else {
                $this->kode_ng = '';
                $this->nama_ng = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode NG tidak ditemukan']);
            }
        } else {
            $this->nama_ng = '';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode NG tidak boleh kosong']);
        }
    }

    public function updatedStatus()
    {
        $this->penyebab = '';
        $this->keterangan_penyebab = '';
        $this->penanggulangan = '';
    }

    public function resetSeitai()
    {
        $this->qty_loss = '';
        $this->nomor_box_dari = '';
        $this->nomor_box_sampai = '';
        $this->waktu_kenpin_dari = '';
        $this->waktu_kenpin_sampai = '';
        $this->orderid = '';
        $this->no_palet = '';
        $this->no_lot = '';
        $this->no_lpk = '';
        $this->quantity = '';
    }

    public function saveSeitai()
    {
        $validatedData = $this->validate([
            'qty_loss' => 'required',
            'nomor_box_dari' => 'nullable|numeric',
            'nomor_box_sampai' => 'nullable|numeric|gte:nomor_box_dari',
            'waktu_kenpin_dari' => 'nullable|date_format:H:i',
            'waktu_kenpin_sampai' => 'nullable|date_format:H:i|after:waktu_kenpin_dari',
        ], [
            'nomor_box_sampai.gte' => 'Nomor box sampai harus lebih besar atau sama dengan nomor box dari'
        ]);

        // update pada details
        foreach ($this->details as &$detail) {
            if ($detail->id == $this->idKenpinGoodDetailUpdate) {
                $detail->qty_loss = (int)str_replace(',', '', $validatedData['qty_loss']);
                $detail->nomor_box_dari = $this->nomor_box_dari;
                $detail->nomor_box_sampai = $this->nomor_box_sampai;
                $detail->waktu_kenpin_dari = $this->waktu_kenpin_dari;
                $detail->waktu_kenpin_sampai = $this->waktu_kenpin_sampai;
                break;
            }
        }

        // menghitung total qty loss
        $this->qtyLossTotal = $this->details->sum('qty_loss');
        $this->qtyProduksiTotal = $this->details->sum('qty_produksi');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->resetSeitai();
        $this->dispatch('closeModal');
    }

    public function save()
    {
        $this->validate([
            'kode_produk' => 'required',
            'employeeno' => 'required',
            'kode_ng' => 'required',
            'penyebab' => 'required_if:status,2',
            'keterangan_penyebab' => 'required_if:status,2',
            'penanggulangan' => 'required_if:status,2',
            'bagian_mesin_id' => 'required_if:status,2'
        ], [
            'kode_produk.required' => 'Kode Produk tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
            'kode_ng.required' => 'Kode NG tidak boleh kosong',
            'penyebab.required' => 'Penyebab tidak boleh kosong',
            'keterangan_penyebab.required' => 'Keterangan penyebab tidak boleh kosong',
            'penanggulangan.required' => 'Penanggulangan tidak boleh kosong',
            'bagian_mesin_id.required' => 'Bagian mesin tidak boleh kosong'
        ]);

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();

            $data = TdKenpin::where('id', $this->idKenpinGoods)->first();
            $data->kenpin_no = $this->kenpin_no;
            $data->kenpin_date = Carbon::parse($this->kenpin_date);
            $data->employee_id = $mspetugas->id;
            $data->product_id = $this->product_id;
            $data->department_id = $this->department_id;
            $data->nomor_palet = $this->nomor_palet;
            $qtyLoss = $this->details->sum('qty_loss');
            $data->qty_loss = $qtyLoss;
            $data->status_kenpin = $this->status;
            $data->is_kasus = $this->is_kasus ? true : false;

            if ($this->masalahKenpin) {
                $data->masalah_kenpin_id = $this->masalahKenpin->id;
            }
            $data->machine_part_detail_id = $this->bagian_mesin_id;
            $data->penyebab = $this->penyebab;
            $data->keterangan_penyebab = $this->keterangan_penyebab;
            $data->penanggulangan = $this->penanggulangan;

            if ($this->status == 2) {
                $data->done_at = Carbon::now();
            } else {
                $data->done_at = null;
            }

            $data->updated_on = Carbon::now();
            $data->updated_by = auth()->user()->username;

            $data->save();

            // hapus data pada kenpin goods detail
            TdKenpinGoodsDetail::where('kenpin_id', $this->idKenpinGoods)->delete();

            // update pada kenpin goods detail
            foreach ($this->details as $detail) {
                $kenpinGoodsDetail = new TdKenpinGoodsDetail();
                $kenpinGoodsDetail->product_goods_id = $detail->id;
                $kenpinGoodsDetail->kenpin_id = $data->id;
                $kenpinGoodsDetail->qty_loss = $detail->qty_loss ?? 0;
                $kenpinGoodsDetail->nomor_box_dari = $detail->nomor_box_dari ?? null;
                $kenpinGoodsDetail->nomor_box_sampai = $detail->nomor_box_sampai ?? null;
                $kenpinGoodsDetail->waktu_kenpin_dari = $detail->waktu_kenpin_dari ?? null;
                $kenpinGoodsDetail->waktu_kenpin_sampai = $detail->waktu_kenpin_sampai ?? null;
                $kenpinGoodsDetail->created_on = Carbon::now();
                $kenpinGoodsDetail->created_by = auth()->user()->username;
                $kenpinGoodsDetail->updated_on = Carbon::now();
                $kenpinGoodsDetail->updated_by = auth()->user()->username;
                $kenpinGoodsDetail->save();
            }

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order updated successfully.']);
            return redirect()->route('kenpin-seitai-kenpin');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('kenpin-seitai-kenpin');
    }

    public function search()
    {
        $this->render();
    }


    public function addPalet()
    {
        try {
            $this->validate();
            // Kode Anda jika validasi berhasil
        } catch (ValidationException $e) {
            // Tangani validasi yang gagal
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data belum lengkap']);

            // Mengirimkan pesan error ke view Livewire secara manual jika diperlukan
            $this->setErrorBag($e->validator->errors());

            return;
        }

        if (isset($this->nomor_palet) && $this->nomor_palet != '') {
            $product = MsProduct::where('code', $this->kode_produk)->first();
            $this->details = DB::table('tdproduct_goods AS tdpg')
                ->select(
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'msp.code AS code',
                    'msp.name AS namaproduk',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    DB::raw('0 AS qty_loss'),
                    DB::raw('NULL AS nomor_box_dari'),
                    DB::raw('NULL AS nomor_box_sampai')
                )
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->join('msproduct AS msp', 'tdpg.product_id', '=', 'msp.id')
                ->leftJoin('tdkenpin_goods_detail AS tgd', 'tgd.product_goods_id', '=', 'tdpg.id')
                ->where('tdpg.product_id', $product->id)
                ->where('tdpg.nomor_palet', $this->nomor_palet)
                ->get();

            if ($this->details == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Employee ' . $this->details . ' Tidak Terdaftar']);
            }
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor Palet yang dicari tidak boleh kosong']);
        }
    }

    public function export()
    {
        $tglAwal = Carbon::parse($this->kenpin_date)->startOfDay();
        $tglAkhir = Carbon::parse($this->kenpin_date)->endOfDay();

        $filter = [
            'kenpin_id' => $this->kenpin_id,
            'kenpin_no' => $this->kenpin_no,
        ];

        $detailReportKenpinSeitai = new DetailReportKenpinSeitaiController();
        $response = $detailReportKenpinSeitai->detailReportKenpinSeitai($tglAwal, $tglAkhir, $filter, true);
        if ($response['status'] == 'success') {
            return response()->download($response['filename'])->deleteFileAfterSend(true);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function deleteModal()
    {
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function deleteKenpin()
    {
        try {
            DB::beginTransaction();

            // Ambil data kenpin yang akan dihapus
            $kenpin = TdKenpin::find($this->idKenpinGoods);

            if (!$kenpin) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Data kenpin tidak ditemukan']);
                return;
            }

            // Hapus semua detail kenpin goods yang terkait
            TdKenpinGoodsDetail::where('kenpin_id', $kenpin->id)->delete();

            // Hapus data kenpin utama
            $kenpin->delete();

            DB::commit();

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data kenpin berhasil dihapus']);
            return redirect()->route('kenpin-seitai-kenpin');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal menghapus data kenpin: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.kenpin.edit-kenpin-seitai')->extends('layouts.master');
    }
}
