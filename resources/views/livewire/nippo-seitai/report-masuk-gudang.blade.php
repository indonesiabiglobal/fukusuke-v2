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
    // header("Content-type: application/vnd-ms-excel");
	// header("Content-Disposition: attachment; filename=test.xls");
    $data = collect(
        DB::select("
        SELECT 
        tdpg.production_date AS production_date, 
        tdpg.nomor_palet AS nomor_palet, 
            tdpg.nomor_lot AS nomor_lot,
                tdpg.work_shift AS work_shift,  
                tdpg.employee_id AS employee_id,
                me.empname as namapetugas,
                tdpg.product_id AS product_id,
                mp.code_alias as nocode,
                mp.name as namaproduk,
                mp.palet_jumlah_baris as tinggi,
                mp.palet_isi_baris as jmlbaris,
                tdpg.qty_produksi/cast(mp.case_box_count as  INTEGER) AS qty_produksi
            FROM  tdProduct_Goods AS tdpg
            left JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.id
                left join msproduct as mp on mp.id=tdpg.product_id
                left join msemployee as me on me.id=tdpg.employee_id
            WHERE tdpg.nomor_palet = (LTRIM(RTRIM('$no_palet')))
        "));
        //  dd($data[0]->namaproduk);
        $parts = explode('-', $data[0]->nomor_palet);
        $date = Carbon::parse( $data[0]->production_date);
        // dd( );
@endphp
<body style="background-color: #CCCCCC;margin: 0">
    <div align="center">
        <table class="bayangprint" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="730" style="padding:25px">
            <tbody>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="50%">
                                    <span>
                                        <font style="font-size: 22px; font-weight: bold;">KARTU MASUK GUDANG (P)</font>
                                    </span>
                                </td>
                                <td width="50%" align="center">
                                    <span>
                                        <font>Nomor : </font>
                                        <font style="font-size: 22px; font-weight: bold;">{{ $parts[1] }}</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5px;">
                            <tr>
                                <td width="16%" style="border: 1px solid black;font-weight: bold;padding: 3px;background-color:black;color:white;">
                                    <span>
                                        <font>Gudang</font>
                                    </span>
                                </td>
                                <td width="34%" style="border: 1px solid black;padding: 3px;">
                                    <span>
                                        <font>Tanggal : {{ $date->format('d/m/Y') }}</font>
                                    </span>
                                </td>
                                <td width="5%"></td>
                                <td width="45%"></td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="16%" style="border: 1px solid black;padding: 3px;">
                                    <span>
                                        <font>Petugas</font>
                                    </span>
                                </td>
                                <td width="17%" style="border: 1px solid black;padding: 3px;">
                                    <span>
                                        <font>Ass. Leader</font>
                                    </span>
                                </td>
                                <td width="17%" style="border: 1px solid black;padding: 3px;">
                                    <span>
                                        <font>Gudang</font>
                                    </span>
                                </td>
                                <td width="5%"></td>
                                <td width="50%" style="border: 1px dashed black;padding: 3px;">
                                    <span>
                                        <font style="font-size: 55px;">{{ $parts[0] }}</font>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5px;">
                            <tr>
                                <td width="60%">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Nomor LOT</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>Jumlah Box</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>Jumlah Revisi</span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Operator</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>Shift</span>
                                            </td>
                                        </tr>
                                        @php
                                            $totalqty=0;
                                        @endphp
                                        @foreach ($data as $dt )
                 
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>{{$dt->nomor_lot}}</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>{{$dt->qty_produksi}}</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>{{$dt->namapetugas}}</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>{{$dt->work_shift}}</span>
                                            </td>
                                        </tr>
                                        @php
                                            $totalqty=$totalqty + $dt->qty_produksi
                                        @endphp
                                        
                                        @endforeach

                                        {{-- <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>96402217</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>0</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span></span>
                                            </td>
                                            <td width="30%" style="border: 1px solid black;padding: 3px;">
                                                <span>Winda Rahayu</span>
                                            </td>
                                            <td width="10%" style="border: 1px solid black;padding: 3px;">
                                                <span>2</span>
                                            </td>
                                        </tr> --}}
                                    </table>
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="border: 1px solid black;padding: 3px;">
                                                <p>Catatan : </p>
                                                <span style="border-top: 1px solid black;">No. Dok: FKI/I/frm/GU/0015</span><br>
                                                <span style="border-top: 1px solid black;">Revisi : 1</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="40%">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="border: 1px solid black;padding: 10px;">
                                                <span style="vertical-align: top;">Nomor Produk </span>
                                                <font style="font-size: 33.2px;">{{ $data[0]->nocode }}</font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding: 3px;">
                                                <span style="vertical-align: top;">Nama Produk</span><br>
                                                <p style="font-size: 24px;text-align:center;">{{ $data[0]->namaproduk }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding: 3px;">
                                                <span style="vertical-align: top;">Jumlah Kotak (Box) </span>
                                                <font style="font-size: 42px;font-weight:bold;">{{$totalqty}}</font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding: 15px;">
                                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td width="30%">Tinggi</td>
                                                        <td width="40%">Jumlah/Baris</td>
                                                        <td width="30%" style="text-align: center">Satuan</td>
                                                    </tr>
                                                </table>
                                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td width="30%" style="text-align: center;font-size:30px;border: 1px solid black;">{{ $data[0]->tinggi }}</td>
                                                        <td width="5%" style="text-align: center;font-size:20px;">
                                                            &nbsp;X&nbsp; 
                                                        </td>
                                                        <td width="30%" style="text-align: center;font-size:30px;border: 1px solid black;">{{ $data[0]->jmlbaris }}</td>
                                                        <td width="5%" style="text-align: center;font-size:20px;">
                                                            &nbsp;+&nbsp; 
                                                        </td>
                                                        <td width="30%" style="text-align: center;font-size:30px;border: 1px solid black;">{{ $data[0]->jmlbaris }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="border: 1px solid black;padding-top: 5px;padding-bottom: 17px;">
                                                <p style="vertical-align: top;">Pengecekan Kebersihan Produk</p>
                                                <input type="checkbox"> Petugas Seitai 
                                                <input type="checkbox"> Sebelum Stuffing 
                                            </td>
                                        </tr>
                                    </table>
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