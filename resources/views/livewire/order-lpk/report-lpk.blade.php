<!DOCTYPE html>
<html lang="en">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        @media print {

            /* Menyembunyikan header, footer, dan URL */
            @page {
                margin: 4px;
                size: A4;
            }

            header,
            footer,
            .page-header,
            .page-footer {
                display: none;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .print-page {
            box-sizing: border-box;
            /* background: white; */
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .image-container img {
            width: 100%;
            height: auto;
        }

        .text-infure-gz-dimensi-A {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
        }

        .text-infure-gz-dimensi-B {
            position: absolute;
            top: 80%;
            left: 20%;
            transform: translate(-50%, -50%);
            font-size: 24px;
        }

        .text-infure-gz-dimensi-C {
            position: absolute;
            top: 80%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
        }

        .text-infure-gz-dimensi-D {
            position: absolute;
            top: 80%;
            left: 80%;
            transform: translate(-50%, -50%);
            font-size: 24px;
        }
    </style>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</head>
@php
    use Carbon\Carbon;

    $datas = collect(
        DB::select(
            "
                SELECT
                    tdol.lpk_no,
                    tdo.po_no,
                    tdo.order_date,
                    tdo.stufingdate,
                    tdo.order_qty / mp.case_box_count AS order_qty,
                    tdol.qty_lpk,
                    CAST(ROUND((tdo.order_qty / 1000 * mp.unit_weight::FLOAT8)) AS NUMERIC(10, 0)) AS order_berat,
                    mwl.name AS warnalpk,
                    REPLACE(TO_CHAR(ROUND(CAST((mp.ketebalan * mp.diameterlipat * tdol.qty_gulung * 2 * mpt.berat_jenis) / 1000 AS NUMERIC), 1), 'FM999999990.0'), '.', ',') AS berat_standard,
                    tdol.panjang_lpk,
                    mm.machineno AS nomesin,
                    mp.codebarcode,
                    tdol.qty_gentan AS infure_qtygentan,
                    tdol.qty_gulung AS infure_pjgulunglpk,
                    mp.id,
                    mp.name AS product_name,
                    mp.code_alias,
                    mp.code,
                    mpt.code AS tipe,
                    mpt.name AS tipename,
                    REPLACE(TO_CHAR(mp.ketebalan, 'FM999999990.000'), '.', ',') AS tebal,
                    mp.diameterlipat AS l,
                    mp.productlength AS p,
                    mp.unit_weight AS beratsatuan,
                    REPLACE(TO_CHAR(mp.inflation_thickness, 'FM9999999990.999'), '.', ',') || 'x' ||
                    mp.inflation_fold_diameter AS infure_dimensi,
                    mp.one_winding_m_number AS infure_panjanggulung,
                    CASE
                        WHEN mp.material_classification = '0' THEN 'HD'
                        WHEN mp.material_classification = '1' THEN 'LD'
                        ELSE 'LLD'
                    END AS infure_material,
                    CASE
                        WHEN mp.embossed_classification = '0' THEN 'Tidak Ada'
                        ELSE 'Ada'
                    END AS infure_embose,
                    CASE
                        WHEN mp.surface_classification = '0' THEN 'Tidak Ada'
                        WHEN mp.surface_classification = '1' THEN 'Satu sisi'
                        WHEN mp.surface_classification = '2' THEN 'Dua sisi'
                        WHEN mp.surface_classification = '3' THEN 'Satu Sisi Parsial'
                        ELSE 'Dua Sisi Parsial'
                    END AS infure_corona,
                    CASE
                        WHEN mp.winding_direction_of_the_web = '0' THEN 'Gulungan Kepala depan'
                        WHEN mp.winding_direction_of_the_web = '1' THEN 'Zugara shiri dashi insatsu-men-hyō maki'
                        WHEN mp.winding_direction_of_the_web = '2' THEN 'Zugara atama dashi insatsu-men ura maki'
                        ELSE 'Zugara shiri dashi insatsu-men ura maki'
                    END AS infur_arahgulungan,
                    mli.name AS infure_lakbanwarna,
                    mp.coloring_1 AS infure_mb1_masterbatch,
                    mp.coloring_2 AS infure_mb2,
                    mp.coloring_3 AS infure_mb3,
                    mp.coloring_4 AS infure_mb4,
                    mp.coloring_5 AS infure_mb5,
                    mp.inflation_notes AS infure_catatan,
                    mp.gentan_classification AS infure_gentan,
                    CASE
                        WHEN mp.gazette_classification = '0' THEN 'Gazet Biasa'
                        WHEN mp.gazette_classification = '1' THEN 'Hineri Gazet'
                        WHEN mp.gazette_classification = '2' THEN 'Soko Gazet'
                        WHEN mp.gazette_classification = '3' THEN 'Ato Gazet'
                        WHEN mp.gazette_classification = '4' THEN 'Kata Gazet'
                        ELSE 'Tidak Ada Gazet'
                    END AS infure_gazette,
                    mp.gazette_dimension_a AS infure_gz_dimensi_a,
                    mp.gazette_dimension_b AS infure_gz_dimensi_b,
                    mp.gazette_dimension_c AS infure_gz_dimensi_c,
                    mp.gazette_dimension_d AS infure_gz_dimensi_d,
                    mk.code || ',' || mk.name AS hagata_kodenukigata,
                    mp.extracted_dimension_a AS hagata_a,
                    mk.filename,
                    mp.kodehagata,
                    mp.extracted_dimension_b AS hagata_b,
                    mp.extracted_dimension_c AS hagata_c,
                    mp.number_of_color AS printing_warnadepan,
                    mp.color_spec_1 AS printing_warnadepan1,
                    mp.color_spec_2 AS printing_warnadepan2,
                    mp.color_spec_3 AS printing_warnadepan3,
                    mp.color_spec_4 AS printing_warnadepan4,
                    mp.color_spec_5 AS printing_warnadepan5,
                    mp.back_color_number AS printing_warnabelakang,
                    mp.back_color_1 AS printing_warnabelakang1,
                    mp.back_color_2 AS printing_warnabelakang2,
                    mp.back_color_3 AS printing_warnabelakang3,
                    mp.back_color_4 AS printing_warnabelakang4,
                    mp.back_color_5 AS printing_warnabelakang5,
                    mp.print_type,
                    mjc.name AS printing_jeniscetak,
                    mp.ink_characteristic,
                    msi.name AS printing_sifattinta,
                    mp.endless_printing,
                    mse.name AS printing_endless,
                    mp.winding_direction_of_the_web AS printing_araggulungan,
                    mp.seal_classification,
                    mks.name AS seitai_klasifikasiseal,
                    mp.from_seal_design AS seitai_jaraksealdaripola,
                    mp.kode_plate,
                    mp.lower_sealing_length AS seitai_jaraksealbawah,
                    mp.palet_jumlah_baris AS seitai_jmlhbarispalet,
                    mp.palet_isi_baris AS seitai_isibarispalet,
                    mpb.code AS seitai_kodebox,
                    mpb.name AS seitai_namabox,
                    mp.case_box_count AS seitai_isibox,
                    mpg.code AS seitai_kodegaiso,
                    mpg.name AS seitai_namagaiso,
                    mp.case_gaiso_count AS seitai_isigaiso,
                    mpi.code AS seitai_kodeinner,
                    mpi.name AS seitai_namainner,
                    mp.case_inner_count AS seitai_isiinner,
                    mpl.code AS seitai_kodelayer,
                    mpl.name AS seitai_namalayer,
                    mls.code AS seitai_kodelakban,
                    mls.name AS seitai_namalakban,
                    mp.stampelseitaiid AS seitai_stample,
                    CASE
                        WHEN mpb.box_class = '1' THEN 'Khusus'
                        WHEN mpb.box_class = '2' THEN 'Standar'
                        ELSE ''
                    END AS jns_box,
                    CASE
                        WHEN mpg.box_class = '1' THEN 'Khusus'
                        WHEN mpg.box_class = '2' THEN 'Standar'
                        ELSE ''
                    END AS jns_gaiso,
                    CASE
                        WHEN mpi.box_class = '1' THEN 'Khusus'
                        WHEN mpi.box_class = '2' THEN 'Standar'
                        ELSE ''
                    END AS jns_inner,
                    CASE
                        WHEN mpl.box_class = '1' THEN 'Khusus'
                        WHEN mpl.box_class = '2' THEN 'Standar'
                        ELSE ''
                    END AS jns_layer,
                    '' AS kodeplate,
                    mp.manufacturing_summary AS seitai_catatan,
                    tdol.qty_lpk * mp.productlength / 1000 AS total_assembly_line
                FROM
                    tdorderlpk AS tdol
                    LEFT JOIN tdorder AS tdo ON tdo.id = tdol.order_id
                    LEFT JOIN msproduct AS mp ON mp.id = tdol.product_id
                    LEFT JOIN msproduct_type AS mpt ON mp.product_type_id = mpt.id
                    LEFT JOIN mskatanuki AS mk ON mp.katanuki_id = mk.id
                    LEFT JOIN mspackagingbox AS mpb ON mp.pack_box_id = mpb.id
                    LEFT JOIN mspackaginggaiso AS mpg ON mp.pack_gaiso_id = mpg.id
                    LEFT JOIN mspackaginginner AS mpi ON mp.pack_inner_id = mpi.id
                    LEFT JOIN mspackaginglayer AS mpl ON mp.pack_layer_id = mpl.id
                    LEFT JOIN msmachine AS mm ON mm.id = tdol.machine_id
                    LEFT JOIN mswarnalpk AS mwl ON mwl.id = mp.warnalpkid
                    LEFT JOIN mslakbaninfure AS mli ON mli.id = mp.lakbaninfureid
                    LEFT JOIN mslakbanseitai AS mls ON mls.id = mp.lakbanseitaiid
                    LEFT JOIN msklasifikasiseal AS mks ON mks.code = mp.seal_classification
                    LEFT JOIN msendless AS mse ON mse.code = mp.endless_printing
                    LEFT JOIN mssifattinta AS msi ON msi.code = mp.ink_characteristic
                    LEFT JOIN msjeniscetak AS mjc ON mjc.code = mp.print_type
                WHERE
                    tdol.id IN ($placeholders);
        ",
            $lpk_ids,
        ),
    )->all();
@endphp

<body style="background-color: #CCCCCC;margin: 0">
    @foreach ($datas as $data)
        <div class="print-page">
            <div align="center">
                <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0"
                    width="950" style="padding:25px">
                    <tr>
                        <td>
                            <table width="100%" cellspacing="0" border="0" cellpadding="3">
                                <tr>
                                    <td width="60%" style="vertical-align: bottom">
                                        <div style="width: 220px; text-align:center">
                                            <h1 style="font-size: 27px; border: 2px solid grey; margin: 0px">LPK
                                                {{ $data->lpk_no }}</h1>
                                        </div>
                                    </td>
                                    <td width="30%" style="vertical-align: bottom">
                                        <table width="100%" cellspacing="0" border="0"
                                            style="border: 2px solid black;">
                                            <tr>
                                                <td class="text-right">
                                                    <p style="font-size: 13px; margin: 0px">Panjang Sebenarnya</p>
                                                </td>
                                                <td class="text-right">
                                                    <span class=""
                                                        style="font-size: 14.5px">{{ formatAngka::ribuanCetak($data->panjang_lpk) }}
                                                        m</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">
                                                    <p style="font-size: 13px; margin: 0px">Selisih</p>
                                                </td>
                                                <td class="text-right">
                                                    <span
                                                        style="font-size: 14.5px">{{ formatAngka::ribuanCetak($data->panjang_lpk - $data->total_assembly_line) }}
                                                        m</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="10%" style="vertical-align: bottom">
                                        @php
                                            $url = $data->lpk_no;
                                            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($url);
                                        @endphp
                                        {{ $qrCode }}
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 15px;">
                                <tr>
                                    <td style="padding: 3px; border: 2px solid black;">
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">1. ORDER</font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="padding: 3px; text-align:center; border-left: 2px solid black; border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">Nomor Order</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 30px;font-weight: bold">{{ $data->code }}
                                            </font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;text-align: center;">
                                        <h3 style="font-size: 21.5px">{{ $data->product_name }}</h3>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid black;  text-align:center">
                                        <span style="font-size: 13.5px">Nomor Produk</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 45px;font-weight: bold;">{{ $data->code_alias }}
                                            </font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey;border-left: 2px solid black;text-align:center">
                                        <span style="font-size: 13.5px">PO Number</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 20px; text-transform: uppercase">
                                                {{ $data->po_no }}
                                            </font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Tgl. Order</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">
                                                {{ Carbon::parse($data->order_date)->format('d-M-Y') }}</font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Tgl. Stuffing</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">
                                                {{ Carbon::parse($data->stufingdate)->format('d-M-Y') }}</font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Jml.Order/case</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">
                                                {{ formatAngka::ribuanCetak($data->order_qty) }}
                                                box
                                            </font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Jumlah LPK</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">
                                                {{ formatAngka::ribuanCetak($data->qty_lpk) }}</font>
                                            lbr
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Panjang Order</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">
                                                {{ formatAngka::ribuanCetak($data->panjang_lpk) }}
                                            </font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid black;border-top: 2px solid grey;border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Berat Order</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">
                                                {{ formatAngka::ribuanCetak($data->order_berat) }}</font> kg
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey; border-left: 2px solid black; border-bottom: 2px solid grey; text-align:center">
                                        <span style="font-size: 13.5px">Tipe Produk</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 14.5px;">{{ $data->tipe }}</font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid grey; border-bottom: 2px solid grey;text-align: center;">
                                        <span style="font-size: 13.5px">Nama Tipe</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 14.5px;">{{ $data->tipename }}</font>
                                        </span>
                                    </td>
                                    <td
                                        style="padding: 3px;border-right: 2px solid black; border-bottom: 2px solid grey;">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                            <tr>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px">Tebal</span>
                                                    <br>
                                                    <span
                                                        style="font-size: 14.5px;font-weight: bold;">{{ $data->tebal }}</span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px"></span>
                                                    <br>
                                                    <span style="font-size: 14.5px;font-weight: bold;"> X </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px">Lebar</span>
                                                    <br>
                                                    <span
                                                        style="font-size: 14.5px;font-weight: bold;">{{ $data->l }}</span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px"></span>
                                                    <br>
                                                    <span style="font-size: 14.5px;font-weight: bold;"> X </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px">Panjang</span>
                                                    <br>
                                                    <span
                                                        style="font-size: 14.5px;font-weight: bold;">{{ $data->p }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr style="font-size: 18px;">
                                    <td
                                        style="padding: 3px;border-left: 2px solid black; border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">Warna LPK : </span>
                                        <span style="font-size: 14.5px">{{ $data->warnalpk }}</span>
                                    </td>
                                    <td style="padding: 3px; border-right: 2px solid black;">
                                        <span style="font-size: 13.5px">Nomor Barcode : </span>
                                        <span
                                            style="font-weight: bold; font-size: 14.5px">{{ $data->codebarcode }}</span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border: 2px solid black;">
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">2. INFURE</font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="padding: 3px;border-left: 2px solid black; border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Nomor Mesin</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 21.5px;font-weight: bold;">
                                                {{ $data->nomesin }}
                                            </font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Dimensi Infure</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">{{ $data->infure_dimensi }}</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Panjang Gulung</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;text-align: center;">
                                                {{ formatAngka::ribuanCetak($data->infure_pjgulunglpk) }} m</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Jml Gentan</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">
                                                {{ $data->infure_qtygentan }}
                                            </font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Berat Standar</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">
                                                {{ $data->berat_standard }}
                                            </font> Kg
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey; text-align: center;">
                                        <span style="font-size: 13.5px">Material</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">{{ $data->infure_material }}</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid black; text-align: center;">
                                        <span style="font-size: 13.5px">Arah Gulung</span>
                                        <br>
                                        <span>
                                            <font style="font-size: 16px;">{{ $data->infur_arahgulungan }} </font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border-left: 2px solid black; border-top: 2px solid grey; border-right: 2px solid grey;"
                                        width="45%">
                                        <span style="font-size: 13.5px">Master Batch</span> <br>
                                        <span style="font-size: 14.5px; font-weight: bold;">1.
                                            {{ $data->infure_mb1_masterbatch }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">2.
                                            {{ $data->infure_mb2 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">3.
                                            {{ $data->infure_mb3 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">4.
                                            {{ $data->infure_mb4 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">5.
                                            {{ $data->infure_mb5 }}</span><br>
                                        <table>
                                            <tr>
                                                <td>
                                                    <span style="font-size: 13.5px">Catatan : </span>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->infure_catatan }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-top: 2px solid grey; border-right: 2px solid grey;"
                                        width="20%">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="border-top: 2px solid grey; text-align: center;">
                                                    <span style="font-size: 13.5px">Embos</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->infure_embose }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-top: 2px solid grey; text-align: center;">
                                                    <span style="font-size: 13.5px">Corona Discharge</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->infure_corona }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px">Warna Lakban</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->infure_lakbanwarna }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td
                                        style="border-top: 2px solid grey; border-right: 2px solid black; border-bottom:none">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="border-bottom: 2px solid grey;text-align:center">
                                                    <span
                                                        style="font-size: 13.5px">{{ $data->infure_gazette }}</span><br>
                                                </td>
                                            </tr>
                                            @if ($data->infure_gazette != 'Tidak Ada Gazet')
                                                <tr>
                                                    <td style="text-align:center">
                                                        <div class="image-container">
                                                            <img src="{{ asset('asset/image/Gazette.png') }}"
                                                                alt="" style="height:100%; width:100%">
                                                            <div class="text-infure-gz-dimensi-A"
                                                                style="font-size: 14.5px">
                                                                {{ $data->infure_gz_dimensi_a }}</div>
                                                            <div class="text-infure-gz-dimensi-B"
                                                                style="font-size: 14.5px">
                                                                {{ $data->infure_gz_dimensi_b }}</div>
                                                            <div class="text-infure-gz-dimensi-C"
                                                                style="font-size: 14.5px">
                                                                {{ $data->infure_gz_dimensi_c }}</div>
                                                            <div class="text-infure-gz-dimensi-D"
                                                                style="font-size: 14.5px">
                                                                {{ $data->infure_gz_dimensi_d }}</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border: 2px solid black;">
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">3. PRINTING</font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border-left: 2px solid black; border-right: 2px solid grey;"
                                        width="40%">
                                        <span style="font-size: 13.5px">Warna Depan :
                                            {{ $data->printing_warnadepan }}
                                            warna
                                        </span> <br>
                                        <span style="font-size: 14.5px; font-weight: bold;">1.
                                            {{ $data->printing_warnadepan1 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">2.
                                            {{ $data->printing_warnadepan2 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">3.
                                            {{ $data->printing_warnadepan3 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">4.
                                            {{ $data->printing_warnadepan4 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">5.
                                            {{ $data->printing_warnadepan5 }}</span><br>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;" width="40%">
                                        <span style="font-size: 13.5px">Warna Belakang :
                                            {{ $data->printing_warnabelakang }}
                                            warna </span> <br>
                                        <span style="font-size: 14.5px; font-weight: bold;">1.
                                            {{ $data->printing_warnabelakang1 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">2.
                                            {{ $data->printing_warnabelakang2 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">3.
                                            {{ $data->printing_warnabelakang3 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">4.
                                            {{ $data->printing_warnabelakang4 }}</span><br>
                                        <span style="font-size: 14.5px; font-weight: bold;">5.
                                            {{ $data->printing_warnabelakang5 }}</span><br>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid black;" width="40%">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="border-bottom: 2px solid grey;text-align: center;">
                                                    <span style="font-size: 13.5px">Jenis Cetak</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_jeniscetak }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 2px solid grey;text-align: center;">
                                                    <span style="font-size: 13.5px">Jenis Tinta</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_sifattinta }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 2px solid grey;text-align: center;">
                                                    <span style="font-size: 13.5px">Cetak Endless</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_endless }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;">
                                                    <span style="font-size: 13.5px">Kode Plate</span><br>
                                                    <span
                                                        style="font-size: 14.5px; font-weight: bold;">{{ $data->kode_plate }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border: 2px solid black;">
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">4. SEITAI</font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="padding: 3px;border-left: 2px solid black; border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">
                                            Seal <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->seitai_klasifikasiseal }}</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">
                                            Jarak Seal Bawah <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->seitai_jaraksealbawah }} mm</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">
                                            Jarak Seal Dari Pola <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->seitai_jaraksealdaripola }} mm</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">
                                            Jumlah Baris Palet <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->seitai_jmlhbarispalet }}</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid grey;">
                                        <span style="font-size: 13.5px">
                                            Isi Baris Palet <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->seitai_isibarispalet }}</font>
                                        </span>
                                    </td>
                                    <td style="padding: 3px;border-right: 2px solid black;">
                                        <span style="font-size: 13.5px">
                                            Kode Hagata <br>
                                            <font style="font-size: 14.5px;font-weight: bold;">
                                                {{ $data->kodehagata }}
                                            </font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey; border-left: 2px solid black;"
                                        width="8%">
                                        <span>
                                            -
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        Gaiso
                                                    </font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">Box
                                                    </font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        Inner
                                                    </font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        Layer
                                                    </font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        Lakban
                                                    </font>
                                                    <br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey;text-align: center; vertical-align: top;"
                                        width="8%">
                                        <span style="font-size: 13.5px">
                                            Kode
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_kodegaiso }}</font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_kodebox }}</font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_kodeinner }}</font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_kodelayer }}</font>
                                                    <br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_kodelakban }}</font>
                                                    <br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey;text-align: center; vertical-align: top;"
                                        width="12%">
                                        <span style="font-size: 13.5px">
                                            Isi
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_isigaiso }}</font>
                                                    lembar<br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_isibox }}</font>
                                                    lembar<br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_isiinner }}</font>
                                                    lembar<br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey;text-align: center; vertical-align: top;"
                                        width="8%">
                                        <span style="font-size: 13.5px">
                                            Jenis
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->jns_gaiso }}
                                                    </font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->jns_box }}
                                                    </font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->jns_inner }}
                                                    </font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->jns_layer }}
                                                    </font><br>
                                                    {{-- <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namalakban }}</font><br> --}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey;text-align: center; vertical-align: top;"
                                        width="10%">
                                        <span style="font-size: 13.5px">
                                            Stample
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold; font-size: 13.5px">
                                                        {{ $data->seitai_stample }}
                                                    </font>
                                                    <br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey;vertical-align: top;"
                                        width="33%">
                                        <span style="font-size: 13.5px">
                                            Nama
                                        </span>
                                        <table>
                                            <tr>
                                                <td>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_namagaiso }}</font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_namabox }}</font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_namainner }}</font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_namalayer }}</font><br>
                                                    <font style="font-weight: bold;" style="font-size: 14.5px">
                                                        {{ $data->seitai_namalakban }}</font><br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td
                                        style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid black; text-align:center">
                                        <img src="{{ asset('storage/' . $data->filename) }}" alt=""
                                            style="height:100%; width:100%">
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0" style="text-align:center;">
                                <tr>
                                    <td style="padding: 3px;border-top: 2px solid grey; border-right: 2px solid grey; border-left: 2px solid black;"
                                        width="79%"> - </td>
                                    <td style="padding: 3px;border-right: 2px solid black; text-align:center"
                                        width="21%">
                                        <table style="margin: 0 auto;">
                                            <tr>
                                                <td style="text-align:center">
                                                    <span
                                                        style="font-weight: bold;font-size: 14.5px;color:red;">A=</span>
                                                    <span
                                                        style="font-weight: bold;font-size: 14.5px;">{{ $data->hagata_a }}</span>
                                                </td>
                                                <td style="text-align:center">
                                                    <span
                                                        style="font-weight: bold;font-size: 14.5px;">B={{ $data->hagata_b }}</span><br>
                                                </td>
                                                <td style="text-align:center">
                                                    <span
                                                        style="font-weight: bold;font-size: 14.5px;color:blue;">C=</span>
                                                    <span
                                                        style="font-weight: bold;font-size: 14.5px;">{{ $data->hagata_c }}</span><br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border: 2px solid black;">
                                        <span>
                                            <font style="font-size: 16px;font-weight: bold;">5. CATATAN PRODUKSI</font>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 3px;border-left: 2px solid black; border-bottom: 2px solid black; border-right: 2px solid grey; font-size: 13.5px"
                                        width="70%">
                                        {{-- <font style="font-weight: bold;font-size: 14.5px;">{{ $data->seitai_catatan }}</font><br> --}}
                                        <textarea name="" id="" cols="90" rows="12"
                                            style="border: none;font-weight: bold;font-size: 14.5px;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">{{ $data->seitai_catatan }}</textarea>
                                    </td>
                                    <td
                                        style="padding: 3px;border-bottom: 2px solid black; border-right: 2px solid black;">
                                        <span>
                                            <p style="font-size: 13.5px">Blow Ratio</p>
                                            <p style="font-size: 13.5px">Diameter KB</p>
                                            <p style="font-size: 13.5px">Tinggi Neck In</p>
                                            <hr>
                                            <p style="font-size: 13.5px">Operator</p>
                                            <p style="font-size: 13.5px">Ass Leader</p>
                                            <p style="font-size: 13.5px">Leader</p>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>

</html>

<style>
    .text-right {
        text-align: right;
    }
</style>
