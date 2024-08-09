@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css"
        integrity="sha512-1k7mWiTNoyx2XtmI96o+hdjP8nn0f3Z2N4oF/9ZZRgijyV4omsKOXEnqL1gKQNPy2MTSP9rIEWGcH/CInulptA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}

    {{-- <link rel="stylesheet" href="{{ asset('asset/css/notika-custom-icon.css') }}"> --}}


    <style>
        .page-content {
            background-color: #f4f6f9 !Important;
        }
    </style>
@endsection
@section('content')
<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 align-items-center">
                            <form action="{{ route('dashboard-infure') }}" method="get" class=" d-flex">
                                <div class="input-group">
                                    <input type="text" name="filterDate" id="filterDate" class="form-control"
                                        data-provider="flatpickr" data-date-format="d-m-Y" data-range-date="true"
                                        data-default-date="{{ $filterDate }}">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-load w-lg p-1">
                                    <span>
                                        <i class="ri-search-line"></i> Filter
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0">Total Produk Kenpin</h4>
            </div>
            <div class="card-body">
                <table id="scroll-infure"
                    class="table table-bordered dt-responsive nowrap align-middle mdl-data-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Division</th>
                            <th>Code</th>
                            <th>Jumlah Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($totalprodukkenpin as $data)
                            <tr>
                                <td>{{ $data->division_code }} </td>
                                <td>{{ $data->product_code }}</td>
                                <td>{{ $data->jumlahloss }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0">Total Produk Jenis Kenpin</h4>
            </div>
            <div class="card-body">
                <table id="scroll-seitai"
                    class="table table-bordered dt-responsive nowrap align-middle mdl-data-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Division</th>
                            <th>Jenis</th>
                            <th>Jumlah Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jenisprodukkenpin as $data)
                            <tr>
                                <td>{{ $data->division_code }} </td>
                                <td>{{ $data->jenis }}</td>
                                <td>{{ $data->jumlahloss }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>
    {{-- <script src="{{ URL::asset('build/js/app.js') }}"></script> --}}

    {{-- <script src="https://img.themesbrand.com/velzon/apexchart-js/stock-prices.js"></script> --}}
    <script src="{{ URL::asset('build/libs/jsvectormap/maps/us-merc-en.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/widgets.init.js') }}"></script>

    {{-- <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"
        integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#filterDate').flatpickr({
                mode: "range",
                dateFormat: "d-m-Y",
                defaultDate: ['today to today'],
            });

            // $('#data-table-basic').DataTable();

            $('#scroll-infure').DataTable({
                "scrollY": "250px",
                "scrollCollapse": true,
                "paging": false
            });

            $('#scroll-seitai').DataTable({
                "scrollY": "250px",
                "scrollCollapse": true,
                "paging": false
            });
        });
        // end Counter Table Infure
    </script>
@endsection
