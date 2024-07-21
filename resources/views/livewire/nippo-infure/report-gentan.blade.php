<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body style="background-color: #CCCCCC;margin: 0">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="350" style="padding:25px">
            <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="50%" align="center">
                                    <span>
                                        <font style="font-size: 38px; font-weight: bold;">1</font>
                                    </span>
                                </td>
                                <td width="50%" align="center">
                                    <span>Barcode</span>
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center">
                                    <span>
                                        <font style="font-size: 28px; font-weight: bold;">
                                            {{ $lpk_no }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-bottom: 1px solid black;">
                            <tr>
                                <td align="center">
                                    <span>
                                        <font style="font-size: 22px;">
                                            {{ $name }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-bottom: 1px solid black;">
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            No. Order
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $code }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Kode
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $product_type_code }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-bottom: 1px solid black;">
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Tgl Prod
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $production_date }}
                                        </font>
                                    </span>
                                </td>
                            </tr>                            
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Jam
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $work_hour }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Shift
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $work_shift }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Mesin
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $code }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-bottom: 1px solid black;">
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Berat
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $berat_produksi }}
                                        </font>
                                    </span>
                                </td>
                            </tr>                            
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Panjang
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : @if (isset($panjang_produksi))
                                                {{ $panjang_produksi }}
                                            @endif
                                        </font>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Lebih
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : -
                                        </font>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            No Han
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $nomor_han }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-bottom: 1px solid black;">
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            NIK
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : {{ $nik }}
                                        </font>
                                    </span>
                                </td>
                            </tr>                            
                            <tr>
                                <td width="40%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            Nama
                                        </font>
                                    </span>
                                </td>
                                <td width="60%">
                                    <span>
                                        <font style="font-size: 22px;">
                                            : @if (isset($empname))
                                                {{ $empname }}
                                            @endif
                                        </font>
                                    </span>
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