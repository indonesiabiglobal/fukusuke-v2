<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
{{-- @php
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename=Nippo.xls");
@endphp --}}
@php
    use Carbon\Carbon;

    $data = collect(
        DB::select("
            select
                tdpa.id as id,
                tdpa.production_no as production_no,
                tdpa.production_date as production_date,
                tdpa.employee_id as employee_id,
                tdpa.work_shift as work_shift,
                tdpa.work_hour as work_hour,
                tdpa.machine_id as machine_id,
                tdpa.lpk_id as lpk_id,
                tdpa.product_id as product_id,
                tdpa.panjang_produksi as panjang_produksi,
                tdpa.panjang_printing_inline as panjang_printing_inline,
                tdpa.berat_standard as berat_standard,
                tdpa.berat_produksi as berat_produksi,
                tdpa.nomor_han as nomor_han,
                tdpa.gentan_no as gentan_no,
                tdpa.seq_no as seq_no,
                tdpa.status_production as status_production,
                tdpa.status_kenpin as status_kenpin,
                tdpa.infure_cost as infure_cost,
                tdpa.infure_cost_printing as infure_cost_printing,
                tdpa.infure_berat_loss as infure_berat_loss,
                tdpa.kenpin_berat_loss as kenpin_berat_loss,
                tdpa.kenpin_meter_loss as kenpin_meter_loss,
                tdpa.kenpin_meter_loss_proses as kenpin_meter_loss_proses,
                tdpa.created_by as created_by,
                tdpa.created_on as created_on,
                tdpa.updated_by as updated_by,
                tdpa.updated_on as updated_on,
                tdol.order_id as order_id,
                tdol.lpk_no as lpk_no,
                tdol.lpk_date as lpk_date,
                tdol.panjang_lpk as panjang_lpk,
                tdol.qty_gentan as qty_gentan,
                tdol.qty_gulung as qty_gulung,
                tdol.qty_lpk as qty_lpk,
                tdol.total_assembly_line as total_assembly_line,
                tdol.total_assembly_qty as total_assembly_qty,
                msp.name as productname,
                msp.code as codename,
                msp.product_type_code,
                mse.empname,
                msm.machineno,
                mse.nik
            from tdproduct_assembly as tdpa
                inner join tdorderlpk as tdol on tdpa.lpk_id = tdol.id
                inner join msmachine as msm on msm.id = tdpa.machine_id
                inner join msproduct as msp on msp.id = tdpa.product_id
                inner join msemployee as mse on mse.id = tdpa.employee_id
            where tdpa.created_on >= $tanggal
        "),
    );
@endphp
<body style="background-color: #CCCCCC;margin: 0">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="950" style="padding:25px">
            <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align: left;">
                            <tr>
                                <th colspan="9">CHEKLIST LOSS INFURE</th>
                            </tr>
                            <tr>
                                <th colspan="9">Tanggal Produksi : 28 Juli 2024</th>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="1">
                            <tr>
                                <th width="8%">Tanggal Proses</th>
                                <th width="8%">Tanggal Produksi</th>
                                <th width="8%" rowspan="2">Nomor LPK</th>
                                <th width="15%">Nama Produk</th>
                                <th width="8%" rowspan="2">Nomor Mesin</th>
                                <th width="15%">Petugas</th>
                                <th width="15%" rowspan="2">Nama Loss</th>
                                <th width="5%" rowspan="2">Kode Loss</th>
                                <th width="8%" rowspan="2">Berat Loss</th>
                            </tr>
                            <tr>
                                <th>No. Proses</th>
                                <th>Shift</th>
                                <th>Nomor Order</th>
                                <th>NIK</th>
                            </tr>

                            {{-- items --}}
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->production_date)->format('d-m-y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->production_date)->format('d-m-y') }}</td>
                                    <td>{{ $item->lpk_no }}</td>
                                    <td>({{ $item->product_type_code }}) {{ $item->productname }}</td>
                                    <td>{{ $item->machineno }}</td>
                                    <td>{{ $item->empname }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $item->seq_no }}</td>
                                    <td>{{ $item->work_shift }}</td>
                                    <td></td>
                                    <td>{{ $item->codename }}</td>
                                    <td></td>
                                    <td>{{ $item->nik }}</td>
                                    @php
                                        $detail = collect(
                                            DB::select("
                                                SELECT
                                                    tal.loss_infure_id,
                                                    tal.id,
                                                    tal.lpk_id,
                                                    ml.name,
                                                    tal.berat_loss
                                                FROM
                                                    tdproduct_assembly_loss as tal
                                                    INNER JOIN mslossinfure as ml on ml.id = tal.loss_infure_id
                                                WHERE
                                                    lpk_id = '$item->lpk_id'
                                            "),
                                        );
                                    
                                    // Ambil item pertama dan sisa item
                                    $firstItem = $detail->first();
                                    $remainingItems = $detail->slice(1);
                                    $totalBeratLoss = 0;
                                    @endphp

                                    <!-- Loop pertama -->
                                    @if ($firstItem)
                                        <td>{{ $firstItem->name }}</td>
                                        <td>{{ $firstItem->loss_infure_id }}</td>
                                        <td>{{ $firstItem->berat_loss }}</td>
                                    @endif
                                </tr>
                                <tr>
                                    @foreach ($remainingItems as $item)
                                        <td colspan="6"></td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->loss_infure_id }}</td>
                                        <td>{{ $item->berat_loss }}</td>
                                    @endforeach
                                </tr>
                            @endforeach

                            {{-- Spasi --}}
                            <tr>
                                <th colspan="9"></th>
                            </tr>

                            <tr style="text-align: left">
                                <th colspan="8">GRAND TOTAL</th>
                                @if (!empty($detail))
                                    @foreach ($detail as $item)
                                        @php
                                            $totalBeratLoss += $item->berat_loss;
                                        @endphp
                                    @endforeach
                                    <th>{{ $totalBeratLoss }}</th>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
