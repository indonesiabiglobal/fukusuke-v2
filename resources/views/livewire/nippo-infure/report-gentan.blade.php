<!DOCTYPE html>
<html lang="en">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $('#button').on('click', function() {
                window.print();
            });
        });
        $(function() {
            var hasPrinted = false;

            window.onbeforeprint = function() {
                hasPrinted = false;
            };

            window.onafterprint = function() {
                hasPrinted = true;
                tryToCloseWindow();
            };

            function tryToCloseWindow() {
                if (hasPrinted) {
                    setTimeout(function() {
                        window.close();
                        // Jika window.close() tidak berhasil, coba metode alternatif
                        setTimeout(function() {
                            if (!window.closed) {
                                alert("Pencetakan selesai. Silakan tutup jendela ini secara manual.");
                            }
                        }, 1000);
                    }, 100);
                }
            }

            // Memicu pencetakan
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
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
            tdol.lpk_no,
            msp.name,
            msp.code,
            msp.code_alias,
            msp.product_type_code,
            to_char(tdpa.production_date, 'DD-MM-YYYY') AS production_date,
            tdpa.work_hour,
            tdpa.work_shift,
            msm.machineno,
            tdpa.berat_produksi,
            tdpa.panjang_produksi,
            tdol.total_assembly_line-tdol.panjang_lpk as selisih,
            tdpa.nomor_han,
            mse.employeeno as nik,
            mse.empname,
            tdpa.gentan_no
        FROM
            tdproduct_assembly AS tdpa
            INNER JOIN tdorderlpk AS tdol ON tdpa.lpk_id = tdol.ID
            INNER JOIN msproduct as msp on msp.id = tdol.product_id
            LEFT JOIN msworkingshift as msw on msw.id = tdpa.work_shift
            INNER JOIN msmachine as msm on msm.id = tdpa.machine_id
            INNER JOIN msemployee as mse on mse.id = tdpa.employee_id
        WHERE
            tdpa.id = $produk_asemblyid
        "),
    )->first();
@endphp

<body style="background-color: #CCCCCC;margin: 0" onload="window.print()">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="350"
            style="padding:25px">
            <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="50%" align="center">
                                    <span>
                                        <font style="font-size: 38px; font-weight: bold;">{{ $data->gentan_no }}</font>
                                    </span>
                                </td>
                                <td width="50%" align="center">
                                    {{-- <span>Barcode</span> --}}
                                    @php
                                        $url = $data->lpk_no;
                                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($url);
                                    @endphp
                                    {{ $qrCode }}
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center">
                                    <span>
                                        <font style="font-size: 28px; font-weight: bold;">
                                            {{ $data->lpk_no }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <hr>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                            style="border-bottom: 1px solid black;">
                            <tr>
                                <td align="center">
                                    <span>
                                        <font style="font-size: 22px;">
                                            {{ $data->name }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                            style="border-bottom: 1px solid black;">
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
                                            : {{ $data->code }}
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
                                            : {{ $data->code_alias }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                            style="border-bottom: 1px solid black;">
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
                                            : {{ $data->production_date }}
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
                                            : {{ $data->work_hour }}
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
                                            : {{ $data->work_shift }}
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
                                            : {{ $data->machineno }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                            style="border-bottom: 1px solid black;">
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
                                            : {{ $data->berat_produksi }}
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
                                            : @if (isset($data->panjang_produksi))
                                                {{ $data->panjang_produksi }}
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
                                            : {{ $data->selisih }}
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
                                            : {{ $data->nomor_han }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                            style="border-bottom: 1px solid black;">
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
                                            : {{ $data->nik }}
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
                                            : @if (isset($data->empname))
                                                {{ $data->empname }}
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
