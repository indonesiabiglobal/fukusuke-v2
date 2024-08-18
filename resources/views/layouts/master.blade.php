<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="enable">

<head>
    <meta charset="utf-8" />
    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef"/>
    {{-- <link rel="apple-touch-icon" href="{{ asset('logo.png') }}"> --}}
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <title> Fukusuke Kogyo Indonesia </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    {{-- <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}"> --}}
    {{-- @section('css') --}}
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
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

    {{-- toastr --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" ></script>

    @livewireScripts
    {{-- @powerGridScripts --}}
    <script>
        window.addEventListener('notification', event => {
            toastr[event.detail[0].type](event.detail[0].message);
        });

        // Cek apakah ada session flash notification
        @if(session()->has('notification'))
            var type = "{{ session('notification')['type'] }}";
            var message = "{{ session('notification')['message'] }}";
            toastr[type](message);
        @endif
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
