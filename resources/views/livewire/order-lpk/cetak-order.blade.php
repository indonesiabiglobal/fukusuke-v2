<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
@php
    use Carbon\Carbon;

$data = collect(
        DB::select("
        SELECT
            tod.processdate,
            tod.po_no,
            tod.order_date,
            mp.code,
            mp.name,
            mp.ketebalan||'x'||mp.diameterlipat||'x'||mp.productlength as dimensi,
            tod.order_qty,
            tod.stufingdate,
            tod.etddate,
            tod.etadate,
            mbu.name as namabuyer
        FROM
            tdorder AS tod
            INNER JOIN msproduct AS mp ON mp.ID = tod.product_id
            INNER JOIN msbuyer AS mbu ON mbu.ID = tod.buyer_id
        WHERE
            tod.id = $orderId
        "),
    )->first();
@endphp
<body style="background-color: #CCCCCC;margin: 0" onload="window.print()">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="700" style="padding:25px">
            <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td>
                                    <h1>Fukusuke - Order Form</h1>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" border="1" cellpadding="3">
                            <tr>
                                <td>
                                    Tanggal Proses
                                </td>
                                <td>
                                    {{ $data->processdate }} - Nomor : 33
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    PO Number
                                </td>
                                <td>
                                    {{ $data->po_no }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tanggal Order
                                </td>
                                <td>
                                    {{ $data->order_date }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nomor Order
                                </td>
                                <td>
                                    {{ $data->code }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nama Produk
                                </td>
                                <td>
                                    {{ $data->name }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Dimensi
                                </td>
                                <td>
                                    {{ $data->dimensi }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Jumlah Order
                                </td>
                                <td>
                                    {{ $data->order_qty }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tanggal Stufing
                                </td>
                                <td>
                                    {{ $data->stufingdate }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tanggal ETD
                                </td>
                                <td>
                                    {{ $data->etddate }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tanggal ETA
                                </td>
                                <td>
                                    {{ $data->etadate }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nama Buyer
                                </td>
                                <td>
                                    {{ $data->namabuyer }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
