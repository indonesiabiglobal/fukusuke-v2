<?php

namespace App\Http\Livewire\Kenpin;

use App\Models\MsProduct;
use App\Models\TdProductGoods;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class mutasiIsiPaletKenpinController extends Component
{
    public $searchOld;
    public $searchNew;
    public $data = [];
    public $result = [];
    public $dataMutasi = [];
    public $nomor_lot;
    public $machineno;
    public $production_date;
    public $qty_seitai;
    public $qty_mutasi;
    public $orderId;
    public $products;
    public $case_box_count;

    public function mount()
    {
        $this->products = MsProduct::get();
    }

    public function search()
    {
        if (isset($this->searchOld) && $this->searchOld != '') {
            $this->data = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machineno,
                tdpg.production_date,
                tdpg.qty_produksi / msp.case_box_count as qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct as msp on msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet = '$this->searchOld'");

            if ($this->data == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchOld . ' Tidak Terdaftar']);
            }
        }
    }

    public function searchTujuan()
    {
        if (isset($this->searchNew) && $this->searchNew != '') {
            $this->result = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machineno,
                tdpg.production_date,
                tdpg.qty_produksi / msp.case_box_count as qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct as msp on msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet = '$this->searchNew'");

            if ($this->result == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchNew . ' Tidak Terdaftar']);
            }
        }
    }

    public function import($id)
    {
        $paletSumber = DB::table('tdproduct_goods as tdpg')
            ->select([
                'tdpg.id',
                'tdpg.nomor_lot',
                'msm.machineno',
                'tdpg.production_date',
                'msp.case_box_count',
                'tdpg.qty_produksi as qty_produksi_old',
                DB::raw('tdpg.qty_produksi / msp.case_box_count as qty_produksi'),
                'tdpg.nomor_palet'
            ])
            ->join('msmachine as msm', 'msm.id', '=', 'tdpg.machine_id')
            ->join('msproduct as msp', 'msp.id', '=', 'tdpg.product_id')
            ->where('tdpg.id', $id)
            ->first();

        $this->orderId = $paletSumber->id;
        $this->searchOld = $paletSumber->nomor_palet;
        $this->nomor_lot = $paletSumber->nomor_lot;
        $this->qty_seitai = $paletSumber->qty_produksi;
        $this->qty_mutasi = $paletSumber->qty_produksi;
        $this->case_box_count = $paletSumber->case_box_count;
        $this->machineno = $paletSumber->machineno;
        $this->production_date = $paletSumber->production_date;

        if (!$this->searchNew) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Palet Tujuan ' . $this->searchNew . ' Tidak Terisi']);
        }
    }

    public function addMutasi()
    {
        if ($this->qty_mutasi == '' || $this->qty_mutasi == 0 || $this->qty_mutasi == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Box Mutasi Harus Diisi']);
            return;
        }
        if ($this->qty_mutasi > $this->qty_seitai) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Box Mutasi Tidak Boleh Melebihi Box Seitai']);
            return;
        }

        // check apakah palet tujuan ada
        if ($this->searchNew == '') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Palet Tujuan ' . $this->searchNew . ' Tidak Terisi']);
            $this->dispatch('closeModalMutasi');
            return;
        }

        // menambahkan data mutasi
        $this->dataMutasi[$this->orderId] = [
            'id' => $this->orderId,
            'nomor_lot' => $this->nomor_lot,
            'qty_produksi' => $this->qty_seitai,
            'qty_seitai' => $this->qty_seitai,
            'qty_mutasi' => $this->qty_mutasi,
            'machineno' => $this->machineno,
            'production_date' => $this->production_date,
            'case_box_count' => $this->case_box_count
        ];

        // menambahkan data mutasi ke dalam array result
        $this->result[] = (object)[
            'id' => $this->orderId,
            'nomor_lot' => $this->nomor_lot,
            'qty_produksi' => $this->qty_mutasi,
            'machineno' => $this->machineno,
            'production_date' => $this->production_date,
            'case_box_count' => $this->case_box_count
        ];

        $orderId = $this->orderId;
        // mengurangi data box pada palet sumber di table
        $dataByIndex = array_filter($this->data, function ($item) use ($orderId) {
            return $item->id == $orderId;
        });
        $dataIndex = key($dataByIndex);
        $this->data[$dataIndex]->qty_produksi = $this->data[$dataIndex]->qty_produksi - $this->qty_mutasi;
        $this->qty_seitai = $this->qty_seitai - $this->qty_mutasi;

        // menghapus data di data palet sumber jika qty_seitai == 0
        if ($this->qty_seitai == 0) {
            unset($this->data[$dataIndex]);
        }

        $this->dispatch('closeModalMutasi');
    }

    public function saveMutasi()
    {
        foreach ($this->dataMutasi as $key => $value) {
            if ($value['qty_seitai'] == $value['qty_mutasi']) {
                $save = TdProductGoods::where('id', $value['id'])->update([
                    'nomor_palet' => $this->searchNew
                ]);
            } else {
                $save = TdProductGoods::where('id', $value['id'])->update([
                    'nomor_palet' => $this->searchOld,
                    'qty_produksi' => ((int)$value['qty_seitai'] * (int)$value['case_box_count']) - ((int)$value['qty_mutasi'] * (int)$value['case_box_count'])
                ]);

                $data = TdProductGoods::where('id', $value['id'])->first();

                $datas = new TdProductGoods();
                $datas->production_no = $data['production_no'];
                $datas->production_date = $data['production_date'];
                $datas->employee_id = $data['employee_id'];
                $datas->employee_id_infure = $data['employee_id_infure'];
                $datas->work_shift = $data['work_shift'];
                $datas->work_hour = $data['work_hour'];
                $datas->machine_id = $data['machine_id'];
                $datas->lpk_id = $data['lpk_id'];
                $datas->product_id = $data['product_id'];
                $datas->qty_produksi = (int)$value['qty_mutasi'] * (int)$value['case_box_count'];
                $datas->seitai_berat_loss = $data['seitai_berat_loss'];
                $datas->infure_berat_loss = $data['infure_berat_loss'];
                $datas->seq_no = $data['seq_no'];
                $datas->nomor_palet = $this->searchNew;
                $datas->nomor_lot = $data['nomor_lot'];
                $datas->status_production = $data['status_production'];
                $datas->status_warehouse = $data['status_warehouse'];
                $datas->kenpin_qty_loss = $data['kenpin_qty_loss'];
                $datas->kenpin_qty_loss_proses = $data['kenpin_qty_loss_proses'];
                $datas->created_by = $data['created_by'];
                $datas->created_on = $data['created_on'];
                $datas->updated_by = $data['updated_by'];
                $datas->updated_on = $data['updated_on'];
                $datas->save();
            }
        }

        if ($save) {
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function undo()
    {
        foreach ($this->dataMutasi as $key => $value) {
            $id = $value['id'];
            // menambahkan kembali data yang dimutasi ke palet sumber
            $dataByIndex = array_filter($this->data, function ($item) use ($id) {
                return $item->id == $id;
            });
            if (count($dataByIndex) > 0) {
                $dataIndex = key($dataByIndex);

                $this->data[$dataIndex]->qty_produksi = $this->data[$dataIndex]->qty_produksi + $value['qty_mutasi'];
            } else {
                $this->data[] = (object)[
                    'id' => $value['id'],
                    'nomor_lot' => $value['nomor_lot'],
                    'qty_produksi' => $value['qty_mutasi'],
                    'machineno' => $value['machineno'],
                    'production_date' => $value['production_date'],
                    'case_box_count' => $value['case_box_count']
                ];
            }

            // menghapus data mutasi dari palet tujuan
            $resultByIndex = array_filter($this->result, function ($item) use ($id) {
                return $item->id == $id;
            });
            $resultIndex = key($resultByIndex);

            // Jika ditemukan, hapus elemen dari array
            if ($resultByIndex !== false) {
                unset($this->result[$resultIndex]);
            }

            // Untuk memastikan array tetap terurut setelah penghapusan
            $this->result = array_values($this->result);
        }
    }

    public function delete()
    {
        $save = TdProductGoods::where('id',$this->orderId)->update([
            'nomor_palet'=>$this->searchOld
        ]);

        if($save){
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function cancel(){
        $this->searchOld='';
        $this->searchNew='';
        $this->data=[];
        $this->result=[];
        $this->nomor_lot='';
        $this->qty_seitai='';
        $this->qty_mutasi='';
    }

    public function render()
    {
        return view('livewire.nippo-seitai.mutasi-isi-palet')->extends('layouts.master');
    }
}
