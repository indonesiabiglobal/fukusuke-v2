<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Models\MsBuyer;
use App\Models\MsLakbanInfure;
use App\Models\MsLakbanSeitai;
use App\Models\MsProduct;
use App\Models\MsWarnaLPK;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddMasterProduk extends Component
{
    // data master
    public $photoKatanuki;
    public $masterProductType;
    public $masterMaterial;
    public $masterEmbossed;
    public $masterSurface;
    public $masterGazetteClassifications;
    public $masterGentanClassifications;
    public $masterKatanuki;
    public $masterPrintType;
    public $masterInkCharacteristics;
    public $masterEndlessPrinting;
    public $masterArahGulung;
    public $masterKlasifikasiSeal;
    public $masterPackagingGaiso;
    public $masterPackagingBox;
    public $masterPackagingInner;
    public $masterPackagingLayer;
    public $masterUnit;
    public $masterLakbanInfure;
    public $masterLakbanSeitai;
    public $masterStampleSeitai;
    public $masterHagataSeitai;
    public $masterJenisSealSeitai;
    public $masterWarnaLPK;

    // data add produk
    public $code;
    public $name;
    public $product_type_id;
    public $code_alias;
    public $codebarcode;
    public $ketebalan;
    public $diameterlipat;
    public $productlength;
    public $unit_weight;
    public $product_unit;
    public $inflation_thickness;
    public $inflation_fold_diameter;
    public $one_winding_m_number;
    public $material_classification;
    public $embossed_classification;
    public $surface_classification;
    public $coloring_1;
    public $coloring_2;
    public $coloring_3;
    public $coloring_4;
    public $coloring_5;
    public $inflation_notes;
    public $gentan_classification;
    public $gazette_classification;
    public $gazette_dimension_a;
    public $gazette_dimension_b;
    public $gazette_dimension_c;
    public $gazette_dimension_d;
    public $katanuki_id;
    public $extracted_dimension_a;
    public $extracted_dimension_b;
    public $extracted_dimension_c;
    public $number_of_color;
    public $color_spec_1;
    public $color_spec_2;
    public $color_spec_3;
    public $color_spec_4;
    public $color_spec_5;
    public $back_color_number;
    public $back_color_1;
    public $back_color_2;
    public $back_color_3;
    public $back_color_4;
    public $back_color_5;
    public $print_type;
    public $ink_characteristic;
    public $endless_printing;
    public $winding_direction_of_the_web;
    public $seal_classification;
    public $custom_seal_classification;
    public $from_seal_design;
    public $lower_sealing_length;
    public $palet_jumlah_baris;
    public $palet_isi_baris;
    public $pack_gaiso_id;
    public $pack_box_id;
    public $pack_inner_id;
    public $pack_layer_id;
    public $manufacturing_summary;
    public $case_gaiso_count;
    public $case_gaiso_count_unit;
    public $case_box_count;
    public $case_box_count_unit;
    public $case_inner_count;
    public $case_inner_count_unit;
    public $lakbanseitaiid;
    public $custom_lakban_infure;
    public $custom_lakban_seitai;
    public $lakbaninfureid;
    public $hagataseitaiid;
    public $jenissealseitaiid;
    public $warnalpkid;
    public $custom_warna_lpk;
    public $kodehagata;
    public $kode_plate;
    public $case_box_stampel;
    public $case_gaiso_stampel;
    public $case_inner_stampel;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'product_type_id' => 'required',
        'code_alias' => 'required',
        'codebarcode' => 'required',
        'warnalpkid' => 'required',
        'inflation_thickness' => 'required',
        'inflation_fold_diameter' => 'required',
        'one_winding_m_number' => 'required',
        'material_classification' => 'required',
        'embossed_classification' => 'required',
        'surface_classification' => 'required',
        'lakbaninfureid' => 'required',
        'gentan_classification' => 'required',
        'gazette_classification' => 'required',
        'gazette_dimension_a' => 'required',
        'gazette_dimension_b' => 'required',
        'gazette_dimension_c' => 'required',
        'gazette_dimension_d' => 'required',
        'seal_classification' => 'required',
        'palet_jumlah_baris' => 'required',
        'palet_isi_baris' => 'required',
        'lakbanseitaiid' => 'required',
        'katanuki_id' => 'required',
        'kodehagata' => 'required',
        'extracted_dimension_a' => 'required',
        'extracted_dimension_b' => 'required',
        'pack_box_id' => 'required',
        'ketebalan' => 'required',
        'diameterlipat' => 'required',
        'productlength' => 'required',
        'unit_weight' => 'required',
        'product_unit' => 'required',
        'print_type' => 'required',
        'ink_characteristic' => 'required',
        'endless_printing' => 'required',
        'winding_direction_of_the_web' => 'required',
        'kode_plate' => 'nullable',
        'case_box_count' => 'required',
        'case_box_count_unit' => 'required',
        'case_box_stampel' => 'required',
    ];

    protected $messages = [
        'case_gaiso_count.required_unless' => 'Jumlah Gaiso tidak boleh kosong.',

        'code.required' => 'Nomor Order tidak boleh kosong.',
        'name.required' => 'Nama Produk tidak boleh kosong.',
        'product_type_id.required' => 'Kode Tipe tidak boleh kosong.',
        'code_alias.required' => 'Kode Produk (Alias) tidak boleh kosong.',
        'codebarcode.required' => 'Code Barcode tidak boleh kosong.',
        'warnalpkid.required' => 'Warna LPK tidak boleh kosong.',
        'inflation_thickness.required' => 'Infure Dimensi Tebal tidak boleh kosong.',
        'inflation_fold_diameter.required' => 'Infure Dimensi Lebar tidak boleh kosong.',
        'one_winding_m_number.required' => 'Panjang Gulung tidak boleh kosong.',
        'material_classification.required' => 'Klasifikasi Bahan tidak boleh kosong.',
        'embossed_classification.required' => 'Klasifikasi Emboss tidak boleh kosong.',
        'surface_classification.required' => 'Corona tidak boleh kosong.',
        'lakbaninfureid.required' => 'Lakban Infure tidak boleh kosong.',
        'gentan_classification.required' => 'Gentan tidak boleh kosong.',
        'gazette_classification.required' => 'Gazette tidak boleh kosong.',
        'gazette_dimension_a.required' => 'GZ Dimensi A tidak boleh kosong.',
        'gazette_dimension_b.required' => 'GZ Dimensi B tidak boleh kosong.',
        'gazette_dimension_c.required' => 'GZ Dimensi C tidak boleh kosong.',
        'gazette_dimension_d.required' => 'GZ Dimensi D tidak boleh kosong.',
        'seal_classification.required' => 'Klasifikasi Seal tidak boleh kosong.',
        'palet_jumlah_baris.required' => 'Jumlah Baris Palet tidak boleh kosong.',
        'palet_isi_baris.required' => 'Isi Baris Palet tidak boleh kosong.',
        'lakbanseitaiid.required' => 'Lakban Seitai tidak boleh kosong.',
        'katanuki_id.required' => 'Tipe Hagata tidak boleh kosong.',
        'kodehagata.required' => 'Kode Hagata tidak boleh kosong.',
        'extracted_dimension_a.required' => 'Dimensi A Ekstraksi tidak boleh kosong.',
        'extracted_dimension_b.required' => 'Dimensi B Ekstraksi tidak boleh kosong.',
        'pack_box_id.required' => 'Kemasan Box tidak boleh kosong.',
        'ketebalan.required' => 'Dimensi Tebal tidak boleh kosong.',
        'diameterlipat.required' => 'Dimensi Lebar tidak boleh kosong.',
        'productlength.required' => 'Dimensi Panjang tidak boleh kosong.',
        'unit_weight.required' => 'Berat Satuan tidak boleh kosong.',
        'product_unit.required' => 'Satuan Produk tidak boleh kosong.',
        'print_type.required' => 'Jenis Cetak tidak boleh kosong.',
        'ink_characteristic.required' => 'Sifat Tinta tidak boleh kosong.',
        'endless_printing.required' => 'Endless Printing tidak boleh kosong.',
        'winding_direction_of_the_web.required' => 'Arah Gulung tidak boleh kosong.',
        // 'kode_plate.required' => 'Kode Plate tidak boleh kosong.',
        'case_box_count.required' => 'Jumlah Box tidak boleh kosong.',
        'case_box_count_unit.required' => 'Satuan Box tidak boleh kosong.',
        'case_box_stampel.required' => 'Stampel Box tidak boleh kosong.',
        'case_gaiso_count.required' => 'Jumlah Gaiso tidak boleh kosong.',
        'case_gaiso_count_unit.required' => 'Satuan Gaiso tidak boleh kosong.',
        'case_gaiso_stampel.required' => 'Stampel Gaiso tidak boleh kosong.',
        'case_inner_count.required' => 'Jumlah Inner tidak boleh kosong.',
        'case_inner_count_unit.required' => 'Satuan Inner tidak boleh kosong.',
        'case_inner_stampel.required' => 'Stampel Inner tidak boleh kosong.',
    ];

    public function mount()
    {
        $this->masterProductType = DB::table('msproduct_type')
            ->select('id', 'code', DB::raw("CONCAT(name, ', ', code) as name"))
            ->get();
        $this->masterMaterial = DB::table('msmaterial')->get(['id', 'code', 'name']);
        $this->masterEmbossed = DB::table('msembossedclassification')->get(['id', 'code', 'name']);
        $this->masterSurface = DB::table('mssurfaceclassification')->get(['id', 'code', 'name']);
        $this->masterUnit = DB::table('msunit')->get(['id', 'code', 'name']);
        $this->masterGazetteClassifications = DB::table('msgazetteclassification')->get(['id', 'code', 'name']);
        $this->masterGentanClassifications = DB::table('msgentanclassification')->get(['id', 'code', 'name']);
        $this->masterKatanuki = DB::table('mskatanuki')->get(['id', 'code', 'name']);
        $this->masterPrintType = DB::table('msjeniscetak')->get(['id', 'code', 'name']);
        $this->masterInkCharacteristics = DB::table('mssifattinta')->get(['id', 'code', 'name']);
        $this->masterEndlessPrinting = DB::table('msendless')->get(['id', 'code', 'name']);
        $this->masterArahGulung = DB::table('msarahgulung')->get(['id', 'code', 'name']);
        $this->masterKlasifikasiSeal = DB::table('msklasifikasiseal')->get(['id', 'code', 'name']);
        $this->masterPackagingGaiso = DB::table('mspackaginggaiso')->get(['id', 'code', 'name', 'box_class']);
        $this->masterPackagingBox = DB::table('mspackagingbox')->get(['id', 'code', 'name', 'box_class']);
        $this->masterPackagingInner = DB::table('mspackaginginner')->get(['id', 'code', 'name', 'box_class']);
        $this->masterPackagingLayer = DB::table('mspackaginglayer')->get(['id', 'code', 'name', 'box_class']);
        $this->masterLakbanInfure = DB::table('mslakbaninfure')->get(['id', 'code', 'name']);
        $this->masterLakbanSeitai = DB::table('mslakbanseitai')->get(['id', 'code', 'name']);
        $this->masterStampleSeitai = DB::table('msstampleseitai')->get(['id', 'code', 'name']);
        $this->masterHagataSeitai = DB::table('mshagataseitai')->get(['id', 'code', 'name']);
        $this->masterJenisSealSeitai = DB::table('msjenissealseitai')->get(['id', 'code', 'name']);
        $this->masterWarnaLPK = DB::table('mswarnalpk')->get();
    }

    public function store()
    {
        try {
            if ($this->pack_gaiso_id !== null && $this->pack_gaiso_id['value'] !== "") {
                $this->rules['case_gaiso_count'] = 'required';
                $this->rules['case_gaiso_count_unit'] = 'required';
                $this->rules['case_gaiso_stampel'] = 'required';
            }

            if ($this->pack_inner_id !== null && $this->pack_inner_id['value'] !== "") {
                $this->rules['case_inner_count'] = 'required';
                $this->rules['case_inner_count_unit'] = 'required';
                $this->rules['case_inner_stampel'] = 'required';
            }

            $validatedData = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => $e->validator->errors()->first()]);
            return;
        }

        try {
            DB::beginTransaction();

            $product = new MsProduct();
            $product->code = isset($this->code) ? $this->code : null;
            $product->name = isset($this->name) ? $this->name : null;
            $productType = DB::table('msproduct_type')->where('id', $this->product_type_id['value'])->first();
            $product->product_type_id = $productType->id;
            $product->product_type_code = $productType->code;
            $product->code_alias = isset($this->code_alias) ? $this->code_alias : null;
            $product->codebarcode = isset($this->codebarcode) ? $this->codebarcode : null;
            $product->ketebalan = isset($this->ketebalan)  ? $this->ketebalan : null;
            $product->diameterlipat = isset($this->diameterlipat) ? $this->diameterlipat : null;
            $product->productlength = isset($this->productlength) ? $this->productlength : null;
            $product->unit_weight = isset($this->unit_weight) ? $this->unit_weight : null;

            if (isset($this->product_unit)) {
                $produkUnit = DB::table('msunit')->where('id', $this->product_unit['value'])->first();
                $product->product_unit_id = $produkUnit->id;
                $product->product_unit = $produkUnit->code;
            }

            $product->inflation_thickness = isset($this->inflation_thickness) ? $this->inflation_thickness : null;
            $product->inflation_fold_diameter = isset($this->inflation_fold_diameter) ? $this->inflation_fold_diameter : null;
            $product->one_winding_m_number = isset($this->one_winding_m_number) ? $this->one_winding_m_number : null;

            if (isset($this->material_classification)) {
                $material = DB::table('msmaterial')->where('id', $this->material_classification['value'])->first();
                $product->material_classification_id = $material->id;
                $product->material_classification = $material->code;
            }

            if (isset($this->embossed_classification)) {
                $embossed = DB::table('msembossedclassification')->where('id', $this->embossed_classification['value'])->first();
                $product->embossed_classification_id = $embossed->id;
                $product->embossed_classification = $embossed->code;
            }

            if (isset($this->surface_classification)) {
                $surface = DB::table('mssurfaceclassification')->where('id', $this->surface_classification['value'])->first();
                $product->surface_classification_id = $surface->id;
                $product->surface_classification = $surface->code;
            }

            $product->coloring_1 = isset($this->coloring_1) ? $this->coloring_1 : null;
            $product->coloring_2 = isset($this->coloring_2) ? $this->coloring_2 : null;
            $product->coloring_3 = isset($this->coloring_3) ? $this->coloring_3 : null;
            $product->coloring_4 = isset($this->coloring_4) ? $this->coloring_4 : null;
            $product->coloring_5 = isset($this->coloring_5) ? $this->coloring_5 : null;
            $product->inflation_notes = isset($this->inflation_notes) ? $this->inflation_notes : null;

            if (isset($this->gentan_classification)) {
                $gentan = DB::table('msgentanclassification')->where('id', $this->gentan_classification['value'])->first();
                $product->gentan_classification_id = $gentan->id;
                $product->gentan_classification = $gentan->code;
            }

            if (isset($this->gazette_classification)) {
                $gazette = DB::table('msgazetteclassification')->where('id', $this->gazette_classification['value'])->first();
                $product->gazette_classification_id = $gazette->id;
                $product->gazette_classification = $gazette->code;
            }

            $product->gazette_dimension_a = isset($this->gazette_dimension_a) ? $this->gazette_dimension_a : null;
            $product->gazette_dimension_b = isset($this->gazette_dimension_b) ? $this->gazette_dimension_b : null;
            $product->gazette_dimension_c = isset($this->gazette_dimension_c) ? $this->gazette_dimension_c : null;
            $product->gazette_dimension_d = isset($this->gazette_dimension_d) ? $this->gazette_dimension_d : null;
            $product->katanuki_id = isset($this->katanuki_id) ? $this->katanuki_id['value'] : null;
            $product->extracted_dimension_a = isset($this->extracted_dimension_a)   ? $this->extracted_dimension_a : null;
            $product->extracted_dimension_b = isset($this->extracted_dimension_b)   ? $this->extracted_dimension_b : null;
            $product->extracted_dimension_c = isset($this->extracted_dimension_c)  ? $this->extracted_dimension_c : null;
            $product->number_of_color = isset($this->number_of_color) ? $this->number_of_color : null;
            $product->color_spec_1 = isset($this->color_spec_1)     ? $this->color_spec_1 : null;
            $product->color_spec_2 = isset($this->color_spec_2)    ? $this->color_spec_2 : null;
            $product->color_spec_3 = isset($this->color_spec_3)   ? $this->color_spec_3 : null;
            $product->color_spec_4 = isset($this->color_spec_4) ? $this->color_spec_4 : null;
            $product->color_spec_5 = isset($this->color_spec_5) ? $this->color_spec_5 : null;
            $product->back_color_number = isset($this->back_color_number) ? $this->back_color_number : null;
            $product->back_color_1 = isset($this->back_color_1) ? $this->back_color_1 : null;
            $product->back_color_2 = isset($this->back_color_2) ? $this->back_color_2 : null;
            $product->back_color_3 = isset($this->back_color_3) ? $this->back_color_3 : null;
            $product->back_color_4 = isset($this->back_color_4) ? $this->back_color_4 : null;
            $product->back_color_5 = isset($this->back_color_5) ? $this->back_color_5 : null;

            if (isset($this->print_type)) {
                $printType = DB::table('msjeniscetak')->where('id', $this->print_type['value'])->first();
                $product->print_type_id = $printType->id;
                $product->print_type = $printType->code;
            }

            if (isset($this->ink_characteristic)) {
                $inkCharacteristic = DB::table('mssifattinta')->where('id', $this->ink_characteristic['value'])->first();
                $product->ink_characteristic_id = $inkCharacteristic->id;
                $product->ink_characteristic = $inkCharacteristic->code;
            }

            if (isset($this->endless_printing)) {
                $endlessPrinting = DB::table('msendless')->where('id', $this->endless_printing['value'])->first();
                $product->endless_printing_id = $endlessPrinting->id;
                $product->endless_printing = $endlessPrinting->code;
            }

            if (isset($this->winding_direction_of_the_web)) {
                $windingDirection = DB::table('msarahgulung')->where('id', $this->winding_direction_of_the_web['value'])->first();
                $product->winding_direction_of_the_web_id = $windingDirection->id;
                $product->winding_direction_of_the_web = $windingDirection->code;
            }

            if (isset($this->seal_classification)) {
                if ($this->seal_classification['value'] == 'lainnya') {
                    // insert new seal classification
                    $maxCode = DB::table('msklasifikasiseal')->max('code');
                    $sealClassification = DB::table('msklasifikasiseal')->insertGetId([
                        'code' => $maxCode + 1,
                        'name' => $this->custom_seal_classification,
                        'status' => 1,
                        'created_by' => auth()->user()->username,
                        'created_on' => Carbon::now(),
                        'updated_by' => auth()->user()->username,
                        'updated_on' => Carbon::now(),
                    ]);
                    $product->seal_classification_id = $sealClassification;
                    $product->seal_classification = $sealClassification;
                } else {
                    $sealClassification = DB::table('msklasifikasiseal')->where('id', $this->seal_classification['value'])->first();
                    $product->seal_classification_id = $sealClassification->id;
                    $product->seal_classification = $sealClassification->code;
                }
            }

            $product->from_seal_design = isset($this->from_seal_design) ? $this->from_seal_design : null;
            $product->lower_sealing_length = isset($this->lower_sealing_length) ? $this->lower_sealing_length : null;
            $product->palet_jumlah_baris = isset($this->palet_jumlah_baris) ? $this->palet_jumlah_baris : null;
            $product->palet_isi_baris = isset($this->palet_isi_baris)   ? $this->palet_isi_baris : null;
            $product->pack_gaiso_id = isset($this->pack_gaiso_id) ? $this->pack_gaiso_id['value'] : null;;
            $product->pack_box_id = isset($this->pack_box_id) ? $this->pack_box_id['value'] : null;
            $product->pack_inner_id = isset($this->pack_inner_id) ? $this->pack_inner_id['value'] : null;;
            $product->pack_layer_id = isset($this->pack_layer_id) ? $this->pack_layer_id['value'] : null;;
            $product->manufacturing_summary = isset($this->manufacturing_summary)   ? $this->manufacturing_summary : null;
            $product->case_gaiso_count = isset($this->case_gaiso_count) ? $this->case_gaiso_count : null;
            $product->case_gaiso_count_unit = isset($this->case_gaiso_count_unit) ? $this->case_gaiso_count_unit['value'] : null;
            $product->case_gaiso_stampel = isset($this->case_gaiso_stampel) ? $this->case_gaiso_stampel : null;
            $product->case_box_count = isset($this->case_box_count) ? $this->case_box_count : null;
            $product->case_box_count_unit = isset($this->case_box_count_unit) ? $this->case_box_count_unit['value'] : null;
            $product->case_box_stampel = isset($this->case_box_stampel) ? $this->case_box_stampel : null;
            $product->case_inner_count = isset($this->case_inner_count) ? $this->case_inner_count : null;
            $product->case_inner_count_unit = isset($this->case_inner_count_unit) ? $this->case_inner_count_unit['value'] : null;
            $product->case_inner_stampel = isset($this->case_inner_stampel) ? $this->case_inner_stampel : null;

            // kakban infure
            if (isset($this->lakbaninfureid) && $this->lakbaninfureid['value'] != null) {
                if ($this->lakbaninfureid['value'] == 'lainnya') {
                    // insert new seal classification
                    $maxCode = MsLakbanInfure::max('code');
                    $lakbanInfure = MsLakbanInfure::insertGetId([
                        'code' => str_pad($maxCode + 1, 2, '0', STR_PAD_LEFT),
                        'name' => $this->custom_lakban_infure,
                        'status' => 1,
                        'created_by' => auth()->user()->username,
                        'created_on' => Carbon::now(),
                        'updated_by' => auth()->user()->username,
                        'updated_on' => Carbon::now(),
                    ]);
                    $product->lakbaninfureid = $lakbanInfure;
                } else {
                    $product->lakbaninfureid = $this->lakbaninfureid['value'];
                }
            }
            // lakban seitai
            if (isset($this->lakbanseitaiid) && $this->lakbanseitaiid['value'] != null) {
                if ($this->lakbanseitaiid['value'] == 'lainnya') {
                    // insert new seal classification
                    $maxCode = MsLakbanSeitai::max('code');
                    $lakbanSeitai = MsLakbanSeitai::insertGetId([
                        'code' => str_pad($maxCode + 1, 2, '0', STR_PAD_LEFT),
                        'name' => $this->custom_lakban_seitai,
                        'status' => 1,
                        'created_by' => auth()->user()->username,
                        'created_on' => Carbon::now(),
                        'updated_by' => auth()->user()->username,
                        'updated_on' => Carbon::now(),
                    ]);
                    $product->lakbanseitaiid = $lakbanSeitai;
                } else {
                    $product->lakbanseitaiid = $this->lakbanseitaiid['value'];
                }
            }
            $product->lakbaninfureid = isset($this->lakbaninfureid) ? $this->lakbaninfureid['value'] : null;;
            $product->kodehagata = isset($this->kodehagata) ? $this->kodehagata : null;
            $product->warnalpkid = isset($this->warnalpkid) ? $this->warnalpkid['value'] : null;
            $product->kode_plate = isset($this->kode_plate) ? $this->kode_plate : null;

            // warna LPK
            if (isset($this->warnalpkid) && $this->warnalpkid['value'] != null) {
                if ($this->warnalpkid['value'] == 'lainnya') {
                    // insert new seal classification
                    $maxCode = MsWarnaLPK::max('code');
                    $warnaLPK = MsWarnaLPK::insertGetId([
                        'code' => str_pad($maxCode + 1, 2, '0', STR_PAD_LEFT),
                        'name' => $this->custom_warna_lpk,
                        'status' => 1,
                        'created_by' => auth()->user()->username,
                        'created_on' => Carbon::now(),
                        'updated_by' => auth()->user()->username,
                        'updated_on' => Carbon::now(),
                    ]);
                    $product->warnalpkid = $warnaLPK;
                } else {
                    $product->warnalpkid = $this->warnalpkid['value'];
                }
            }
            // $product->jenissealseitaiid = isset($this->jenissealseitaiid) ? $this->jenissealseitaiid['value'] : null;;
            $product->status = 1;
            $product->created_by = auth()->user()->username;
            $product->created_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->updated_on = Carbon::now();
            $product->save();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Product created successfully.']);
            return redirect()->route('product');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Product: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Product: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('product');
    }

    public function render()
    {
        if (isset($this->katanuki_id) && $this->katanuki_id != '') {
            $katanuki_id = is_array($this->katanuki_id) ? $this->katanuki_id['value'] : $this->katanuki_id;
            $this->photoKatanuki = DB::table('mskatanuki')->where('id', $katanuki_id)->first()->filename;
        }
        return view('livewire.master-tabel.produk.add-master-produk')->extends('layouts.master');
    }
}
