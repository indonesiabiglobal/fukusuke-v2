<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        {{-- <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="26">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="26">
            </span>
        </a>
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="26">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="26">
            </span>
        </a> --}}
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    @php
        // Get user roles with safe null checking
        $userRoles = [];
        if (auth()->check() && auth()->user()->roles) {
            $userRoles = auth()->user()->roles->pluck('rolename')->toArray();
        }
    @endphp

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @if (in_array('Admin', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#dashboard" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="dashboard">
                            <i class="ri-dashboard-line"></i> <span data-key="d-dashboard">Dashboard</span>
                        </a>
                        <div class="collapse menu-dropdown" id="dashboard">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="dashboard-infure" class="nav-link" data-key="d-infure"> INFURE </a>
                                </li>
                                <li class="nav-item">
                                    <a href="dashboard-seitai" class="nav-link" data-key="d-seitai"> SEITAI </a>
                                </li>
                                <li class="nav-item">
                                    <a href="dashboard-ppic" class="nav-link" data-key="d-ppic"> PPIC </a>
                                </li>
                                <li class="nav-item">
                                    <a href="dashboard-qc" class="nav-link" data-key="d-qc"> QC </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">MENU</span></li> --}}
                @if (in_array('Order', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#orderlpk" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="orderlpk">
                            <i class="ri-shopping-cart-2-line"></i> <span data-key="t-orderlpk">Order & LPK</span>
                        </a>
                        <div class="collapse menu-dropdown" id="orderlpk">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="order-lpk" class="nav-link" data-key="t-order-lpk" {{-- onclick="return confirm('Are you sure you want to proceed to Order Entry?');" --}}>
                                        Order Entry </a>
                                </li>
                                <li class="nav-item">
                                    <a href="lpk-entry" class="nav-link" data-key="t-lpk-entry"> LPK Entry </a>
                                </li>
                                <li class="nav-item">
                                    <a href="cetak-lpk" class="nav-link" data-key="t-lpk"> Cetak LPK </a>
                                </li>
                                <li class="nav-item">
                                    <a href="order-report" class="nav-link" data-key="t-order-report"> Order Report </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('NippoInfure', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#nippoinfure" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="nippoinfure">
                            <i class="ri-settings-5-line"></i> <span data-key="t-pages">Nippo INFURE</span>
                        </a>
                        <div class="collapse menu-dropdown" id="nippoinfure">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="nippo-infure" class="nav-link" data-key="t-starter"> Nippo Infure </a>
                                </li>
                                <li class="nav-item">
                                    <a href="loss-infure" class="nav-link" data-key="t-starter"> Loss Infure </a>
                                </li>
                                <li class="nav-item">
                                    <a href="checklist-infure" class="nav-link" data-key="t-starter"> Check List </a>
                                </li>
                                <li class="nav-item">
                                    <a href="label-gentan" class="nav-link" data-key="t-starter"> Label Gentan </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('NippoSeitai', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#nipposeitai" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="nipposeitai">
                            <i class="ri-settings-5-line"></i> <span data-key="t-pages">Nippo SEITAI</span>
                        </a>
                        <div class="collapse menu-dropdown" id="nipposeitai">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="nippo-seitai" class="nav-link" data-key="t-starter"> Nippo Seitai </a>
                                </li>
                                <li class="nav-item">
                                    <a href="loss-seitai" class="nav-link" data-key="t-starter"> Loss Seitai </a>
                                </li>
                                <li class="nav-item">
                                    <a href="mutasi-isi-palet" class="nav-link" data-key="t-starter"> Mutasi Isi
                                        Palet </a>
                                </li>
                                <li class="nav-item">
                                    <a href="check-list-seitai" class="nav-link" data-key="t-starter"> Check List
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="label-masuk-gudang" class="nav-link" data-key="t-starter"> Label Masuk
                                        Gudang </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('JamKerja', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#jamkerja" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="jamkerja">
                            <i class="ri-time-line"></i> <span data-key="t-pages">Jam Kerja</span>
                        </a>
                        <div class="collapse menu-dropdown" id="jamkerja">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="infure-jam-kerja" class="nav-link" data-key="t-starter"> Infure </a>
                                </li>
                                <li class="nav-item">
                                    <a href="seitai-jam-kerja" class="nav-link" data-key="t-starter"> Seitai </a>
                                </li>
                                <li class="nav-item">
                                    <a href="checklist-jam-kerja" class="nav-link" data-key="t-starter"> Check List </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Kenpin', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#kenpin" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="kenpin">
                            <i class="ri-film-line"></i> <span data-key="t-pages">Kenpin</span>
                        </a>
                        <div class="collapse menu-dropdown" id="kenpin">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="kenpin-infure" class="nav-link" data-key="t-starter"> Kenpin Infure </a>
                                </li>
                                <li class="nav-item">
                                    <a href="kenpin-seitai" class="nav-link" data-key="t-starter"> Kenpin Seitai </a>
                                </li>
                                <li class="nav-item">
                                    <a href="mutasi-isi-palet-kenpin" class="nav-link" data-key="t-starter"> Mutasi
                                        Isi Palet </a>
                                </li>
                                <li class="nav-item">
                                    <a href="print-label-gudang-kenpin" class="nav-link" data-key="t-starter"> Print
                                        Label Gudang </a>
                                </li>
                                <li class="nav-item">
                                    <a href="report-kenpin" class="nav-link" data-key="t-starter"> Report </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Warehouse', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#warehouse" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="warehouse">
                            <i class="bi bi-journal-medical"></i> <span data-key="t-pages">Warehouse</span>
                        </a>
                        <div class="collapse menu-dropdown" id="warehouse">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="penarikan-palet" class="nav-link" data-key="t-starter"> Penarikan Palet
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pengembalian-palet" class="nav-link" data-key="t-starter"> Pengembalian
                                        Palet </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#report" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="report">
                            <i class="ri-printer-line"></i> <span data-key="t-pages">Report</span>
                        </a>
                        <div class="collapse menu-dropdown" id="report">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="general-report" class="nav-link" data-key="t-starter"> General Report
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="detail-report" class="nav-link" data-key="t-starter"> Detail Report </a>
                                </li>
                                <li class="nav-item">
                                    <a href="production-loss-report" class="nav-link" data-key="t-starter">
                                        Production Loss Report </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Master', $userRoles) || in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#mastertabel" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="mastertabel">
                            <i class="bx bx-table"></i> <span data-key="t-mastertabel">Master Tabel</span>
                        </a>
                        <div class="collapse menu-dropdown" id="mastertabel">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="buyer" class="nav-link" data-key="t-buyer"> Buyer </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#produk" class="nav-link" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="produk" data-key="t-profile"><i
                                            class="bx bx-money"></i> Produk </a>
                                    <div class="collapse menu-dropdown" id="produk">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="master-produk" class="nav-link" data-key="t-simple-page">
                                                    Master Produk </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="tipe-produk" class="nav-link" data-key="t-settings"> Tipe
                                                    Produk </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="jenis-produk" class="nav-link" data-key="t-settings"> Jenis
                                                    Produk </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="departemen" class="nav-link" data-key="t-departemen"> Departemen </a>
                                </li>
                                <li class="nav-item">
                                    <a href="karyawan" class="nav-link" data-key="t-starter"> Karyawan </a>
                                </li>
                                <li class="nav-item">
                                    <a href="menu-katanuki" class="nav-link" data-key="t-starter"> Katanuki </a>
                                </li>
                                {{-- Mesin --}}
                                <li class="nav-item">
                                    <a href="#mesin" class="nav-link" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="mesin" data-key="t-profile"><i
                                            class="bx bx-printer"></i> Mesin </a>
                                    <div class="collapse menu-dropdown" id="mesin">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="mesin" class="nav-link" data-key="t-simple-page">
                                                    Mesin </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="bagian-mesin" class="nav-link" data-key="t-simple-page">
                                                    Bagian Mesin </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="detail-bagian-mesin" class="nav-link" data-key="t-simple-page">
                                                    Detail Bagian Mesin
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="warehouse" class="nav-link" data-key="t-starter"> Warehouse </a>
                                </li>
                                <li class="nav-item">
                                    <a href="working-shift" class="nav-link" data-key="t-starter"> Working Shift </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#loss" class="nav-link" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="loss" data-key="t-profile"><i
                                            class="bx bx-money"></i> Loss </a>
                                    <div class="collapse menu-dropdown" id="loss">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="menu-loss-infure" class="nav-link" data-key="t-simple-page">
                                                    Loss Infure </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="menu-loss-seitai" class="nav-link" data-key="t-simple-page">
                                                    Loss Seitai </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="menu-loss-kenpin" class="nav-link" data-key="t-simple-page">
                                                    Loss Kenpin </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="menu-loss-klasifikasi" class="nav-link"
                                                    data-key="t-settings"> Loss Klasifikasi </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="menu-loss-kategori" class="nav-link" data-key="t-settings">
                                                    Loss Kategori </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="#kemasan" class="nav-link" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="kemasan" data-key="t-profile"><i
                                            class="bx bx-money"></i> Kemasan </a>
                                    <div class="collapse menu-dropdown" id="kemasan">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="kemasan-box" class="nav-link" data-key="t-simple-page">
                                                    Kemasan Box </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="kemasan-inner" class="nav-link" data-key="t-simple-page">
                                                    Kemasan Inner </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="kemasan-layer" class="nav-link" data-key="t-settings">
                                                    Kemasan Layer </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="kemasan-gasio" class="nav-link" data-key="t-settings">
                                                    Kemasan Gaiso </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                {{-- Jam mati mesin --}}
                                <li class="nav-item">
                                    <a href="#jam-mati-mesin" class="nav-link" data-bs-toggle="collapse"
                                        role="button" aria-expanded="false" aria-controls="jam-mati-mesin"
                                        data-key="t-profile"><i class="bx bx-alarm-off"></i> Jam Mati Mesin </a>
                                    <div class="collapse menu-dropdown" id="jam-mati-mesin">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="jam-mati-mesin-infure" class="nav-link"
                                                    data-key="t-simple-page"> Infure </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="jam-mati-mesin-seitai" class="nav-link"
                                                    data-key="t-simple-page"> Seitai </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                {{-- Masalah Kenpin --}}
                                <li class="nav-item">
                                    <a href="#masalah-kenpin" class="nav-link" data-bs-toggle="collapse"
                                        role="button" aria-expanded="false" aria-controls="masalah-kenpin"
                                        data-key="t-profile">
                                        <i class="bx bx-error"></i>
                                        Masalah Kenpin
                                    </a>
                                    <div class="collapse menu-dropdown" id="masalah-kenpin">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="masalah-kenpin-infure" class="nav-link"
                                                    data-key="t-simple-page"> Infure </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="masalah-kenpin-seitai" class="nav-link"
                                                    data-key="t-simple-page"> Seitai </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Admin', $userRoles))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#administration" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="administration">
                            <i class="ri-admin-line"></i> <span data-key="t-pages">Administration</span>
                        </a>
                        <div class="collapse menu-dropdown" id="administration">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="security-management" class="nav-link" data-key="t-security"> Security
                                        Management </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (in_array('Admin', $userRoles))
                    <li class="nav-item d-none">
                        <a class="nav-link menu-link" href="#inventory" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="inventory">
                            <i class=" ri-store-line"></i> <span data-key="t-pages">Inventory</span>
                        </a>
                        <div class="collapse menu-dropdown" id="inventory">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="pemasukan-barang" class="nav-link" data-key="t-security"> Pemasukan
                                        Barang </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pengeluaran-barang" class="nav-link" data-key="t-security"> Pengeluaran
                                        Barang </a>
                                </li>
                                <li class="nav-item">
                                    <a href="posisi-wip" class="nav-link" data-key="t-security"> Posisi WIP </a>
                                </li>
                                <li class="nav-item">
                                    <a href="bahan-baku" class="nav-link" data-key="t-security"> Bahan Baku/penolong
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="barang-jadi" class="nav-link" data-key="t-security"> Barang Jadi </a>
                                </li>
                                <li class="nav-item">
                                    <a href="mesin-peralatan" class="nav-link" data-key="t-security"> Mesin &
                                        Peralatan </a>
                                </li>
                                <li class="nav-item">
                                    <a href="barang-reject" class="nav-link" data-key="t-security"> Barang Reject &
                                        Scrap </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
