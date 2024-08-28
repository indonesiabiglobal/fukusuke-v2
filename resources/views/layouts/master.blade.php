<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="enable">

<head>
    <meta charset="utf-8" />
    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <title> Fukusuke Kogyo Indonesia </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/fukusuke.ico') }}">
    {{-- @section('css') --}}
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- @endsection --}}
    @include('layouts.head-css')
    @livewireStyles

    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

    {{-- toastr --}}
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    {{-- @powerGridStyles --}}
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.top-tagbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    {{-- @include('layouts.customizer') --}}
    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    @stack('scripts')
    {{-- toastr --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @livewireScripts
    {{-- @powerGridScripts --}}
    <script>
        window.addEventListener('notification', event => {
            toastr[event.detail[0].type](event.detail[0].message);
        });

        // Cek apakah ada session flash notification
        @if (session()->has('notification'))
            var type = "{{ session('notification')['type'] }}";
            var message = "{{ session('notification')['message'] }}";
            toastr[type](message);
        @endif


        // datatable
        // inisialisasi DataTable

        // Fungsi untuk menginisialisasi ulang DataTable
        document.addEventListener('livewire:init', () => {
            function initDataTable(id) {
                // Hapus DataTable jika sudah ada
                if ($.fn.dataTable.isDataTable('#' + id)) {
                    let table = $('#' + id).DataTable();
                    table.clear(); // Bersihkan data tabel
                    table.destroy(); // Hancurkan DataTable
                    // Hindari penggunaan $('#' + id).empty(); di sini
                }

                setTimeout(() => {
                    // Inisialisasi ulang DataTable
                    let table = $('#' + id).DataTable({
                        "pageLength": 10,
                        "searching": true,
                        "responsive": true,
                        "scrollX": true,
                        "order": [
                            [2, "asc"]
                        ],
                        "language": {
                            "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </div>
                        `
                        }
                    });
                    // tombol delete
                    $('.btn-delete').on('click', function() {
                        let id = $(this).attr('data-id');

                        // livewire click
                        $wire.dispatch('delete', {
                            id
                        });
                    });

                    // default column visibility
                    $('.toggle-column').each(function() {
                        let column = table.column($(this).attr('data-column'));
                        column.visible($(this).is(':checked'));
                    });

                    // Inisialisasi ulang event listener checkbox
                    $('.toggle-column').off('change').on('change', function() {
                        let column = table.column($(this).attr('data-column'));
                        column.visible(!column.visible());
                    });
                }, 500);
            }
        })
    </script>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>

    <script>
        // format number
        window.formatNumber = function(value) {
            console.log(value);

            // Hapus koma jika ada
            value = value.replace(/,/g, '');

            // Hapus karakter yang bukan angka
            value = value.replace(/[^0-9]/g, '');

            // Hapus nol di depan angka
            value = value.replace(/^0+/, '');

            // Jika value adalah angka yang valid, format dengan pemisah ribuan
            if (!isNaN(value) && value !== '') {
                return Number(value).toLocaleString('en-US');
            }

            // Kembalikan value tanpa modifikasi jika tidak valid
            return value;
        };


        window.formatNumberDecimal = function(value) {
            // Hapus koma jika ada
            value = value.replace(/,/g, '');

            // Hapus karakter yang bukan angka atau titik desimal
            value = value.replace(/[^0-9.]/g, '');

            // Hapus nol di depan angka kecuali untuk nol yang digunakan sebelum titik desimal
            value = value.replace(/^0+(?=\d)/, '');

            // Jika ada lebih dari satu titik desimal, hapus semua kecuali yang pertama
            let parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Format bagian sebelum titik desimal dengan pemisah ribuan
            if (!isNaN(value) && value !== '') {
                parts = value.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
            }

            // Kembalikan value tanpa modifikasi jika tidak valid
            return value;
        };
    </script>

    {{-- <script>
        $(function() {
            $('.select2').select2({
                placeholder: "select an option",
                allowClear: true
            })
        })
    </script>
    @stack('scripts') --}}
</body>

</html>
