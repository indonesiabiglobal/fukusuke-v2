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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
            'Tanggal Kejadian',
            'Shift',
            'Grup',
            'No. Kartu Kenpin',
            'No. Label Gudang',
            'Department NG',
            'Status Kenpin',
            'Kasus',
            'No. Produk',
            'Nama Produk',
            'No. Mesin Seitai',
            'Bagian Mesin',
            'Kode Masalah',
            'Masalah',
            'Detail Masalah',
            'Jumlah Box Seitai',
        ];

        $headerLot = [
            'No Lot',
            'Jumlah Box Palet',
            'No Box Dari',
            'No Box Sampai',
            'Jumlah Box Kenpin',
            'Qty Loss',
            'Jumlah Orang kenpin'
        ];

        $headerKenpin = [
            'Total Jumlah Box Kenpin',
            'Total Loss Kenpin (Box)',
            'NIK',
            'Nama Operator',
            'Tanggal Selesai Kenpin',
            'Jumlah NG yang ditemukan (Box)',
            'Jumlah NG yang ditemukan (Gaiso)',
            'Penyebab',
            'Keterangan Penyebab',
            'Penanggulangan Masalah',
        ];

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
                    tdka.incident_date AS incident_date,
                    tdka.shift AS shift,
                    tdka.grup AS grup,
                    tdka.kenpin_no AS kenpin_no,
                    tdka.nomor_palet AS nomor_palet,
                    tdka.status_kenpin AS status_kenpin,
                    tdka.detail_masalah,
                    tdka.penyebab,
                    tdka.keterangan_penyebab,
                    tdka.penanggulangan,
                    tdka.done_at,
                    tdka.is_kasus,
                    tdka.qty_loss as total_qty_loss,
                    msd.name AS department_ng,
                    msp.code_alias AS produk_code,
                    msp.NAME AS nama_produk,
                    msp.case_box_count,
                    mse.empname AS nama_petugas,
                    mse.employeeno AS nik_petugas,
                    msm.machineno,
                    msmpd.code AS code_bagian_mesin,
                    msmpd.name AS nama_bagian_mesin,
                    msmk.code AS code_masalah,
                    msmk.name AS nama_masalah,
                    tdpg.nomor_lot,
                    tdpg.qty_produksi,
                    tdkad.qty_loss AS qty_loss,
                    tdkad.nomor_box_dari,
                    tdkad.nomor_box_sampai,
                    tdkad.jumlah_orang_kenpin,
                    tdkad.jumlah_ng_box,
                    tdkad.jumlah_ng_gaiso
                FROM
                    tdkenpin AS tdka
                    INNER JOIN msdepartment AS msd ON msd.ID = tdka.kenpin_department_id
                    INNER JOIN tdkenpin_goods_detail AS tdkad ON tdka.ID = tdkad.kenpin_id
                    LEFT JOIN tdproduct_goods AS tdpg ON tdkad.product_goods_id = tdpg.ID
                    INNER JOIN msProduct AS msp ON tdpg.product_id = msp.ID
                    INNER JOIN msemployee AS mse ON mse.ID = tdka.employee_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                    INNER JOIN ms_machine_part_detail AS msmpd ON msmpd.ID = tdka.machine_part_detail_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                WHERE
                    tdka.kenpin_department_id = 7
                    $filterKenpinId
                    $filterDate
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
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
            if (!isset($dataFiltered[$item->kenpin_no])) {
                $dataFiltered[$item->kenpin_no] = [
                    'incident_date' => $item->incident_date,
                    'kenpin_date' => $item->kenpin_date,
                    'kenpin_no' => $item->kenpin_no,
                    'shift' => $item->shift,
                    'grup' => $item->grup,
                    'nomor_palet' => $item->nomor_palet,
                    'status_kenpin' => $item->status_kenpin,
                    'penyebab' => $item->penyebab,
                    'keterangan_penyebab' => $item->keterangan_penyebab,
                    'penanggulangan' => $item->penanggulangan,
                    'is_kasus' => $item->is_kasus,
                    'detail_masalah' => $item->detail_masalah,
                    'total_qty_loss' => $item->total_qty_loss,
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
                    'nomor_lot' => $item->nomor_lot,
                ];
            }

            $jumlahBoxPalet = $item->qty_produksi / $item->case_box_count ?: 0;
            $jumlahBoxKenpin = $item->nomor_box_sampai ? $item->nomor_box_sampai - $item->nomor_box_dari + 1 : 0;
            $dataFiltered[$item->kenpin_no][$item->nomor_palet][] = [
                'nomor_lot' => $item->nomor_lot,
                'jumlah_box_palet' => $jumlahBoxPalet,
                'nomor_box_dari' => $item->nomor_box_dari ?? '-',
                'nomor_box_sampai' => $item->nomor_box_sampai ?? '-',
                'jumlah_box_kenpin' => $jumlahBoxKenpin,
                'qty_loss' => $item->qty_loss,
                'jumlah_orang_kenpin' => $item->jumlah_orang_kenpin,
            ];

            // jumlah box palet dan kenpin total
            $dataFiltered[$item->kenpin_no]['jumlah_box_seitai'] = isset($dataFiltered[$item->kenpin_no]['jumlah_box_seitai']) ? $dataFiltered[$item->kenpin_no]['jumlah_box_seitai'] + $jumlahBoxPalet : $jumlahBoxPalet;
            $dataFiltered[$item->kenpin_no]['total_jumlah_box_kenpin'] = isset($dataFiltered[$item->kenpin_no]['total_jumlah_box_kenpin']) ? $dataFiltered[$item->kenpin_no]['total_jumlah_box_kenpin'] + $jumlahBoxKenpin : $jumlahBoxKenpin;

            $dataFiltered[$item->kenpin_no]['total_jumlah_ng_box'] = isset($dataFiltered[$item->kenpin_no]['total_jumlah_ng_box']) ? $dataFiltered[$item->kenpin_no]['total_jumlah_ng_box'] + $item->jumlah_ng_box : $item->jumlah_ng_box;
            $dataFiltered[$item->kenpin_no]['total_jumlah_ng_gaiso'] = isset($dataFiltered[$item->kenpin_no]['total_jumlah_ng_gaiso']) ? $dataFiltered[$item->kenpin_no]['total_jumlah_ng_gaiso'] + $item->jumlah_ng_gaiso : $item->jumlah_ng_gaiso;
        }

        // header
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $maxLot = 0;
        foreach ($dataFiltered as $itemKenpin) {
            $lotCount = count($itemKenpin[$itemKenpin['nomor_palet']]);
            if ($lotCount > $maxLot) {
                $maxLot = $lotCount;
            }
        }
        for ($i = 0; $i < $maxLot; $i++) {
            $columnHeaderStartLot = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd));
            foreach ($headerLot as $key => $value) {
                $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
                $columnHeaderEnd++;
            }

            // style header lot background color with random bright color
            // generate bright RGB by keeping each channel in high range (180-255)
            $r = mt_rand(180, 255);
            $g = mt_rand(180, 255);
            $b = mt_rand(180, 255);
            // ARGB expects 8 hex chars, prefix with FF for full opacity
            $randomColor = 'FF' . sprintf('%02X%02X%02X', $r, $g, $b);
            $activeWorksheet->getStyle($columnHeaderStartLot . $rowHeaderStart . ':' . Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1) . $rowHeaderStart)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($randomColor);
        }

        $columnHeaderKenpinStart = $columnHeaderEnd;
        foreach ($headerKenpin as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('A4');
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        // end header

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;
        foreach ($dataFiltered as $kenpinNo => $itemKenpin) {
            $columnItemEnd = $columnItemStart;
            // tgl input
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemKenpin['kenpin_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // tgl kejadian
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemKenpin['incident_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // shift
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['shift']);
            $columnItemEnd++;

            // grup
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['grup']);
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

            // kasus
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['is_kasus'] ? 'Ya' : 'Tidak');
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

            // detail masalah
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['detail_masalah']);
            $columnItemEnd++;

            // jumlah box seitai
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['jumlah_box_seitai']);
            $columnItemEnd++;

            foreach ($itemKenpin[$itemKenpin['nomor_palet']] as $lotNo => $itemLot) {
                // nomor lot
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['nomor_lot']);
                $columnItemEnd++;

                // jumlah box palet
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['jumlah_box_palet']);
                $columnItemEnd++;

                // nomor box dari
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['nomor_box_dari']);
                $columnItemEnd++;

                // nomor box sampai
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['nomor_box_sampai']);
                $columnItemEnd++;

                // jumlah box kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['jumlah_box_kenpin']);
                $columnItemEnd++;

                // qty loss
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['qty_loss']);
                $columnItemEnd++;

                // jumlah orang kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemLot['jumlah_orang_kenpin']);
                $columnItemEnd++;
            }
            $columnItemEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderKenpinStart));

            // total jumlah box kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['total_jumlah_box_kenpin']);
            $columnItemEnd++;

            // Total Loss Kenpin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['total_qty_loss'] ?? 0);
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

            // total jumlah ng box
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['total_jumlah_ng_box'] ?? 0);
            $columnItemEnd++;

            // total jumlah ng gaiso
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['total_jumlah_ng_gaiso'] ?? 0);
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
        $columnItemEnd++;
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        if ($isSingleReport) {
            $filename = 'Detail-Kenpin-Seitai-' . $filter['kenpin_no'] . '.xlsx';
        } else {
            $filename = 'Detail-Kenpin-Seitai-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        }
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
