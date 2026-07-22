<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Helpers\phpspreadsheet;
use App\Models\MsProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Traits\HandlesHeavyJob;
use Livewire\Attributes\Session;

class MasterProduk extends Component
{
    use HandlesHeavyJob;
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $products;
    public $searchTerm;
    public $product_type_id;
    public $idUpdate;
    public $idDelete;
    public $paginate = 10;
    #[Session]
    public $sortingTable;

    public function mount()
    {
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
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
        // $this->render();
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            MsProduct::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now()
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the buyer: ' . $e->getMessage()]);
        }
    }


    public function export()
    {
        $this->startHeavyJob();
        $response = $this->exportProduct();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportProduct()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER PRODUK - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            // Basic
            'No', 'Nomor Order', 'Kode Produk (Alias)', 'Code Barcode',
            'Kode Tipe Produk', 'Nama Tipe Produk', 'Jenis Produk',
            'Kode Satuan', 'Nama Satuan', 'Berat Satuan',
            'Tebal (T)', 'Lebar (L)', 'Panjang (P)',
            'ID Warna LPK', 'Nama Warna LPK',
            // INFURE
            'Dim. Infure T', 'Dim. Infure L', 'Panjang Gulung',
            'Kode Material', 'Nama Material',
            'Kode Embos', 'Nama Embos',
            'Kode Corona', 'Nama Corona',
            'Kode Lakban Infure', 'Nama Lakban Infure',
            'MB-1', 'MB-2', 'MB-3', 'MB-4', 'MB-5', 'Catatan Infure',
            // Gentan/Gazette
            'Kode Gentan', 'Nama Gentan',
            'Kode Gazette', 'Nama Gazette',
            'GZ Dim A', 'GZ Dim B', 'GZ Dim C', 'GZ Dim D',
            // PRINTING
            'Jml Warna Depan',
            'Spec Warna 1', 'Spec Warna 2', 'Spec Warna 3', 'Spec Warna 4', 'Spec Warna 5',
            'Jml Warna Belakang',
            'Spec Belakang 1', 'Spec Belakang 2', 'Spec Belakang 3', 'Spec Belakang 4', 'Spec Belakang 5',
            'Kode Jenis Cetak', 'Nama Jenis Cetak',
            'Kode Sifat Tinta', 'Nama Sifat Tinta',
            'Kode Endless', 'Nama Endless',
            'Kode Arah Gulung', 'Nama Arah Gulung',
            'Kode Plate',
            // SEITAI
            'Kode Klas. Seal', 'Nama Klas. Seal',
            'Jarak Seal dari Pola', 'Jarak Seal Bawah',
            'Jml Baris Palet', 'Isi Baris Palet',
            'Kode Lakban Seitai', 'Nama Lakban Seitai',
            'Kode Gaiso', 'Nama Gaiso',
            'Kode Box', 'Nama Box',
            'Kode Inner', 'Nama Inner',
            'Kode Layer', 'Nama Layer',
            'Isi Gaiso', 'Satuan Gaiso', 'Stempel Gaiso',
            'Isi Box', 'Satuan Box', 'Stempel Box',
            'Isi Inner', 'Satuan Inner', 'Stempel Inner',
            // HAGATA
            'Kode Hagata (Tipe)', 'Nama Hagata (Tipe)',
            'Kode Hagata', 'Dim Hagata A', 'Dim Hagata B', 'Dim Hagata C',
            'Catatan Produksi',
            // Status
            'Status', 'Updated By', 'Updated On',
        ];

        $lastColumn = 'A';
        foreach ($header as $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $lastColumn = $columnHeaderEnd;
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('A3');
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);

        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $lastColumn . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $lastColumn . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $lastColumn . $rowHeaderStart);

        $data = DB::table('msproduct as msp')
            ->leftJoin('msproduct_type as mspt', 'msp.product_type_id', '=', 'mspt.id')
            ->leftJoin('msproduct_group as mspg', 'mspt.product_group_id', '=', 'mspg.id')
            ->leftJoin('msunit as msu', 'msp.product_unit_id', '=', 'msu.id')
            ->leftJoin('mswarnalpk as mswlpk', 'mswlpk.id', '=', 'msp.warnalpkid')
            ->leftJoin('msmaterial as msmat', 'msmat.id', '=', 'msp.material_classification_id')
            ->leftJoin('msembossedclassification as msemb', 'msemb.id', '=', 'msp.embossed_classification_id')
            ->leftJoin('mssurfaceclassification as mssurf', 'mssurf.id', '=', 'msp.surface_classification_id')
            ->leftJoin('mslakbaninfure as mslbi', 'mslbi.id', '=', 'msp.lakbaninfureid')
            ->leftJoin('msgentanclassification as msgent', 'msgent.id', '=', 'msp.gentan_classification_id')
            ->leftJoin('msgazetteclassification as msgaz', 'msgaz.id', '=', 'msp.gazette_classification_id')
            ->leftJoin('msjeniscetak as msjc', 'msjc.id', '=', 'msp.print_type_id')
            ->leftJoin('mssifattinta as msst', 'msst.id', '=', 'msp.ink_characteristic_id')
            ->leftJoin('msendless as mse', 'mse.id', '=', 'msp.endless_printing_id')
            ->leftJoin('msarahgulung as msag', 'msag.id', '=', 'msp.winding_direction_of_the_web_id')
            ->leftJoin('msklasifikasiseal as mskls', 'mskls.id', '=', 'msp.seal_classification_id')
            ->leftJoin('mslakbanseitai as mslbs', 'mslbs.id', '=', 'msp.lakbanseitaiid')
            ->leftJoin('mspackaginggaiso as mspkg', 'mspkg.id', '=', 'msp.pack_gaiso_id')
            ->leftJoin('mspackagingbox as mspkb', 'mspkb.id', '=', 'msp.pack_box_id')
            ->leftJoin('mspackaginginner as mspki', 'mspki.id', '=', 'msp.pack_inner_id')
            ->leftJoin('mspackaginglayer as mspkl', 'mspkl.id', '=', 'msp.pack_layer_id')
            ->leftJoin('msunit as msug', 'msug.id', '=', 'msp.case_gaiso_count_unit')
            ->leftJoin('msunit as msub', 'msub.id', '=', 'msp.case_box_count_unit')
            ->leftJoin('msunit as msui', 'msui.id', '=', 'msp.case_inner_count_unit')
            ->leftJoin('mskatanuki as mskat', 'mskat.id', '=', 'msp.katanuki_id')
            ->select(
                'msp.code as product_code',
                'msp.code_alias',
                'msp.codebarcode',
                'msp.product_type_code',
                'mspt.name as product_type_name',
                'mspg.name as product_group_name',
                'msu.code as product_unit_code',
                'msu.name as product_unit_name',
                'msp.unit_weight',
                'msp.ketebalan',
                'msp.diameterlipat',
                'msp.productlength',
                'msp.warnalpkid',
                'mswlpk.name as warna_lpk_name',
                'msp.inflation_thickness',
                'msp.inflation_fold_diameter',
                'msp.one_winding_m_number',
                'msmat.code as material_code',
                'msmat.name as material_name',
                'msemb.code as embossed_code',
                'msemb.name as embossed_name',
                'mssurf.code as surface_code',
                'mssurf.name as surface_name',
                'mslbi.code as lakban_infure_code',
                'mslbi.name as lakban_infure_name',
                'msp.coloring_1', 'msp.coloring_2', 'msp.coloring_3', 'msp.coloring_4', 'msp.coloring_5',
                'msp.inflation_notes',
                'msgent.code as gentan_code',
                'msgent.name as gentan_name',
                'msgaz.code as gazette_code',
                'msgaz.name as gazette_name',
                'msp.gazette_dimension_a', 'msp.gazette_dimension_b', 'msp.gazette_dimension_c', 'msp.gazette_dimension_d',
                'msp.number_of_color',
                'msp.color_spec_1', 'msp.color_spec_2', 'msp.color_spec_3', 'msp.color_spec_4', 'msp.color_spec_5',
                'msp.back_color_number',
                'msp.back_color_1', 'msp.back_color_2', 'msp.back_color_3', 'msp.back_color_4', 'msp.back_color_5',
                'msjc.code as print_type_code',
                'msjc.name as print_type_name',
                'msst.code as ink_characteristic_code',
                'msst.name as ink_characteristic_name',
                'mse.code as endless_code',
                'mse.name as endless_name',
                'msag.code as arah_gulung_code',
                'msag.name as arah_gulung_name',
                'msp.kode_plate',
                'mskls.code as seal_code',
                'mskls.name as seal_name',
                'msp.from_seal_design',
                'msp.lower_sealing_length',
                'msp.palet_jumlah_baris',
                'msp.palet_isi_baris',
                'mslbs.code as lakban_seitai_code',
                'mslbs.name as lakban_seitai_name',
                'mspkg.code as gaiso_code',
                'mspkg.name as gaiso_name',
                'mspkb.code as box_code',
                'mspkb.name as box_name',
                'mspki.code as inner_code',
                'mspki.name as inner_name',
                'mspkl.code as layer_code',
                'mspkl.name as layer_name',
                'msp.case_gaiso_count',
                'msug.name as gaiso_unit_name',
                'msp.case_gaiso_stampel',
                'msp.case_box_count',
                'msub.name as box_unit_name',
                'msp.case_box_stampel',
                'msp.case_inner_count',
                'msui.name as inner_unit_name',
                'msp.case_inner_stampel',
                'mskat.code as katanuki_code',
                'mskat.name as katanuki_name',
                'msp.kodehagata',
                'msp.extracted_dimension_a',
                'msp.extracted_dimension_b',
                'msp.extracted_dimension_c',
                'msp.manufacturing_summary',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->when(isset($this->product_type_id) && $this->product_type_id != "" && $this->product_type_id != "undefined" && $this->product_type_id['value'] != "", function ($query) {
                $query->where('msp.product_type_id', $this->product_type_id);
            })
            ->orderBy('msp.id', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data pada periode tanggal tersebut tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            // Basic
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->code_alias);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->codebarcode);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_type_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_type_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_group_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_unit_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->product_unit_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->unit_weight);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->ketebalan);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->diameterlipat);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->productlength);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->warnalpkid);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->warna_lpk_name);
            // INFURE
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inflation_thickness);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inflation_fold_diameter);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->one_winding_m_number);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->material_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->material_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->embossed_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->embossed_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->surface_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->surface_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->lakban_infure_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->lakban_infure_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->coloring_1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->coloring_2);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->coloring_3);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->coloring_4);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->coloring_5);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inflation_notes);
            // Gentan/Gazette
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gentan_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gentan_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_dimension_a);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_dimension_b);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_dimension_c);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gazette_dimension_d);
            // PRINTING
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->number_of_color);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->color_spec_1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->color_spec_2);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->color_spec_3);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->color_spec_4);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->color_spec_5);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_number);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_2);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_3);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_4);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->back_color_5);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->print_type_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->print_type_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->ink_characteristic_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->ink_characteristic_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->endless_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->endless_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->arah_gulung_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->arah_gulung_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->kode_plate);
            // SEITAI
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->seal_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->seal_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->from_seal_design);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->lower_sealing_length);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->palet_jumlah_baris);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->palet_isi_baris);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->lakban_seitai_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->lakban_seitai_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gaiso_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gaiso_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->box_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->box_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inner_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inner_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->layer_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->layer_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_gaiso_count);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->gaiso_unit_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_gaiso_stampel);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_box_count);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->box_unit_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_box_stampel);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_inner_count);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->inner_unit_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->case_inner_stampel);
            // HAGATA
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->katanuki_code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->katanuki_name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->kodehagata);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->extracted_dimension_a);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->extracted_dimension_b);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->extracted_dimension_c);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->manufacturing_summary);
            // Status
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->status == 1 ? 'Active' : 'Inactive');
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->updated_by);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->updated_on);

            phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItem . ':' . $lastColumn . $rowItem, false, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItem . ':' . $lastColumn . $rowItem);
            $rowItem++;
        }

        $rowFooter = $rowItem + 1;
        $activeWorksheet->setCellValue('A' . $rowFooter, 'Dicetak pada: ' . Carbon::now()->translatedFormat('d-M-Y H:i:s') . ', oleh: ' . auth()->user()->empname);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowFooter, false, 9, 'Calibri');

        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $activeWorksheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $filename = 'Master-Produk.xlsx';
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function render()
    {
        $data = DB::table('msproduct as msp')
            ->leftJoin('msproduct_type as mspt', 'msp.product_type_id', '=', 'mspt.id')
            ->leftJoin('mskatanuki as msk', 'msp.katanuki_id', '=', 'msk.id')
            ->select(
                'msp.id',
                'msp.code as product_code',
                'msp.code_alias',
                'msp.name as product_name',
                'msp.product_type_code',
                'mspt.name as product_type_name',
                DB::raw('msp.ketebalan || \'x\' || msp.diameterlipat || \'x\' || msp.productlength as dimensi'),
                'msp.unit_weight',
                'msk.code as katanuki_code',
                'msp.number_of_color',
                'msp.back_color_number',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->when(isset($this->product_type_id) && $this->product_type_id != "" && $this->product_type_id != "undefined" && $this->product_type_id['value'] != "", function ($query) {
                $query->where('msp.product_type_id', $this->product_type_id);
            })
            ->orderBy('msp.updated_on', 'DESC')
            ->get();

        return view('livewire.master-tabel.produk.master-produk', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
