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
	header("Content-Disposition: attachment; filename=Nippo.xls");
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
                            <tr>
                                <td>28-Jul-2024</td>
                                <td>28-Jul-2024</td>
                                <td>240711-080</td>
                                <td>(2181) New EG_M Natural</td>
                                <td>00I01</td>
                                <td>Wahyu Setiaji</td>
                            </tr>
                            <tr>
                                <td>234</td>
                                <td>1</td>
                                <td></td>
                                <td>0472662</td>
                                <td></td>
                                <td>12061209</td>
                                <td>Jusi Keras</td>
                                <td>92</td>
                                <td>0.60</td>
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td>Putus Pinhole</td>
                                <td>25</td>
                                <td>3.60</td>
                            </tr>
                            {{-- Spasi --}}
                            <tr>
                                <th colspan="9"></th>
                            </tr>

                            <tr style="text-align: left">
                                <th colspan="8">GRAND TOTAL</th>
                                <th>543.20</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>