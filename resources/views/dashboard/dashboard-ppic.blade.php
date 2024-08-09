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
                                <form action="{{ route('dashboard') }}" method="get" class=" d-flex">
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
    {{-- kadou jikan infure --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanInfure"></div>
                                </div>
                            </div><!-- end card body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loss --}}
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0">Hasil produksi per-tipe Infure</h4>
                </div>
                <div class="card-body">
                    <table id="scroll-infure"
                        class="table table-bordered dt-responsive nowrap align-middle mdl-data-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>code</th>
                                <th>Name</th>
                                <th>Berat</th>
                                <th>Panjang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pertipeinfure as $data)
                                <tr>
                                    <td>{{ $data->product_type_code }} </td>
                                    <td>{{ $data->product_type_name }}</td>
                                    <td>{{ $data->berat_produksi }}</td>
                                    <td>{{ $data->panjang_produksi }}</td>
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
                    <h4 class="card-title mb-0">Hasil produksi per-tipe Seitai</h4>
                </div>
                <div class="card-body">
                    <table id="scroll-seitai"
                        class="table table-bordered dt-responsive nowrap align-middle mdl-data-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Berat</th>
                                <th>Loss</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pertipeseitai as $data)
                                <tr>
                                    <td>{{ $data->product_type_code }} </td>
                                    <td>{{ $data->product_type_name }}</td>
                                    <td>{{ $data->berat_produksi }}</td>
                                    <td>{{ $data->seitai_berat_loss }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- end Loss --}}

    {{-- TOP Trouble --}}
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6 mb-1">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-pie-chart-box-line text-primary"></i>
                            </span>
                        </div>
                        <div class="text-end flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Hasil Produksi Infure</p>
                            <h4 class="fs-22 fw-semibold mb-3">
                                <span class="counter-value" data-target="{{ $hasilproduksiinfure[0]->berat_produksi }}">0</span>k
                            </h4>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <h5 class="text-danger fs-12 mb-0">
                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -2.74 %
                                </h5>
                                <p class="text-muted mb-0">{{ $hasilproduksiinfure[0]->bulan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 mb-1">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-pie-chart-box-fill text-primary"></i>
                            </span>
                        </div>
                        <div class="text-end flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Hasil Produksi Seitai</p>
                            <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="{{ $hasilproduksiseitai[0]->berat_produksi }}">0</span>k
                            </h4>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <h5 class="text-danger fs-12 mb-0">
                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -2.74 %
                                </h5>
                                <p class="text-muted mb-0">{{ $hasilproduksiseitai[0]->bulan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 mb-1">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-bar-chart-box-line text-primary"></i>
                            </span>
                        </div>
                        <div class="text-end flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Hasil Produksi Infure</p>
                            <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="{{ $hasilproduksiinfure[1]->berat_produksi }}">0</span>k
                            </h4>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <h5 class="text-danger fs-12 mb-0">
                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -2.74 %
                                </h5>
                                <p class="text-muted mb-0">{{ $hasilproduksiinfure[1]->bulan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6 mb-1">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="ri-bar-chart-box-fill text-primary"></i>
                            </span>
                        </div>
                        <div class="text-end flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Hasil Produksi Seitai</p>
                            <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="{{ $hasilproduksiseitai[1]->berat_produksi }}">0</span>k
                            </h4>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <h5 class="text-danger fs-12 mb-0">
                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -2.74 %
                                </h5>
                                <p class="text-muted mb-0">{{ $hasilproduksiseitai[1]->bulan }}</p>
                            </div>
                        </div>
                    </div>
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

            /*
            Infure
            */
            let kadouJikanInfureMesin = @json($kadouJikanInfureMesin);


            // Kadou Jikan Infure
            Highcharts.chart('kadouJikanInfure', {
                chart: {
                    type: 'column'
                },
                title: {
                    align: 'left',
                    text: `<a href="#" id="kadouJikanTitle" class="text-muted">
                               Hasil produksi per-jenis Infure EG-Arm-Gomi
                            </a>`,
                    useHTML: true
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    categories: ['HD EG', 'HD GOMI', 'HD ARM', 'LD GOMI'],
                    crosshair: true,
                    accessibility: {
                        description: 'Countries'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Machine Running Rate'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:f}'
                        },
                        borderRadius: 8
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color};">{point.name}</span>: ' +
                        '<b>{point.y:.2f}%</b> of total<br/>'
                },

                series: [
                    {
                        name: 'Corn',
                        data: [53651639, 23938806, 1054159, 9881334]
                    },
                    {
                        name: 'Wheat',
                        data: [0, 40801, 21759, 1681]
                    }
                ]
            });

            document.getElementById('kadouJikanTitle').addEventListener('click', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalListMesinInfure'));
                myModal.show();
            });

            // pie chart presentase loss
            let chartPieBasicColors = getChartColorsArray("presentaseLossInfure");
            if (chartPieBasicColors) {
                let options = {
                    series: lossInfure.lossInfure.map(item => parseFloat(parseFloat(item.berat_loss / lossInfure
                        .totalLossInfure * 100).toFixed(2))),
                    chart: {
                        height: 300,
                        type: 'pie',
                    },
                    labels: lossInfure.lossInfure.map(item => item.loss_name),
                    legend: {
                        position: 'bottom'
                    },
                    dataLabels: {
                        dropShadow: {
                            enabled: false,
                        }
                    },
                    colors: chartPieBasicColors
                };

                let chart = new ApexCharts(document.querySelector("#presentaseLossInfure"),
                    options);
                chart.render();
            }
            // end Loss Infure

            // top loss infure
            let topLossInfure = @json($topLossInfure);
            let html = '';
            topLossInfure.map((item, index) => {
                html += `<li class="list-group-item ps-0">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                        <div class="text-center">
                                            <h5 class="mb-0">${index + 1}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <h5 class="text-muted mt-0 mb-1 fs-13">
                                        <span class="badge text bg-primary">LOSS</span> ${item.loss_name}
                                    </h5>
                                    <a href="#" class="text-reset fs-14 mb-0">
                                        <span class="badge text bg-danger">Berat</span> ${parseFloat(item.berat_loss).toFixed(3)}
                                    </a>
                                </div>
                            </div>
                        </li>`;
            });
            $('#topLossInfure').html(html);
        });
    </script>
@endsection
