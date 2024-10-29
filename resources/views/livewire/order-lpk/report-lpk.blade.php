<!DOCTYPE html>
<html lang="en">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
crossorigin="anonymous">
</script>
<script>
    // $(function() {
    //     $('#button').on('click', function() {
    //         window.print();
    //     });
    // });
    // $(function() {
    //     var hasPrinted = false;

    //     window.onbeforeprint = function() {
    //         hasPrinted = false;
    //     };

    //     window.onafterprint = function() {
    //         hasPrinted = true;
    //         tryToCloseWindow();
    //     };

    //     function tryToCloseWindow() {
    //         if (hasPrinted) {
    //             setTimeout(function() {
    //                 window.close();
    //                 // Jika window.close() tidak berhasil, coba metode alternatif
    //                 setTimeout(function() {
    //                     if (!window.closed) {
    //                         alert("Pencetakan selesai. Silakan tutup jendela ini secara manual.");
    //                     }
    //                 }, 1000);
    //             }, 100);
    //         }
    //     }

    //     // Memicu pencetakan
    //     setTimeout(function() {
    //         window.print();
    //     }, 1000);
    // });
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        @media print {
            /* Menyembunyikan header, footer, dan URL */
            @page {
                margin: 0;
            }
            header, footer, .page-header, .page-footer {
                display: none;
            }
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
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
</head>
@php
    use Carbon\Carbon;

    $data = collect(
        DB::select("
                SELECT tdol.lpk_no,tdo.po_no,tdo.order_date, tdo.stufingdate,tdo.order_qty/mp.case_box_count as order_qty,tdol.qty_lpk,
                ((tdol.qty_lpk *mp.unit_weight)/mp.case_box_count)/1000 as order_berat,mwl.name as warnalpk,
                (mp.ketebalan * mp.diameterlipat * tdol.qty_gulung * 2 * mpt.berat_jenis ) / 1000 AS berat_standard,
                tdol.panjang_lpk,mm.machineno as nomesin,mp.codebarcode,tdol.qty_gentan as infure_qtygentan,tdol.qty_gulung as infure_pjgulunglpk,
                mp.id, mp.name as product_name,mp.code_alias,mp.code,
                mpt.code as tipe , mpt.name as tipename,mp.ketebalan as t, mp.diameterlipat as l, mp.productlength as p,
                mp.ketebalan ||'x'||mp.diameterlipat||'x'||mp.productlength as dimensi_txlxp,
                mp.unit_weight as beratsatuan,mp.inflation_thickness ||'x'||mp.inflation_fold_diameter as infure_dimensi,
                mp.one_winding_m_number as infure_panjanggulung,
                 case when mp.material_classification='0' then 'HD' when mp.material_classification='1' then 'LD' ELSE 'lld' END  as infure_material,
               case when mp.embossed_classification='0' then 'Tidak Ada' else 'Ada' end as infure_embose,case when mp.surface_classification='0' then 'Tidak Ada' when mp.surface_classification='1' then 'Satu sisi' when mp.surface_classification='2' then 'Dua sisi' when mp.surface_classification='3' then 'Satu Sisi Parsial' else 'Dua Sisi Parsial' end as infure_corona,
                case when mp.winding_direction_of_the_web='0' then 'Gulungan Kepala depan' when mp.winding_direction_of_the_web='1' then 'Zugara shiri dashi insatsu-men-hyō maki' when mp.winding_direction_of_the_web='2' then 'Zugara atama dashi insatsu-men ura maki' ELSE 'Zugara shiri dashi insatsu-men ura maki' END  as infur_arahgulungan,
                mli.name as infure_lakbanwarna,
                mp.coloring_1 as infure_mb1_masterbatch,
                mp.coloring_2 as infure_mb2, mp.coloring_3 as infure_mb3,mp.coloring_4 as infure_mb4,mp.coloring_5 as infure_mb5,
                mp.inflation_notes as infure_catatan,
                mp.gentan_classification as infure_gentan,(case when mp.gazette_classification='0' then 'Gazet Biasa'
		when mp.gazette_classification='1' then 'Hineri Gazet' when mp.gazette_classification='2' then 'Soko Gazet'  when mp.gazette_classification='3' then 'Ato Gazet'  when mp.gazette_classification='4' then 'Kata Gazet' else 'Tidak Ada Gazet' end ) as infure_gazette,
                mp.gazette_dimension_a as infure_gz_dimensi_a,mp.gazette_dimension_b as infure_gz_dimensi_b,
                mp.gazette_dimension_c as infure_gz_dimensi_c,mp.gazette_dimension_d as infure_gz_dimensi_d,
                mk.code ||','||mk.name as hagata_kodenukigata,mp.extracted_dimension_a as hagata_a,
                mk.filename,mp.kodehagata,
                mp.extracted_dimension_b as hagata_b,mp.extracted_dimension_c as hagata_c,
                mp.number_of_color as printing_warnadepan,mp.color_spec_1 as printing_warnadepan1,mp.color_spec_2 as printing_warnadepan2,mp.color_spec_3 as printing_warnadepan3,
                mp.color_spec_4 as printing_warnadepan4,mp.color_spec_5 as printing_warnadepan5,
                mp.back_color_number as printing_warnabelakang,mp.back_color_1 as printing_warnabelakang1,
                mp.back_color_2 as printing_warnabelakang2,mp.back_color_3 as printing_warnabelakang3,
                mp.back_color_4 as printing_warnabelakang4,mp.back_color_5 as printing_warnabelakang5,
                mp.print_type,mjc.name as printing_jeniscetak,
                mp.ink_characteristic,msi.name as printing_sifattinta,
                mp.endless_printing,mse.name as printing_endless,mp.winding_direction_of_the_web as printing_araggulungan,
                mp.seal_classification, mks.name as seitai_klasifikasiseal,
                mp.from_seal_design as seitai_jaraksealdaripola,
                mp.lower_sealing_length as seitai_jaraksealbawah,mp.palet_jumlah_baris as seitai_jmlhbarispalet,
                mp.palet_isi_baris as seitai_isibarispalet,	mpb.code as seitai_kodebox , mpb.name as seitai_namabox,
                mp.case_box_count as seitai_isibox,
                mpg.code as seitai_kodegaiso ,mpg.name as seitai_namagaiso,mp.case_gaiso_count as seitai_isigaiso,
                mpi.code as seitai_kodeinner, mpi.name as seitai_namainner,mp.case_inner_count as seitai_isiinner,
                mpl.code as seitai_kodelayer,mpl.name as seitai_namalayer,
                mls.code as seitai_kodelakban,mls.name as seitai_namalakban,mp.stampelseitaiid as seitai_stample,
                case when mpb.box_class='1' then 'Khusus' when  mpb.box_class='2' then 'Standar' else '' end as jns_box,
				case when mpg.box_class='1' then 'Khusus' when  mpg.box_class='2' then 'Standar' else '' end as jns_gaiso,
				case when mpi.box_class='1' then 'Khusus' when  mpi.box_class='2' then 'Standar' else '' end as jns_inner,
				case when mpl.box_class='1' then 'Khusus' when  mpl.box_class='2' then 'Standar' else '' end as jns_layer,'' as kodeplate,
                mp.manufacturing_summary as seitai_catatan, tdol.total_assembly_line
                from tdorderlpk as tdol
                left join tdorder as tdo on tdo.id=tdol.order_id
                left JOIN msproduct as mp on mp.id=tdol.product_id
                left JOIN msproduct_type as mpt on mp.product_type_id=mpt.id
                left JOIN mskatanuki as mk on mp.katanuki_id=mk.id
                left JOIN mspackagingbox as mpb on mp.pack_box_id=mpb.id
                left jOIN mspackaginggaiso as mpg on  mp.pack_gaiso_id=mpg.id
                left join mspackaginginner as mpi on mp.pack_inner_id=mpi.id
                left join mspackaginglayer as mpl on mp.pack_layer_id=mpl.id
                left join msmachine as mm on mm.id=tdol.machine_id
                left join mswarnalpk as mwl on mwl.id=mp.warnalpkid
                left join mslakbaninfure as mli on mli.id=mp.lakbaninfureid
                left join mslakbanseitai as mls on mls.id=mp.lakbanseitaiid
                left join msklasifikasiseal as mks on mks.code=mp.seal_classification
                left join msendless as mse on mse.code=mp.endless_printing
                left join mssifattinta as msi  on msi.code=mp.ink_characteristic
				left join msjeniscetak as mjc  on mjc.code=mp.print_type
                where tdol.id='$lpk_id'
        "),
    )->first();
@endphp

<body style="background-color: #CCCCCC;margin: 0">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="950"
            style="padding:25px">
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" border="0" cellpadding="3">
                            <tr>
                                <td width="60%">
                                    <div style="width: 220px; text-align:center">
                                        <h1 style="font-size: 27px; border: 1px solid grey;">LPK {{ $data->lpk_no }}</h1>
                                    </div>
                                </td>
                                <td width="30%" style="border: 1px solid black;">
                                    <p style="font-size: 13px">Panjang Sebenarnya <span class="" style="font-size: 14.5px">{{ $data->panjang_lpk }} m</span></p>
                                    <p style="font-size: 13px">Selisih - <span style="font-size: 14.5px">{{ $data->panjang_lpk - $data->total_assembly_line }} m</span></p>
                                </td>
                                <td width="10%">
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
                                <td style="padding: 3px; border: 1px solid black;">
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">1. ORDER</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px; text-align:center; border-left: 1px solid black; border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">Nomor Order</span>
                                    <br>
                                    <span >
                                        <font style="font-size: 30px;font-weight: bold">{{ $data->code }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;text-align: center;">
                                    <h3 style="font-size: 21.5px">{{ $data->product_name }}</h3>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid black;  text-align:center">
                                    <span style="font-size: 13.5px">Nomor Produk</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 45px;font-weight: bold;">{{ $data->code_alias }}</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey;border-left: 1px solid black;text-align:center">
                                    <span style="font-size: 13.5px">PO Number</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 20px;">{{ $data->po_no }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Tgl. Order</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;">
                                            {{ Carbon::parse($data->order_date)->format('d-M-Y') }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Tgl. Stuffing</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">
                                            {{ Carbon::parse($data->stufingdate)->format('d-M-Y') }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Jml.Order/case</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;">{{ $data->order_qty }} box</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Jumlah LPK</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">{{ $data->qty_lpk }}</font>
                                        lbr
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Panjang Order</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;">{{ $data->panjang_lpk }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid black;border-top: 1px solid grey;border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">berat Order</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">
                                            {{ $data->order_berat }}</font> kg
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-right: 1px solid grey; border-left: 1px solid black; border-bottom: 1px solid grey; text-align:center">
                                    <span style="font-size: 13.5px">Tipe Produk</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 14.5px;">{{ $data->tipe }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; border-bottom: 1px solid grey;text-align: center;">
                                    <span style="font-size: 13.5px">Nama Tipe</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 14.5px;">{{ $data->tipename }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid black; border-bottom: 1px solid grey;">
                                    <table width="100%" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                        <tr>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px">Tebal</span>
                                                <br>
                                                <span style="font-size: 14.5px;font-weight: bold;">{{ $data->t }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px"></span>
                                                <br>
                                                <span style="font-size: 14.5px;font-weight: bold;"> X </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px">Lebar</span>
                                                <br>
                                                <span style="font-size: 14.5px;font-weight: bold;">{{ $data->l }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px"></span>
                                                <br>
                                                <span style="font-size: 14.5px;font-weight: bold;"> X </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px">Panjang</span>
                                                <br>
                                                <span style="font-size: 14.5px;font-weight: bold;">{{ $data->p }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr style="font-size: 18px;">
                                <td style="padding: 3px;border-left: 1px solid black; border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">Warna LPK : </span>
                                    <span style="font-size: 14.5px">{{ $data->warnalpk }}</span>
                                </td>
                                <td style="padding: 3px; border-right: 1px solid black;">
                                    <span style="font-size: 13.5px">Nomor Barcode : </span>
                                    <span style="font-weight: bold; font-size: 14.5px">{{ $data->codebarcode }}</span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border: 1px solid black;">
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">2. INFURE</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-left: 1px solid black; border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Nomor Mesin</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 21.5px;font-weight: bold;">{{ $data->nomesin }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Dimensi Infure</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;">{{ $data->infure_dimensi }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Panjang Gulung</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;text-align: center;">
                                            {{ $data->infure_pjgulunglpk }} m</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Jml Gentan</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">{{ $data->infure_qtygentan }}
                                        </font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Berat Standar</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">{{ $data->berat_standard }}
                                        </font> Kg
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey; text-align: center;">
                                    <span style="font-size: 13.5px">Material</span>
                                    <br>
                                    <span>
                                        <font style="font-size: 16px;">{{ $data->infure_material }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid black; text-align: center;">
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
                                <td style="padding: 3px;border-left: 1px solid black; border-top: 1px solid grey; border-right: 1px solid grey;" width="45%">
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
                                <td style="border-top: 1px solid grey; border-right: 1px solid grey;" width="20%">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="border-top: 1px solid grey; text-align: center;">
                                                <span style="font-size: 13.5px">Embos</span><br>
                                                <span
                                                    style="font-size: 14.5px; font-weight: bold;">{{ $data->infure_embose }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border-top: 1px solid grey; text-align: center;">
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
                                <td style="border-top: 1px solid grey; border-right: 1px solid black; border-bottom:none">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="border-bottom: 1px solid grey;text-align:center">
                                                <span style="font-size: 13.5px">{{ $data->infure_gazette }}</span><br>
                                            </td>
                                        </tr>
                                        @if ($data->infure_gazette != 'Tidak Ada Gazet')
                                            <tr>
                                                <td style="border-bottom: 1px solid black; text-align:center">
                                                    <div class="image-container">
                                                        <img src="{{ asset('asset/image/Gazette.png') }}" alt=""
                                                        style="height:100%; width:100%">
                                                        <div class="text-infure-gz-dimensi-A" style="font-size: 14.5px">{{ $data->infure_gz_dimensi_a }}</div>
                                                        <div class="text-infure-gz-dimensi-B" style="font-size: 14.5px">{{ $data->infure_gz_dimensi_b }}</div>
                                                        <div class="text-infure-gz-dimensi-C" style="font-size: 14.5px">{{ $data->infure_gz_dimensi_c }}</div>
                                                        <div class="text-infure-gz-dimensi-D" style="font-size: 14.5px">{{ $data->infure_gz_dimensi_d }}</div>
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
                                <td style="padding: 3px;border: 1px solid black;">
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">3. PRINTING</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-left: 1px solid black; border-right: 1px solid grey;" width="40%">
                                    <span style="font-size: 13.5px">Warna Depan : {{ $data->printing_warnadepan }} warna </span> <br>
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
                                <td style="padding: 3px;border-right: 1px solid grey;" width="40%">
                                    <span style="font-size: 13.5px">Warna Belakang : {{ $data->printing_warnabelakang }} warna </span> <br>
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
                                <td style="padding: 3px;border-right: 1px solid black;" width="40%">
                                    <table width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="border-bottom: 1px solid grey;text-align: center;">
                                                <span style="font-size: 13.5px">Jenis Cetak</span><br>
                                                <span style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_jeniscetak }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border-bottom: 1px solid grey;text-align: center;">
                                                <span style="font-size: 13.5px">Jenis Tinta</span><br>
                                                <span
                                                    style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_sifattinta }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border-bottom: 1px solid grey;text-align: center;">
                                                <span style="font-size: 13.5px">Cetak Endless</span><br>
                                                <span
                                                    style="font-size: 14.5px; font-weight: bold;">{{ $data->printing_endless }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13.5px">Kode Plate</span><br>
                                                <span
                                                    style="font-size: 14.5px; font-weight: bold;">{{ $data->kodeplate }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border: 1px solid black;">
                                    <span>
                                        <font style="font-size: 16px;font-weight: bold;">4. SEITAI</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-left: 1px solid black; border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">
                                        Seal <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">
                                            {{ $data->seitai_klasifikasiseal }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">
                                        Jarak Seal Bawah <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">
                                            {{ $data->seitai_jaraksealbawah }} mm</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">
                                        Jarak Seal Dari Pola <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">
                                            {{ $data->seitai_jaraksealdaripola }} mm</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">
                                        Jumlah Baris Palet <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">
                                            {{ $data->seitai_jmlhbarispalet }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid grey;">
                                    <span style="font-size: 13.5px">
                                        Isi Baris Palet <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">
                                            {{ $data->seitai_isibarispalet }}</font>
                                    </span>
                                </td>
                                <td style="padding: 3px;border-right: 1px solid black;">
                                    <span style="font-size: 13.5px">
                                        Kode Hagata <br>
                                        <font style="font-size: 14.5px;font-weight: bold;">{{ $data->kodehagata }}
                                        </font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey; border-left: 1px solid black;" width="8%">
                                    <span>
                                        -
                                    </span>
                                    <table>
                                        <tr>
                                            <td>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">Gaiso</font><br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">Box</font><br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">Inner</font><br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">Layer</font><br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">Lakban</font><br>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey;text-align: center; vertical-align: top;"
                                    width="8%">
                                    <span style="font-size: 13.5px">
                                        Kode
                                    </span>
                                    <table>
                                        <tr>
                                            <td>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_kodegaiso }}</font>
                                                <br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_kodebox }}</font>
                                                <br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_kodeinner }}</font>
                                                <br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_kodelayer }}</font>
                                                <br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_kodelakban }}</font>
                                                <br>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey;text-align: center; vertical-align: top;"
                                    width="12%">
                                    <span style="font-size: 13.5px">
                                        Isi
                                    </span>
                                    <table>
                                        <tr>
                                            <td>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_isigaiso }}</font>
                                                lembar<br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_isibox }}</font>
                                                lembar<br>
                                                <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_isiinner }}</font>
                                                lembar<br>
                                            </td>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey;text-align: center; vertical-align: top;"
                        width="8%">
                        <span style="font-size: 13.5px">
                            Jenis
                        </span>
                        <table>
                            <tr>
                                <td>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->jns_gaiso }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->jns_box }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->jns_inner }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->jns_layer }}</font><br>
                                    {{-- <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namalakban }}</font><br> --}}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey;text-align: center; vertical-align: top;"
                        width="10%">
                        <span style="font-size: 13.5px">
                            Stample
                        </span>
                        <table>
                            <tr>
                                <td>
                                    <font style="font-weight: bold; font-size: 13.5px">{{ $data->seitai_stample }} </font><br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey;vertical-align: top;"
                        width="33%">
                        <span style="font-size: 13.5px">
                            Nama
                        </span>
                        <table>
                            <tr>
                                <td>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namagaiso }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namabox }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namainner }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namalayer }}</font><br>
                                    <font style="font-weight: bold;" style="font-size: 14.5px">{{ $data->seitai_namalakban }}</font><br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid black; text-align:center">
                        <img src="{{ asset('storage/' . $data->filename) }}" alt=""
                            style="height:100%; width:100%">
                    </td>
                </tr>
        </table>
        <table width="100%" cellspacing="0" cellpadding="0" style="text-align:center;">
            <tr>
                <td style="padding: 3px;border-top: 1px solid grey; border-right: 1px solid grey; border-left: 1px solid black;" width="79%"> - </td>
                <td style="padding: 3px;border-right: 1px solid black; text-align:center"
                    width="21%">
                    <table style="margin: 0 auto;">
                        <tr>
                            <td style="text-align:center">
                                <span style="font-weight: bold;font-size: 14.5px;color:red;">A=</span>
                                <span style="font-weight: bold;font-size: 14.5px;">{{ $data->hagata_a }}</span>
                            </td>
                            <td style="text-align:center">
                                <span style="font-weight: bold;font-size: 14.5px;">B={{ $data->hagata_b }}</span><br>
                            </td>
                            <td style="text-align:center">
                                <span style="font-weight: bold;font-size: 14.5px;color:blue;">C=</span>
                                <span style="font-weight: bold;font-size: 14.5px;">{{ $data->hagata_c }}</span><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="padding: 3px;border: 1px solid black;">
                    <span>
                        <font style="font-size: 16px;font-weight: bold;">5. CATATAN PRODUKSI</font>
                    </span>
                </td>
            </tr>
        </table>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="padding: 3px;border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid grey; font-size: 13.5px" width="70%">
                    {{-- <font style="font-weight: bold;font-size: 14.5px;">{{ $data->seitai_catatan }}</font><br> --}}
                    <textarea name="" id="" cols="90" rows="12"  style="border: none;font-weight: bold;font-size: 14.5px;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">{{ $data->seitai_catatan }}</textarea>
                </td>
                <td style="padding: 3px;border-bottom: 1px solid black; border-right: 1px solid black;">
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
    </div>
</body>

</html>
