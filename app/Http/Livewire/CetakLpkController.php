<?php

namespace App\Http\Livewire;

use App\Models\TdOrderLpk;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CetakLpkController extends Component
{
    public $lpk_no;
    public $lpk_id;
    public $lpk_date;
    public $code;
    public $product_name;
    public $qty_lpk;
    public $reprint_no;
    public $results;

    public function print()
    {
        // $data = collect(DB::select("
        //         SELECT tdol.lpk_no,tdo.po_no,tdo.order_date, tdo.stufingdate,tdo.order_qty,tdol.qty_lpk,mwl.name as warnalpk,
        //         tdol.panjang_lpk,mm.machineno as nomesin,mp.codebarcode,
        //         mp.id, mp.name as product_name,mp.code_alias,
        //         mpt.code ||','|| mpt.name as kodetipe,mp.ketebalan as t, mp.diameterlipat as l, mp.productlength as p,
        //         mp.ketebalan ||'x'||mp.diameterlipat||'x'||mp.productlength as dimensi_txlxp, 
        //         mp.unit_weight as beratsatuan,mp.inflation_thickness ||'x'||mp.inflation_fold_diameter as infure_dimensi,
        //         mp.one_winding_m_number as infure_panjanggulung,mp.material_classification as infure_material,
        //         mp.embossed_classification as infure_embose,mp.surface_classification as infure_corona,
        //         mp.coloring_1 as infure_mb1_masterbatch,
        //         mp.coloring_2 as infure_mb2, mp.coloring_3 as infure_mb3,mp.coloring_4 as infure_mb4,mp.coloring_5 as infure_mb5,
        //         mp.inflation_notes as infure_catatan,
        //         mp.gentan_classification as infure_gentan,mp.gazette_classification as infure_gazette,
        //         mp.gazette_dimension_a as infure_gz_dimensi_a,mp.gazette_dimension_b as infure_gz_dimensi_b,
        //         mp.gazette_dimension_c as infure_gz_dimensi_c,mp.gazette_dimension_d as infure_gz_dimensi_d,
        //         mk.code ||','||mk.name as hagata_kodenukigata,mp.extracted_dimension_a as hagata_a,
        //         mp.extracted_dimension_b as hagata_b,mp.extracted_dimension_c as hagata_c,
        //         mp.number_of_color as printing_warnadepan,mp.color_spec_1 as printing_warnadepan1,mp.color_spec_2 as printing_warnadepan2,mp.color_spec_3 as printing_warnadepan3, 
        //         mp.back_color_number as printing_warnabelakang,mp.back_color_1 as printing_warnabelakang1,
        //         mp.back_color_2 as printing_warnabelakang2,mp.back_color_3 as printing_warnabelakang3,
        //         mp.print_type as printing_jeniscetak,mp.ink_characteristic as printing_sifattinta,mp.endless_printing as printing_endless,mp.winding_direction_of_the_web as printing_araggulungan,
        //         mp.seal_classification as seitai_klasifikasiseal,mp.from_seal_design as seitai_jaraksealdaripola,
        //         mp.lower_sealing_length as seitai_jaraksealbawah,mp.palet_jumlah_baris as seitai_jmlhbarispalet,
        //         mp.palet_isi_baris as seitai_isibarispalet,	mpb.code||','||mpb.name as seitai_kodebox,
        //         mp.case_box_count as seitai_isibox, 
        //         mpg.code||','||mpg.name as seitai_kodegaiso,mp.case_gaiso_count as seitai_isigaiso,
        //         mpi.code||','||mpi.name as seitai_kodeinner,mp.case_inner_count as seitai_isiinner,
        //         mpl.code||','||mpl.name as seitai_kodelayer,
        //         mp.manufacturing_summary as seitai_catatan 
        //         from tdorderlpk as tdol 
        //         inner join tdorder as tdo on tdo.id=tdol.order_id
        //         INNER JOIN msproduct as mp on mp.id=tdol.product_id
        //         INNER JOIN msproduct_type as mpt on mp.product_type_id=mpt.id
        //         INNER JOIN mskatanuki as mk on mp.katanuki_id=mk.id
        //         INNER JOIN mspackagingbox as mpb on mp.pack_box_id=mpb.id
        //         inner jOIN mspackaginggaiso as mpg on  mp.pack_gaiso_id=mpg.id
        //         left join mspackaginginner as mpi on mp.pack_inner_id=mpi.id
        //         left join mspackaginglayer as mpl on mp.pack_layer_id=mpl.id
        //         left join msmachine as mm on mm.id=tdol.machine_id
        //         left join mswarnalpk as mwl on mwl.id=tdol.warnalpkid
        //         left join mslakbaninfure as mli on mli.id=mp.lakbaninfureid
        //         left join mslakbanseitai as mls on mls.id=mp.lakbanseitaiid
        //         left join msstampleseitai as mss on mss.id=mp.stampelseitaiid
        //         left join mshagataseitai as mhs on mhs.id=mp.hagataseitaiid 
        //         where tdol.lpk_no='240704-046'
        // "))->first();
        // $ft=[];
        // $ft=$data;
        // dd($ft);

        $lpk_id= $this->lpk_id;
        // $this->emit('redirectToPrint', $lpk_id);
        $this->redirectRoute('report-lpk', ['lpk_id' => $this->lpk_id]);
    }
    
    public function render()
    {
        if(isset($this->lpk_no) && $this->lpk_no != ''){
            $data = DB::table('tdorderlpk as tod')
            ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->select(
                'tod.id as lpk_id',
                'tod.lpk_no',
                'tod.qty_lpk',
                'tod.lpk_date',
                'mp.code',
                'mp.name as product_name',
                'tod.reprint_no as reprint_no'
            )
            ->where('lpk_no', $this->lpk_no)
            ->first();
            if($data == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Mesin ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_id =  $data->lpk_id;
                $this->lpk_date = $data->lpk_date;
                $this->qty_lpk = $data->qty_lpk;
                $this->code = $data->code;
                $this->product_name = $data->product_name;
                $this->reprint_no = $data->reprint_no;
            }
        }
        // dd($this->results);
        return view('livewire.order-lpk.cetak-lpk', [
            'results' => $this->results,
        ])->extends('layouts.master');
    }
}
