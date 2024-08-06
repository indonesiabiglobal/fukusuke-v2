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

    // $data = collect(
    //     DB::select(""),
    // )->first();
@endphp

<body style="background-color: #CCCCCC;margin: 0" onload="window.print()">
    <div align="center">
        <h1>CHECKLIST NIPPO SEITAI</h1>
        <h1>Periode Produksi: 01-Jul-2024 00:00  ~  28-Jul-2024 23:59</h1>
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="100%"
            style="padding:25px">
            <thead>
                <tr>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Tanggal Proses
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Tanggal Produksi
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        NIK
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Nomor Mesin
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Nomor LPK
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Nama Produk
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Quantity (Lembar)
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Loss Infure
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Nomor Palet
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Nomor Gentan
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Nama Loss
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;" rowspan="2">
                        Berat (Kg)
                    </td>
                </tr>
                <tr>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        No. Proses
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Shift
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Petugas
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Nomor Order
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        NIK
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        Nomor LOT
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            01-Jul-2024
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            01-Jul-2024
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            123456
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            123456
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            123456
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            VD BM_S
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            1000
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            1000
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            F1887-290624
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            3-B
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            21. Mark Miss
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            0.5
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            102
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            01
                        </span>
                    </td>
                    <td colspan="3" style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            Pipit Mardianah
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            PFKG5
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            4120591
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            96401101
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            4-A
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            22. Mimiore
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            0.5
                        </span>
                    </td>
                </tr>
                @for ($i=1; $i < 5; $i++)
                <tr>
                    <td colspan="9"></td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            4-A
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            22. Mimiore
                        </span>
                    </td>
                    <td style="padding: 3px;border: 1px solid black;text-align: center; vertical-align: top;">
                        <span>
                            0.5
                        </span>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</body>

</html>
