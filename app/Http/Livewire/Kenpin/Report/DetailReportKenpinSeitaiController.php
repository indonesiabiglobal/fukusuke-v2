<?php

namespace App\Http\Livewire\Kenpin\Report;

use App\Exports\KenpinExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DetailReportKenpinSeitaiController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $product;
    public $productId;
    public $department;
    public $nippo;
    public $buyer;
    public $buyer_id;
    public $lpk_no;
    public $nomorKenpin;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;
    public $status;

    public function detailReportKenpinSeitai($tglAwal, $tglAkhir, $filter = null, $isSingleReport = false)
    {

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DAFTAR KENPIN PER DEPARTEMEN SEITAI');
        if ($isSingleReport) {
            $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y'));
        } else {
            $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        }
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Tanggal Input',
            'No. Kartu Kenpin',
            'No. Label Gudang',
            'Department NG',
            'Status Kenpin',
            'No. Produk',
            'Nama Produk',
            'No. Mesin Seitai',
            'Bagian Mesin',
            'Kode Masalah',
            'Masalah',
            'Jumlah Box Palet',
            'Jumlah Box Kenpin',
            'NIK',
            'Nama Operator',
            'Tanggal Selesai Kenpin',
            'Loss (Lembar)',
            'Penyebab',
            'Keterangan Penyebab',
            'Penanggulangan Masalah',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Filter Query
        $filterKenpinId = $filter && isset($filter['kenpin_id']) ? " AND (tdka.ID = '" . $filter['kenpin_id'] . "')" : '';
        $filterDate = "AND tdka.kenpin_date BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
        $filterProduct = isset($filter['productId']) ? " AND (tdpg.product_id = '" . $filter['productId'] . "')" : '';
        $filterNomorKenpin = isset($filter['nomorKenpin']) ? " AND (tdka.kenpin_no = '" . $filter['nomorKenpin'] . "')" : '';
        $filterStatus = isset($filter['status']) ? " AND (tdka.status_kenpin = '" . $filter['status'] . "')" : '';
        $filterNomorPalet = isset($filter['nomorPalet']) ? " AND (tdpg.nomor_palet = '" . $this->nomorPalet . "')" : '';
        $filterNomorLot = isset($filter['nomorLot']) ? " AND (tdpg.nomor_lot = '" . $this->nomorLot . "')" : '';

        $data = DB::select(
            "
                SELECT
                    tdka.kenpin_date AS kenpin_date,
                    tdka.kenpin_no AS kenpin_no,
                    tdka.nomor_palet AS nomor_palet,
                    tdka.status_kenpin AS status_kenpin,
                    tdka.penyebab,
                    tdka.keterangan_penyebab,
                    tdka.penanggulangan,
                    tdka.done_at,
                    msd.name AS department_ng,
                    tdkad.qty_loss AS qty_loss,
                    msp.code_alias AS produk_code,
                    msp.NAME AS nama_produk,
                    mse.empname AS nama_petugas,
                    mse.employeeno AS nik_petugas,
                    msm.machineno,
                    msmpd.code AS code_bagian_mesin,
                    msmpd.name AS nama_bagian_mesin,
                    msmk.code AS code_masalah,
                    msmk.name AS nama_masalah,
                    CAST(ROUND(SUM(CAST(tdpg.qty_produksi AS numeric) / NULLIF(CAST(msp.case_box_count AS numeric),0)), 0) AS integer) AS jumlah_box_palet,
                    COUNT(tdkadb.ID) AS jumlah_box_kenpin
                FROM
                    tdkenpin AS tdka
                    INNER JOIN msdepartment AS msd ON msd.ID = tdka.kenpin_department_id
                    INNER JOIN tdkenpin_goods_detail AS tdkad ON tdka.ID = tdkad.kenpin_id
                    INNER JOIN tdproduct_goods AS tdpg ON tdkad.product_goods_id = tdpg.ID
                    INNER JOIN msProduct AS msp ON tdpg.product_id = msp.ID
                    INNER JOIN msemployee AS mse ON mse.ID = tdka.employee_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                    INNER JOIN ms_machine_part_detail AS msmpd ON msmpd.ID = tdka.machine_part_detail_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                    LEFT JOIN tdkenpin_goods_detail_box AS tdkadb ON tdkadb.kenpin_goods_detail_id = tdkad.ID
                WHERE
                    tdka.kenpin_department_id = 7
                    $filterKenpinId
                    $filterDate
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
                GROUP BY
                    tdka.kenpin_no,
                    tdka.kenpin_date,
                    tdka.nomor_palet,
                    tdka.status_kenpin,
                    tdka.penyebab,
                    tdka.keterangan_penyebab,
                    tdka.penanggulangan,
                    tdka.done_at,
                    msd.name,
                    tdkad.qty_loss,
                    msp.code_alias,
                    msp.NAME,
                    mse.empname,
                    mse.employeeno,
                    msm.machineno,
                    msmpd.code,
                    msmpd.name,
                    msmk.code,
                    msmk.name
                ORDER BY tdka.kenpin_no ASC, tdka.kenpin_date ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFiltered = [];
        foreach ($data as $item) {
            $dataFiltered[$item->kenpin_no] = [
                'kenpin_date' => $item->kenpin_date,
                'kenpin_no' => $item->kenpin_no,
                'nomor_palet' => $item->nomor_palet,
                'status_kenpin' => $item->status_kenpin,
                'penyebab' => $item->penyebab,
                'keterangan_penyebab' => $item->keterangan_penyebab,
                'penanggulangan' => $item->penanggulangan,
                'done_at' => $item->done_at,
                'department_ng' => $item->department_ng,
                'qty_loss' => $item->qty_loss,
                'produk_code' => $item->produk_code,
                'nama_produk' => $item->nama_produk,
                'nik_petugas' => $item->nik_petugas,
                'nama_petugas' => $item->nama_petugas,
                'machineno' => $item->machineno,
                'code_bagian_mesin' => $item->code_bagian_mesin,
                'nama_bagian_mesin' => $item->nama_bagian_mesin,
                'code_masalah' => $item->code_masalah,
                'nama_masalah' => $item->nama_masalah,
                'jumlah_box_palet' => $item->jumlah_box_palet,
                'jumlah_box_kenpin' => $item->jumlah_box_kenpin,
            ];
        }

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;
        foreach ($dataFiltered as $kenpinNo => $itemKenpin) {
            $columnItemEnd = $columnItemStart;
            // tgl input
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemKenpin['kenpin_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // nomor kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['kenpin_no']);
            $columnItemEnd++;

            // nomor palet
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nomor_palet']);
            $columnItemEnd++;

            // department NG
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['department_ng']);
            $columnItemEnd++;

            // status kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['status_kenpin'] == 1 ? 'Proses' : ($itemKenpin['status_kenpin'] == 2 ? 'Finish' : ''));
            $columnItemEnd++;

            // No Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['produk_code']);
            $columnItemEnd++;

            // nama produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nama_produk']);
            $columnItemEnd++;

            // no mesin seitai
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, substr($itemKenpin['machineno'], -2));
            $columnItemEnd++;

            // bagian mesin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['code_bagian_mesin'] . ' - ' . $itemKenpin['nama_bagian_mesin']);
            $columnItemEnd++;

            // kode masalah
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['code_masalah']);
            $columnItemEnd++;

            // masalah
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nama_masalah']);
            $columnItemEnd++;

            // jumlah box palet
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['jumlah_box_palet']);
            $columnItemEnd++;

            // jumlah box kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['jumlah_box_kenpin']);
            $columnItemEnd++;

            // nik petugas
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nik_petugas']);
            $columnItemEnd++;

            // nama petugas
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nama_petugas']);
            $columnItemEnd++;

            // tanggal selesai kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['done_at'] ? Carbon::parse($itemKenpin['done_at'])->translatedFormat('d-M-Y') : '-');
            $columnItemEnd++;

            // loss (lembar)
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['qty_loss'] ?? 0);
            $columnItemEnd++;

            // penyebab
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['penyebab']);
            $columnItemEnd++;

            // keterangan penyebab
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['keterangan_penyebab']);
            $columnItemEnd++;

            // penanggulangan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['penanggulangan']);

            $rowItem++;
        }
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnItemEnd . $rowItem - 1);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnItemEnd . $rowItem - 1, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnItemEnd . $rowItem - 1);

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        if ($isSingleReport) {
            $filename = 'Detail-Kenpin-Infure-' . $filter['kenpin_no'] . '.xlsx';
        } else {
            $filename = 'Detail-Kenpin-Infure-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        }
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
