<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
@php
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename=NippoSeitai.xls");
@endphp
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
            $tanggal
            limit 10
        "),
    );
@endphp
<body style="background-color: #CCCCCC;margin: 0">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="1050" style="padding:25px">
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
                                <th width="8%">Petugas</th>
                                
                                <th width="8%" rowspan="2">Nama Loss</th>
                                <th width="8%" rowspan="2">Kode Loss</th>
                                <th width="8%" rowspan="2">Berat Loss</th>
                            </tr>
                            <tr>
                                <th>No. Proses</th>
                                <th>Shift</th>
                                <th>Nomor Order</th>
                                <th>NIK</th>
                            </tr>
                            
                            @php
                                $qty_produksi=0;
                                $berat_gentan=0;
                            @endphp
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tglproduksi)->format('d-m-y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tglproduksi)->format('d-m-y') }}</td>
                                    <td>{{ $item->nolpk }}</td>
                                    <td>{{ $item->namaproduk }}</td>
                                    <td>({{ $item->mesinno }})</td>                                    
                                    <td>{{ $item->namapetugas }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                @php
                                    $loss = collect(
                                        DB::select("
                                            select
                                                pgl.product_goods_id,ls.name as namaloss,pgl.berat_loss,ls.code
                                            FROM tdproduct_goods AS tdpg
                                                inner join tdproduct_goods_loss as pgl on tdpg.id=pgl.product_goods_id
                                                left join mslossseitai as ls on pgl.loss_seitai_id=ls.id
                                            WHERE
                                                pgl.product_goods_id = '$item->id'
                                        "),
                                    );
                                @endphp

                                <tr>
                                    <td>{{ $item->noproses }}</td>
                                    <td>{{ $item->shift }}</td>
                                    <td></td>
                                    <td>{{ $item->noorder }}</td>
                                    <td></td>
                                    <td>{{ $item->nikpetugas }}</td>
                                    
                                    <td>{{ isset($loss[0]->namaloss) ? $loss[0]->namaloss : '' }}</td>
                                    <td>{{ isset($loss[0]->code) ? $loss[0]->code : '' }}</td>
                                    <td>{{ isset($loss[0]->berat_loss) ? $loss[0]->berat_loss : '' }}</td>
                                </tr>

                                @if (isset($loss[1]))
                                    <tr>
                                        <td colspan="6"></td>
                                        <td>{{ isset($loss[1]->namaloss) ? $loss[1]->namaloss : '' }}</td>
                                        <td>{{ isset($loss[1]->code) ? $loss[1]->code : '' }}</td>
                                        <td>{{ isset($loss[1]->berat_loss) ? $loss[1]->berat_loss : '' }}</td>
                                    </tr>
                                @endif

                                @if (isset($loss[2]))
                                    <tr>
                                        <td colspan="6"></td>
                                        <td>{{ isset($loss[1]->namaloss) ? $loss[1]->namaloss : '' }}</td>
                                        <td>{{ isset($loss[1]->code) ? $loss[1]->code : '' }}</td>
                                        <td>{{ isset($loss[1]->berat_loss) ? $loss[1]->berat_loss : '' }}</td>
                                    </tr>
                                @endif

                                @if (isset($loss[3]))
                                    <tr>
                                        <td colspan="6"></td>
                                        <td>{{ isset($loss[1]->namaloss) ? $loss[1]->namaloss : '' }}</td>
                                        <td>{{ isset($loss[1]->code) ? $loss[1]->code : '' }}</td>
                                        <td>{{ isset($loss[1]->berat_loss) ? $loss[1]->berat_loss : '' }}</td>
                                    </tr>
                                @endif

                                @php
                                    $qty_produksi += $item->qty_produksi;
                                @endphp
                            @endforeach

                            <tr style="text-align: left">
                                <th colspan="8">GRAND TOTAL</th>
                                <th>-</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
