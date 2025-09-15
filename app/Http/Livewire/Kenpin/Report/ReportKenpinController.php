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

class ReportKenpinController extends Component
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
    public $reportType = 'detail';
    public $generalReportInfureList;
    public $generalReportSeitaiList;
    public $buyer;
    public $buyer_id;
    public $lpk_no;
    public $nomorKenpin;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;
    public $status;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->product = MsProduct::get();
        $this->department = MsDepartment::division()->get();
        $this->nippo = $this->department[0]->name;
        $this->generalReportInfureList = [
            ['value' => 'per-mesin', 'label' => 'Per Mesin Infure'],
            ['value' => 'loss-kenpin-berat', 'label' => 'Loss Kenpin Infure (Berat Kg)'],
            ['value' => 'box-kenpin-qty', 'label' => 'Loss Kenpin Infure (Qty Lembar)'],

        ];
        $this->generalReportSeitaiList = [
            ['value' => 'per-mesin', 'label' => 'Per Mesin Seitai'],
            ['value' => 'per-box', 'label' => 'Per Box Seitai'],
            ['value' => 'per-palet', 'label' => 'Per Palet Seitai'],
            ['value' => 'loss-kenpin', 'label' => 'Loss Kenpin Seitai (Qty Lembar)'],
        ];
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
            'nippo' => 'required',
            'reportType' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
            'nippo.required' => 'Jenis Report tidak boleh kosong',
            'reportType.required' => 'Tipe Report tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
            'nippo' => $this->nippo,
            'reportType' => $this->reportType,
        ], $rules, $messages);

        if ($validate->fails()) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $validate->errors()->first()]);
            return;
        }

        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        if ($this->nippo == 'INFURE') {
            $filter = [
                'lpk_no' => $this->lpk_no,
                'productId' => is_array($this->productId) ? $this->productId['value'] : $this->productId,
                'nomorKenpin' => $this->nomorKenpin,
                'nomorHan' => $this->nomorHan,
                'statusKenpin' => $this->status,
            ];

            if ($this->reportType == 'detail') {
                $detailReportKenpinInfure = new DetailReportKenpinInfureController();
                $response = $detailReportKenpinInfure->detailReportKenpinInfure($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'per-mesin') {
                $generalReportKenpinInfure = new GeneralReportKenpinInfureController();
                $response = $generalReportKenpinInfure->perMesinReportKenpinInfure($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'loss-kenpin-berat') {
                $generalReportKenpinInfure = new GeneralReportKenpinInfureController();
                $response = $generalReportKenpinInfure->beratLossReportKenpinInfure($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'box-kenpin-qty') {
                $generalReportKenpinInfure = new GeneralReportKenpinInfureController();
                $response = $generalReportKenpinInfure->qtyLossReportKenpinInfure($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            }
        } else if ($this->nippo == 'SEITAI') {
            $filter = [
                'lpk_no' => $this->lpk_no,
                'productId' => is_array($this->productId) ? $this->productId['value'] : $this->productId,
                'nomorKenpin' => $this->nomorKenpin,
                'statusKenpin' => $this->status,
                'nomorPalet' => $this->nomorPalet,
                'nomorLot' => $this->nomorLot,
            ];

            if ($this->reportType == 'detail') {
                $detailReportKenpinSeitai = new DetailReportKenpinSeitaiController();
                $response = $detailReportKenpinSeitai->detailReportKenpinSeitai($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'per-mesin') {
                $generalReportKenpinSeitai = new GeneralReportKenpinSeitaiController();
                $response = $generalReportKenpinSeitai->perMesinReportKenpinSeitai($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'per-box') {
                $generalReportKenpinSeitai = new GeneralReportKenpinSeitaiController();
                $response = $generalReportKenpinSeitai->perBoxReportKenpinSeitai($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'per-palet') {
                $generalReportKenpinSeitai = new GeneralReportKenpinSeitaiController();
                $response = $generalReportKenpinSeitai->perPaletReportKenpinSeitai($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            } else if ($this->reportType == 'loss-kenpin') {
                $generalReportKenpinSeitai = new GeneralReportKenpinSeitaiController();
                $response = $generalReportKenpinSeitai->qtyLossReportKenpinSeitai($tglAwal, $tglAkhir, $filter);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.kenpin.report-kenpin')->extends('layouts.master');
    }
}
