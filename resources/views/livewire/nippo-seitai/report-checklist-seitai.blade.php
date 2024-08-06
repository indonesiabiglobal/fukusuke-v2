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
        SELECT
            tdpg.id AS id,
            tdpg.production_no AS production_no,
            tdpg.production_date AS tglproduksi,
            tdpg.created_on AS tglproses,
            tdpg.employee_id AS employee_id,
            ma.empname AS namapetugas,
            ma.employeeno AS nikpetugas,
            tdpg.work_shift AS shift,
            tdpg.work_hour AS work_hour,
            tdpg.machine_id AS machine_id,
            mm.machineno AS mesinno,
            mm.machinename AS mesinnama,
            tdpg.lpk_id AS lpk_id,
            tdol.lpk_no AS nolpk,
            tdpg.product_id AS product_id,
            mp.NAME AS namaproduk,
            mp.code AS noorder,
            tdpg.qty_produksi AS qty_produksi,
            tdpg.seitai_berat_loss AS seitai_berat_loss,
            tdpg.infure_berat_loss AS infure_berat_loss,
            tdpg.nomor_palet AS nomor_palet,
            tdpg.nomor_lot AS nomor_lot,
            tdpg.seq_no AS noproses
        FROM
            tdproduct_goods AS tdpg
            LEFT JOIN tdorderLpk AS tdol ON tdpg.lpk_id = tdol.
            ID LEFT JOIN msemployee AS ma ON tdpg.employee_id = ma.
            ID LEFT JOIN msmachine AS mm ON tdpg.machine_id = mm.
            ID LEFT JOIN msproduct AS mp ON tdpg.product_id = mp.ID
        WHERE
            tdpg.created_on >= '2024-06-17 00:00:00'
            AND tdpg.created_on <= '2024-06-17 23:59:00'
            AND tdpg.id = 356700
        "),
    );
@endphp
<body style="background-color: #CCCCCC;margin: 0" onload="window.print()">
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
                                <th width="8%">NIK</th>
                                <th width="8%" rowspan="2">Nomor Mesin</th>
                                <th width="8%" rowspan="2">Nomor LPK</th>
                                <th width="15%">Nama Produk</th>
                                <th width="8%" rowspan="2">Quantity (Lembar)</th>
                                <th width="8%">Loss Infure</th>
                                <th width="8%">Nomor Palet</th>
                                <th width="8%" rowspan="2">Nomor Gentan</th>
                                <th width="8%" rowspan="2">Nama Loss</th>
                                <th width="8%" rowspan="2">Berat (kg)</th>
                            </tr>
                            <tr>
                                <th>No. Proses</th>
                                <th>Shift</th>
                                <th>Petugas</th>
                                <th>Nomor Order</th>
                                <th>NIK</th>
                                <th>Nomor LOT</th>
                            </tr>

                            {{-- items --}}
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tglproduksi)->format('d-m-y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tglproduksi)->format('d-m-y') }}</td>
                                    <td>{{ $item->nikpetugas }}</td>
                                    <td>({{ $item->mesinno }})</td>
                                    <td>{{ $item->nolpk }}</td>
                                    <td>{{ $item->namaproduk }}</td>
                                    <td>{{ $item->qty_produksi }}</td>
                                    <td></td>
                                    <td>{{ $item->nomor_palet }}</td>
                                    @php
                                        $gentanno = collect(
                                            DB::select("
                                                SELECT
                                                    pga.product_goods_id,
                                                    tdpa.gentan_no || '-' || pga.gentan_line AS nogentan
                                                FROM
                                                    tdproduct_goods_assembly AS pga
                                                    LEFT JOIN tdProduct_Goods AS tdpg ON tdpg.ID = pga.product_goods_id
                                                    LEFT JOIN tdproduct_assembly AS tdpa ON tdpa.ID = pga.product_assembly_id
                                                WHERE
                                                    pga.product_goods_id = '$item->id'
                                            "),
                                        );

                                        // Ambil item pertama dan sisa item
                                        $firstGentan = $gentanno->first();
                                        $remainingGentan = $gentanno->slice(1);
                                    @endphp

                                    <!-- Loop pertama -->
                                    @if ($firstGentan)
                                        <td>{{ $firstGentan->nogentan }}</td>
                                    @endif

                                    @php
                                        $loss = collect(
                                            DB::select("
                                                select
                                                    pgl.product_goods_id,ls.name as namaloss,pgl.berat_loss
                                                FROM tdproduct_goods AS tdpg
                                                    inner join tdproduct_goods_loss as pgl on tdpg.id=pgl.product_goods_id
                                                    left join mslossseitai as ls on pgl.loss_seitai_id=ls.id
                                                WHERE
                                                    pgl.product_goods_id = '$item->id'
                                            "),
                                        );

                                        // Ambil item pertama dan sisa item
                                        $firstLoss = $loss->first();
                                        $remainingLoss = $loss->slice(1);
                                    @endphp

                                    <!-- Loop pertama -->
                                    @if ($firstLoss)
                                        <td>{{ $firstLoss->namaloss }}</td>
                                        <td>{{ $firstLoss->berat_loss }}</td>
                                    @endif
                                </tr>
                                @foreach ($remainingGentan as $item)
                                    <tr>
                                        <td colspan="9"></td>
                                        <td>{{ $item->nogentan }}</td>
                                    @foreach ($remainingLoss as $item)
                                    {{-- <tr> --}}
                                        <td>{{ $item->namaloss }}</td>
                                        <td>{{ $item->berat_loss }}</td>
                                    {{-- </tr>                                         --}}
                                    @endforeach
                                    </tr>
                                @endforeach


                            @endforeach

                            {{-- Spasi --}}
                            <tr>
                                <th colspan="9"></th>
                            </tr>

                            <tr style="text-align: left">
                                <th colspan="8">GRAND TOTAL</th>
                                {{-- @if (!empty($detail))
                                    @foreach ($detail as $item)
                                        @php
                                            $totalBeratLoss += $item->berat_loss;
                                        @endphp
                                    @endforeach
                                    <th>{{ $totalBeratLoss }}</th>
                                @endif --}}
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
