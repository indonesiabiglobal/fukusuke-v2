<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Helpers\phpspreadsheet;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\Configuration\Php;

class LabelMasukGudangController extends Component
{
    public $nomor_palet;
    public $data = [];
    public $code;
    public $name;

    public function search()
    {
        $this->render();
    }

    public function print()
    {
        $nomor_palet = $this->nomor_palet;
        $this->dispatch('redirectToPrint', $nomor_palet);
    }

    public function export()
    {
        $data = collect(
            DB::select("
            SELECT
            tdpg.production_date AS production_date,
            tdpg.nomor_palet AS nomor_palet,
                tdpg.nomor_lot AS nomor_lot,
                    tdpg.work_shift AS work_shift,
                    tdpg.employee_id AS employee_id,
                    me.empname as namapetugas,
                    tdpg.product_id AS product_id,
                    mp.code_alias as nocode,
                    mp.name as namaproduk,
                    mp.palet_jumlah_baris as tinggi,
                    mp.palet_isi_baris as jmlbaris,
                    tdpg.qty_produksi/cast(mp.case_box_count as  INTEGER) AS qty_produksi
                FROM  tdProduct_Goods AS tdpg
                left JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.id
                    left join msproduct as mp on mp.id=tdpg.product_id
                    left join msemployee as me on me.id=tdpg.employee_id
                WHERE tdpg.nomor_palet = (LTRIM(RTRIM('$this->nomor_palet')))
            ")
        );

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        // Menghilangkan gridline
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A5);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.75 / 2.54);

        $startColumn = 'B';
        // Set Title Kartu masuk gudang
        $columnTitleCardEnd = 'S';
        $rowTitleCard = 2;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCard . ':' . $columnTitleCardEnd . $rowTitleCard);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowTitleCard, 'KARTU MASUK GUDANG (P)');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTitleCard, false, 20, 'Tahoma');

        // set title nomor palet
        $columnTitleNomorPaletStart = 'V';
        $columnTitleNomorPaletEnd = 'X';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleNomorPaletStart . $rowTitleCard . ':' . $columnTitleNomorPaletEnd . $rowTitleCard);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleNomorPaletStart . $rowTitleCard, 'Nomor :');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleNomorPaletStart . $rowTitleCard, false, 10, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleNomorPaletStart . $rowTitleCard);

        // set title nomor palet value
        $nomerPalet = explode('-', $data[0]->nomor_palet);
        $columnTitleNomorPaletValueStart = 'Y';
        $endColumn = 'AK';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleNomorPaletValueStart . $rowTitleCard . ':' . $endColumn . $rowTitleCard);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleNomorPaletValueStart . $rowTitleCard, $nomerPalet[1]);
        phpSpreadsheet::styleFont($spreadsheet, $columnTitleNomorPaletValueStart . $rowTitleCard, false, 36, 'Times New Roman');
        phpspreadsheet::textAlignLeft($spreadsheet, $columnTitleNomorPaletValueStart . $rowTitleCard);

        /* Gudang */
        // Text Gudang
        $columnTextGudangEnd = 'G';
        $rowTextGudang = 3;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTextGudang . ':' . $columnTextGudangEnd . $rowTextGudang);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowTextGudang, 'Gudang');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTextGudang, true, 12, 'Tahoma', 'FFFFFFFF');
        phpspreadsheet::styleCell($spreadsheet, $startColumn . $rowTextGudang, 'FF000000');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowTextGudang);

        // Text Tanggal
        $columnTanggalStart = 'H';
        $columnTanggalEnd = 'L';
        $spreadsheet->getActiveSheet()->mergeCells($columnTanggalStart . $rowTextGudang . ':' . $columnTanggalEnd . $rowTextGudang);
        $spreadsheet->getActiveSheet()->setCellValue($columnTanggalStart . $rowTextGudang, 'Tanggal : ');
        phpspreadsheet::styleFont($spreadsheet, $columnTanggalStart . $rowTextGudang, false, 11, 'Tahoma');

        // Tanggal Value
        $columnTanggalValueStart = 'M';
        $columnTanggalValueEnd = 'S';
        $spreadsheet->getActiveSheet()->mergeCells($columnTanggalValueStart . $rowTextGudang . ':' . $columnTanggalValueEnd . $rowTextGudang);
        $spreadsheet->getActiveSheet()->setCellValue($columnTanggalValueStart . $rowTextGudang, Carbon::parse($data[0]->production_date)->format('d-m-Y'));
        phpspreadsheet::styleFont($spreadsheet, $columnTanggalValueStart . $rowTextGudang, false, 11, 'Tahoma');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTanggalStart . $rowTextGudang . ':' . $columnTanggalValueEnd . $rowTextGudang);

        // Petugas
        $rowPetugas = 4;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowPetugas . ':' . $columnTextGudangEnd . $rowPetugas);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowPetugas, 'Petugas');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowPetugas, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowPetugas);

        // Petugas Value
        $rowPetugasValueStart = 5;
        $rowPetugasValueEnd = 6;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowPetugasValueStart . ':' . $columnTextGudangEnd . $rowPetugasValueEnd);
        // $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowPetugasValueStart, $data[0]->namapetugas);
        // phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowPetugasValueStart, false, 11, 'Tahoma');
        phpspreadsheet::addOutlineBorder($spreadsheet, $startColumn . $rowPetugas . ':' . $columnTextGudangEnd . $rowPetugasValueEnd);

        // Assisten Leader
        $columnAssitenEnd = 'N';
        $spreadsheet->getActiveSheet()->mergeCells($columnTanggalStart . $rowPetugas . ':' . $columnAssitenEnd . $rowPetugas);
        $spreadsheet->getActiveSheet()->setCellValue($columnTanggalStart . $rowPetugas, 'Ass. Leader');
        phpspreadsheet::styleFont($spreadsheet, $columnTanggalStart . $rowPetugas, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTanggalStart . $rowPetugas);

        // Assisten Value
        $spreadsheet->getActiveSheet()->mergeCells($columnTanggalStart . $rowPetugasValueStart . ':' . $columnAssitenEnd . $rowPetugasValueEnd);
        // $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowPetugasValueStart, $data[0]->namapetugas);
        // phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowPetugasValueStart, false, 11, 'Tahoma');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTanggalStart . $rowPetugas . ':' . $columnAssitenEnd . $rowPetugasValueEnd);

        // Gudang
        $columnStartGudang = 'O';
        $spreadsheet->getActiveSheet()->mergeCells($columnStartGudang . $rowPetugas . ':' . $columnTanggalValueEnd . $rowPetugas);
        $spreadsheet->getActiveSheet()->setCellValue($columnStartGudang . $rowPetugas, 'Gudang');
        phpspreadsheet::styleFont($spreadsheet, $columnStartGudang . $rowPetugas, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnStartGudang . $rowPetugas);

        // Gudang Value
        $spreadsheet->getActiveSheet()->mergeCells($columnStartGudang . $rowPetugasValueStart . ':' . $columnTanggalValueEnd . $rowPetugasValueEnd);
        // $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowPetugasValueStart, $data[0]->namapetugas);
        // phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowPetugasValueStart, false, 11, 'Tahoma');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnStartGudang . $rowPetugas . ':' . $columnTanggalValueEnd . $rowPetugasValueEnd);

        // Nomor palet
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleNomorPaletStart . $rowTextGudang . ':' . $endColumn . $rowPetugasValueEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleNomorPaletStart . $rowTextGudang, $nomerPalet[0]);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleNomorPaletStart . $rowTextGudang . ':' . $endColumn . $rowPetugasValueEnd);
        phpspreadsheet::styleFont($spreadsheet, $columnTitleNomorPaletStart . $rowTextGudang, false, 72, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleNomorPaletStart . $rowTextGudang);

        // title nomer lot
        $columnTitleNomorLotEnd = 'G';
        $rowTitleNomorLotStart = 8;
        $rowTitleNomorLotEnd = 9;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleNomorLotStart . ':' . $columnTitleNomorLotEnd . $rowTitleNomorLotEnd);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowTitleNomorLotStart, 'Nomor Lot');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTitleNomorLotStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowTitleNomorLotStart);

        // title jumlah box
        $columnTitleJumlahBoxStart = 'H';
        $columnTitleJumlahBoxEnd = 'J';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleJumlahBoxStart . $rowTitleNomorLotStart . ':' . $columnTitleJumlahBoxEnd . $rowTitleNomorLotEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleJumlahBoxStart . $rowTitleNomorLotStart, 'Jumlah Box');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleJumlahBoxStart . $rowTitleNomorLotStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleJumlahBoxStart . $rowTitleNomorLotStart);

        // title jumlah revisi
        $columnTitleJumlahRevisiStart = 'K';
        $columnTitleJumlahRevisiEnd = 'M';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleJumlahRevisiStart . $rowTitleNomorLotStart . ':' . $columnTitleJumlahRevisiEnd . $rowTitleNomorLotEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleJumlahRevisiStart . $rowTitleNomorLotStart, 'Jumlah Revisi');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleJumlahRevisiStart . $rowTitleNomorLotStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleJumlahRevisiStart . $rowTitleNomorLotStart);

        // title operator
        $columnTitleOperatorStart = 'N';
        $columnTitleOperatorEnd = 'S';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleOperatorStart . $rowTitleNomorLotStart . ':' . $columnTitleOperatorEnd . $rowTitleNomorLotEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleOperatorStart . $rowTitleNomorLotStart, 'Operator');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleOperatorStart . $rowTitleNomorLotStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleOperatorStart . $rowTitleNomorLotStart);

        // title shift
        $columnTitleShiftStart = 'T';
        $columnTitleShiftEnd = 'U';
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleShiftStart . $rowTitleNomorLotStart . ':' . $columnTitleShiftEnd . $rowTitleNomorLotEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleShiftStart . $rowTitleNomorLotStart, 'Shift');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleShiftStart . $rowTitleNomorLotStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleShiftStart . $rowTitleNomorLotStart);

        // border untuk title
        phpspreadsheet::addFullBorder($spreadsheet, $startColumn . '8' . ':' . $columnTitleShiftEnd . '9');
        $spreadsheet->getActiveSheet()->getStyle($startColumn . '8' . ':' . $columnTitleShiftEnd . '9')->getAlignment()->setWrapText(true);

        // value
        $rowNomorLotStart = 10;
        $maxIndexNomorLot = 10;
        for ($nomorLotIndex = 0; $nomorLotIndex < $maxIndexNomorLot; $nomorLotIndex++) {
            // nomor lot value
            $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowNomorLotStart . ':' . $columnTitleNomorLotEnd . $rowNomorLotStart);
            $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowNomorLotStart, $data[$nomorLotIndex]->nomor_lot ?? '');
            phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowNomorLotStart, false, 11, 'Times New Roman');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowNomorLotStart);

            // jumlah box value
            $spreadsheet->getActiveSheet()->mergeCells($columnTitleJumlahBoxStart . $rowNomorLotStart . ':' . $columnTitleJumlahBoxEnd . $rowNomorLotStart);
            $spreadsheet->getActiveSheet()->setCellValue($columnTitleJumlahBoxStart . $rowNomorLotStart, $data[$nomorLotIndex]->qty_produksi ?? '');
            phpspreadsheet::styleFont($spreadsheet, $columnTitleJumlahBoxStart . $rowNomorLotStart, false, 11, 'Times New Roman');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleJumlahBoxStart . $rowNomorLotStart);

            // jumlah revisi value
            $spreadsheet->getActiveSheet()->mergeCells($columnTitleJumlahRevisiStart . $rowNomorLotStart . ':' . $columnTitleJumlahRevisiEnd . $rowNomorLotStart);

            // operator value
            $spreadsheet->getActiveSheet()->mergeCells($columnTitleOperatorStart . $rowNomorLotStart . ':' . $columnTitleOperatorEnd . $rowNomorLotStart);
            $spreadsheet->getActiveSheet()->setCellValue($columnTitleOperatorStart . $rowNomorLotStart, $data[$nomorLotIndex]->namapetugas ?? '');
            phpspreadsheet::styleFont($spreadsheet, $columnTitleOperatorStart . $rowNomorLotStart, false, 11, 'Times New Roman');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleOperatorStart . $rowNomorLotStart);

            // shift value
            $spreadsheet->getActiveSheet()->mergeCells($columnTitleShiftStart . $rowNomorLotStart . ':' . $columnTitleShiftEnd . $rowNomorLotStart);
            $spreadsheet->getActiveSheet()->setCellValue($columnTitleShiftStart . $rowNomorLotStart, $data[$nomorLotIndex]->work_shift ?? '');
            phpspreadsheet::styleFont($spreadsheet, $columnTitleShiftStart . $rowNomorLotStart, false, 11, 'Times New Roman');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleShiftStart . $rowNomorLotStart);

            $rowNomorLotStart++;
        }
        // border untuk value
        phpspreadsheet::addFullBorderDotted($spreadsheet, $startColumn . '10' . ':' . $columnTitleShiftEnd . '19');

        phpspreadsheet::addOutlineBorder($spreadsheet, $startColumn . '10' . ':' . $startColumn . '19');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleJumlahBoxStart . '10' . ':' . $columnTitleJumlahBoxEnd . '19');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleOperatorStart . '10' . ':' . $columnTitleOperatorEnd . '19');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleShiftStart . '10' . ':' . $columnTitleShiftEnd . '19');

        // catatan
        $columnCatatanStart = 'B';
        $columnCatatanEnd = 'U';
        $rowCatatanStart = 20;
        $rowCatatanEnd = 25;
        $spreadsheet->getActiveSheet()->setCellValue($columnCatatanStart . $rowCatatanStart, 'Catatan :');
        phpspreadsheet::styleFont($spreadsheet, $columnCatatanStart . $rowCatatanStart, true, 11, 'Tahoma');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnCatatanStart . $rowCatatanStart . ':' . $columnCatatanEnd . $rowCatatanEnd);

        // title dokumentasi
        $columnDokumentasiStart = 'B';
        $columnDokumentasiEnd = 'D';
        $rowDokumentasi = 24;
        $spreadsheet->getActiveSheet()->mergeCells($columnDokumentasiStart . $rowDokumentasi . ':' . $columnDokumentasiEnd . $rowDokumentasi);
        $spreadsheet->getActiveSheet()->setCellValue($columnDokumentasiStart . $rowDokumentasi, 'No Dok :');
        phpspreadsheet::styleFont($spreadsheet, $columnDokumentasiStart . $rowDokumentasi, false, 8, 'Times New Roman');

        // value dokumentasi
        $columnDokumentasiValueStart = 'E';
        $columnDokumentasiValueEnd = 'J';
        $spreadsheet->getActiveSheet()->mergeCells($columnDokumentasiValueStart . $rowDokumentasi . ':' . $columnDokumentasiValueEnd . $rowDokumentasi);
        $spreadsheet->getActiveSheet()->setCellValue($columnDokumentasiValueStart . $rowDokumentasi, 'FKI/I/frm/GU/0015');
        phpspreadsheet::styleFont($spreadsheet, $columnDokumentasiValueStart . $rowDokumentasi, false, 8, 'Times New Roman');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnDokumentasiStart . $rowDokumentasi . ':' . $columnDokumentasiValueEnd . $rowDokumentasi);

        // title revisi
        $columnRevisiStart = 'B';
        $columnRevisiEnd = 'D';
        $rowRevisi = 25;
        $spreadsheet->getActiveSheet()->mergeCells($columnRevisiStart . $rowRevisi . ':' . $columnRevisiEnd . $rowRevisi);
        $spreadsheet->getActiveSheet()->setCellValue($columnRevisiStart . $rowRevisi, 'Revisi :');
        phpspreadsheet::styleFont($spreadsheet, $columnRevisiStart . $rowRevisi, false, 8, 'Times New Roman');

        // value revisi
        $columnRevisiValueStart = 'E';
        $columnRevisiValueEnd = 'J';
        $spreadsheet->getActiveSheet()->mergeCells($columnRevisiValueStart . $rowRevisi . ':' . $columnRevisiValueEnd . $rowRevisi);
        $spreadsheet->getActiveSheet()->setCellValue($columnRevisiValueStart . $rowRevisi, '01');
        phpspreadsheet::styleFont($spreadsheet, $columnRevisiValueStart . $rowRevisi, false, 8, 'Times New Roman');
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnRevisiStart . $rowRevisi . ':' . $columnRevisiValueEnd . $rowRevisi);

        // title nomor produk
        $columnTitleNomorProdukStart = 'V';
        $columnTitleNomorProdukEnd = 'AA';
        $rowTitleNomorProdukStart = 8;
        $rowTitleNomorProdukEnd = 9;
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleNomorProdukStart . $rowTitleNomorProdukStart . ':' . $columnTitleNomorProdukEnd . $rowTitleNomorProdukEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleNomorProdukStart . $rowTitleNomorProdukStart, 'Nomor Produk');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleNomorProdukStart . $rowTitleNomorProdukStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleNomorProdukStart . $rowTitleNomorProdukStart);

        // value nomor produk
        $columnNomorProdukStart = 'AB';
        $spreadsheet->getActiveSheet()->mergeCells($columnNomorProdukStart . $rowTitleNomorProdukStart . ':' . $endColumn . $rowTitleNomorProdukEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnNomorProdukStart . $rowTitleNomorProdukStart, $data[0]->nocode);
        phpspreadsheet::styleFont($spreadsheet, $columnNomorProdukStart . $rowTitleNomorProdukStart, false, 36, 'Times New Roman');
        phpspreadsheet::textAlignLeft($spreadsheet, $columnNomorProdukStart . $rowTitleNomorProdukStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleNomorProdukStart . $rowTitleNomorProdukStart . ':' . $endColumn . $rowTitleNomorProdukEnd);

        // title nama produk
        $columnTitleNamaProdukStart = 'V';
        $columnTitleNamaProdukEnd = 'AK';
        $rowTitleNamaProdukStart = 10;
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleNamaProdukStart . $rowTitleNamaProdukStart . ':' . $columnTitleNamaProdukEnd . $rowTitleNamaProdukStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleNamaProdukStart . $rowTitleNamaProdukStart, 'Nama Produk');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleNamaProdukStart . $rowTitleNamaProdukStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTitleNamaProdukStart . $rowTitleNamaProdukStart);

        // value nama produk
        $columnNamaProdukStart = 'V';
        $columnNamaProdukEnd = 'AK';
        $rowNamaProdukStart = 11;
        $rowNamaProdukEnd = 13;
        $spreadsheet->getActiveSheet()->mergeCells($columnNamaProdukStart . $rowNamaProdukStart . ':' . $columnNamaProdukEnd . $rowNamaProdukEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnNamaProdukStart . $rowNamaProdukStart, $data[0]->namaproduk);
        phpspreadsheet::styleFont($spreadsheet, $columnNamaProdukStart . $rowNamaProdukStart, false, 20, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnNamaProdukStart . $rowNamaProdukStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTitleNamaProdukStart . $rowTitleNamaProdukStart . ':' . $columnTitleNamaProdukEnd . $rowNamaProdukEnd);

        // jumlah kotak (box)
        $columnJumlahKotakStart = 'V';
        $columnJumlahKotakEnd = 'AK';
        $rowJumlahKotakStart = 14;
        $spreadsheet->getActiveSheet()->mergeCells($columnJumlahKotakStart . $rowJumlahKotakStart . ':' . $columnJumlahKotakEnd . $rowJumlahKotakStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnJumlahKotakStart . $rowJumlahKotakStart, 'Jumlah Kotak (Box)');
        phpspreadsheet::styleFont($spreadsheet, $columnJumlahKotakStart . $rowJumlahKotakStart, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJumlahKotakStart . $rowJumlahKotakStart);

        // value jumlah kotak (box)
        $jumlahBox = $data->sum('qty_produksi');
        $columnJumlahKotakValueStart = 'V';
        $columnJumlahKotakValueEnd = 'AK';
        $rowJumlahKotakValueStart = 15;
        $rowJumlahKotakValueEnd = 16;
        $spreadsheet->getActiveSheet()->mergeCells($columnJumlahKotakValueStart . $rowJumlahKotakValueStart . ':' . $columnJumlahKotakValueEnd . $rowJumlahKotakValueEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnJumlahKotakValueStart . $rowJumlahKotakValueStart, $jumlahBox);
        phpspreadsheet::styleFont($spreadsheet, $columnJumlahKotakValueStart . $rowJumlahKotakValueStart, true, 36, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJumlahKotakValueStart . $rowJumlahKotakValueStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnJumlahKotakStart . $rowJumlahKotakStart . ':' . $columnJumlahKotakEnd . $rowJumlahKotakValueEnd);

        // title tinggi
        $columnTinggiStart = 'W';
        $columnTinggiEnd = 'Y';
        $rowTinggiStart = 17;
        $spreadsheet->getActiveSheet()->mergeCells($columnTinggiStart . $rowTinggiStart . ':' . $columnTinggiEnd . $rowTinggiStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnTinggiStart . $rowTinggiStart, 'Tinggi');
        phpspreadsheet::styleFont($spreadsheet, $columnTinggiStart . $rowTinggiStart, false, 8, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTinggiStart . $rowTinggiStart);

        // value tinggi
        $tinggi = $jumlahBox / $data[0]->jmlbaris;
        $columnTinggiValueStart = 'W';
        $columnTinggiValueEnd = 'Y';
        $rowTinggiValueStart = 18;
        $rowTinggiValueEnd = 19;
        $spreadsheet->getActiveSheet()->mergeCells($columnTinggiValueStart . $rowTinggiValueStart . ':' . $columnTinggiValueEnd . $rowTinggiValueEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTinggiValueStart . $rowTinggiValueStart, round($tinggi));
        phpspreadsheet::styleFont($spreadsheet, $columnTinggiValueStart . $rowTinggiValueStart, false, 24, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTinggiValueStart . $rowTinggiValueStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnTinggiValueStart . $rowTinggiValueStart . ':' . $columnTinggiValueEnd . $rowTinggiValueEnd);

        // tanda X
        $columnTandaXStart = 'Z';
        $columnTandaXEnd = 'AA';
        $rowTandaXStart = 18;
        $rowTandaXEnd = 19;
        $spreadsheet->getActiveSheet()->mergeCells($columnTandaXStart . $rowTandaXStart . ':' . $columnTandaXEnd . $rowTandaXEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTandaXStart . $rowTandaXStart, 'X');
        phpspreadsheet::styleFont($spreadsheet, $columnTandaXStart . $rowTandaXStart, false, 22, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTandaXStart . $rowTandaXStart);

        // title jumlah baris
        $columnJumlahBarisStart = 'AB';
        $columnJumlahBarisEnd = 'AD';
        $rowJumlahBarisStart = 17;
        $spreadsheet->getActiveSheet()->mergeCells($columnJumlahBarisStart . $rowJumlahBarisStart . ':' . $columnJumlahBarisEnd . $rowJumlahBarisStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnJumlahBarisStart . $rowJumlahBarisStart, 'Jumlah Baris');
        phpspreadsheet::styleFont($spreadsheet, $columnJumlahBarisStart . $rowJumlahBarisStart, false, 8, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJumlahBarisStart . $rowJumlahBarisStart);

        // value jumlah baris
        $columnJumlahBarisValueStart = 'AB';
        $columnJumlahBarisValueEnd = 'AD';
        $rowJumlahBarisValueStart = 18;
        $rowJumlahBarisValueEnd = 19;
        $spreadsheet->getActiveSheet()->mergeCells($columnJumlahBarisValueStart . $rowJumlahBarisValueStart . ':' . $columnJumlahBarisValueEnd . $rowJumlahBarisValueEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnJumlahBarisValueStart . $rowJumlahBarisValueStart, $data[0]->jmlbaris);
        phpspreadsheet::styleFont($spreadsheet, $columnJumlahBarisValueStart . $rowJumlahBarisValueStart, false, 24, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJumlahBarisValueStart . $rowJumlahBarisValueStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnJumlahBarisStart . $rowJumlahBarisValueStart . ':' . $columnJumlahBarisValueEnd . $rowJumlahBarisValueEnd);


        // tanda +
        $columnTandaPlusStart = 'AE';
        $columnTandaPlusEnd = 'AF';
        $rowTandaPlusStart = 18;
        $rowTandaPlusEnd = 19;
        $spreadsheet->getActiveSheet()->mergeCells($columnTandaPlusStart . $rowTandaPlusStart . ':' . $columnTandaPlusEnd . $rowTandaPlusEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnTandaPlusStart . $rowTandaPlusStart, '+');
        phpspreadsheet::styleFont($spreadsheet, $columnTandaPlusStart . $rowTandaPlusStart, false, 22, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTandaPlusStart . $rowTandaPlusStart);

        // title satuan
        $columnSatuanStart = 'AG';
        $columnSatuanEnd = 'AI';
        $rowSatuanStart = 17;
        $spreadsheet->getActiveSheet()->mergeCells($columnSatuanStart . $rowSatuanStart . ':' . $columnSatuanEnd . $rowSatuanStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnSatuanStart . $rowSatuanStart, 'Satuan');
        phpspreadsheet::styleFont($spreadsheet, $columnSatuanStart . $rowSatuanStart, false, 8, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnSatuanStart . $rowSatuanStart);

        // value satuan
        $columnSatuanValueStart = 'AG';
        $columnSatuanValueEnd = 'AI';
        $rowSatuanValueStart = 18;
        $rowSatuanValueEnd = 19;
        $spreadsheet->getActiveSheet()->mergeCells($columnSatuanValueStart . $rowSatuanValueStart . ':' . $columnSatuanValueEnd . $rowSatuanValueEnd);
        $spreadsheet->getActiveSheet()->setCellValue($columnSatuanValueStart . $rowSatuanValueStart, $jumlahBox % $data[0]->jmlbaris);
        phpspreadsheet::styleFont($spreadsheet, $columnSatuanValueStart . $rowSatuanValueStart, false, 24, 'Times New Roman');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnSatuanValueStart . $rowSatuanValueStart);
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnSatuanStart . $rowSatuanValueStart . ':' . $columnSatuanValueEnd . $rowSatuanValueEnd);

        // border perhitungan
        $columnPerhitunganStart = 'V';
        $columnPerhitunganEnd = 'AK';
        $rowPerhitunganStart = 17;
        $rowPerhitunganEnd = 20;
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnPerhitunganStart . $rowPerhitunganStart . ':' . $columnPerhitunganEnd . $rowPerhitunganEnd);

        // title pengecekan
        $columnPengecekanStart = 'V';
        $columnPengecekanEnd = 'AK';
        $rowPengecekanStart = 21;
        $rowPengecekanEnd = 25;
        $spreadsheet->getActiveSheet()->mergeCells($columnPengecekanStart . $rowPengecekanStart . ':' . $columnPengecekanEnd . $rowPengecekanStart);
        $spreadsheet->getActiveSheet()->setCellValue($columnPengecekanStart . $rowPengecekanStart, 'Pengecekan Kebersihan Produk');
        phpspreadsheet::styleFont($spreadsheet, $columnPengecekanStart . $rowPengecekanStart, false, 8, 'Tahoma');
        phpspreadsheet::textAlignLeft($spreadsheet, $columnPengecekanStart . $rowPengecekanStart);

        // border pengecekan
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnPengecekanStart . $rowPengecekanStart . ':' . $columnPengecekanEnd . $rowPengecekanEnd);

        // checkboxes petugas setai
        $columnPetugasSeitai = 'W';
        $rowPetugasSeitai = 23;
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnPetugasSeitai . $rowPetugasSeitai);

        // title petugas seitai
        $columnTitlePetugasSeitaiStart = 'X';
        $columnTitlePetugasSeitaiEnd = 'AC';
        $rowTitlePetugasSeitai = 23;
        $spreadsheet->getActiveSheet()->mergeCells($columnTitlePetugasSeitaiStart . $rowTitlePetugasSeitai . ':' . $columnTitlePetugasSeitaiEnd . $rowTitlePetugasSeitai);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitlePetugasSeitaiStart . $rowTitlePetugasSeitai, 'Petugas Seitai');
        phpspreadsheet::styleFont($spreadsheet, $columnTitlePetugasSeitaiStart . $rowTitlePetugasSeitai, false, 8, 'Tahoma');
        phpspreadsheet::textAlignLeft($spreadsheet, $columnTitlePetugasSeitaiStart . $rowTitlePetugasSeitai);

        // checkboxes sebelum suffing
        $columnSebelumSuffing = 'AE';
        $rowSebelumSuffing = 23;
        phpspreadsheet::addOutlineBorder($spreadsheet, $columnSebelumSuffing . $rowSebelumSuffing);

        // title sebelum suffing
        $columnTitleSebelumSuffingStart = 'AF';
        $columnTitleSebelumSuffingEnd = 'AK';
        $rowTitleSebelumSuffing = 23;
        $spreadsheet->getActiveSheet()->mergeCells($columnTitleSebelumSuffingStart . $rowTitleSebelumSuffing . ':' . $columnTitleSebelumSuffingEnd . $rowTitleSebelumSuffing);
        $spreadsheet->getActiveSheet()->setCellValue($columnTitleSebelumSuffingStart . $rowTitleSebelumSuffing, 'Sebelum Suffing');
        phpspreadsheet::styleFont($spreadsheet, $columnTitleSebelumSuffingStart . $rowTitleSebelumSuffing, false, 8, 'Tahoma');
        phpspreadsheet::textAlignLeft($spreadsheet, $columnTitleSebelumSuffingStart . $rowTitleSebelumSuffing);

        // membuat border untuk seluruh cell
        phpspreadsheet::addOutlineBorder($spreadsheet, $startColumn . '2:' . $endColumn . '25');
        $startColumn = 'A';
        while ($startColumn !== $endColumn) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumn)->setWidth(25, 'px');

            $startColumn++;
        }

        // baris 8 -25 untuk height dibuat 26px
        for ($rowHeightIndex = 8; $rowHeightIndex <= 25; $rowHeightIndex++) {
            $spreadsheet->getActiveSheet()->getRowDimension($rowHeightIndex)->setRowHeight(26, 'px');
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('asset/report/Label-Gudang-' . $this->nomor_palet . '.xlsx');
        return response()->download('asset/report/Label-Gudang-' . $this->nomor_palet . '.xlsx');
    }

    public function render()
    {
        if (isset($this->nomor_palet) && $this->nomor_palet != '') {
            $this->data = DB::select("
            SELECT
                tdpg.nomor_lot,
                msm.machinename,
                tdpg.production_date,
                tdpg.qty_produksi/cast(msp.case_box_count as  INTEGER) AS qty_produksi,
                tdpg.nomor_palet,
                msp.name,
                msp.code
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct AS msp ON msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet='$this->nomor_palet'");

            if ($this->data == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->nomor_palet . ' Tidak Terdaftar']);
            } else {
                $this->code = $this->data[0]->code;
                $this->name = $this->data[0]->name;
            }
        }

        return view('livewire.nippo-seitai.label-masuk-gudang')->extends('layouts.master');
    }
}
