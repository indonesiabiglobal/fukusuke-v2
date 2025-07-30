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

    <style>
        .page-content {
            background-color: #f4f6f9 !Important;
            padding: calc(100px + .1rem) 0.2rem 0px .2rem !Important;
        }

        thead th {
            text-align: center !important;
            align-content: center !important;
            font-size: 14px;
            padding: 0px !important;
        }

        tbody td {
            text-align: center !important;
            padding: 0px !important;
            padding: 1px !important;
        }

        #table-peringatan-katagae tbody td {
            font-size: 11px;
        }

        #table-produksi tbody td {
            padding: 4px !important;
        }

        .card-mesin-masalah {
            transition: transform 0.2s ease-in-out;
        }

        .card-mesin-masalah:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .problem-list small {
            line-height: 1.4;
        }

        .bg-orange {
            background-color: orange !important;
            color: white;
        }

        .footer {
            visibility: hidden;
        }

        .bg-masalah-1 {
            background-color: #d35400 !important;
        }

        .bg-masalah-2 {
            background-color: #ff9900 !important;
        }

        .bg-masalah-3 {
            background-color: #ffbd53 !important;
        }

        .bg-orange-100 {
            background-color: #FFE699 !important;

        }

        .bg-green-100 {
            background-color: #E2EFDA !important;

        }

        @media (min-width: 768px) {
            [data-layout=vertical][data-sidebar-size=sm] {
                min-height: 0px !important;
                color: #aaaaaa
            }
        }
    </style>
@endsection
@section('content')
    <div class="row max-vh-100">
        <div class="col-12 col-xl-6 p-1">
            <div class="card bg-orange-100">
                <div class="card-header p-2 border-0 align-items-center">
                    <form method="get" class="row g-2 align-items-center"
                        id="form-dashboard-daily">
                        <div class="input-group">
                            <div class="col-md-3">
                                <select class="form-select p-2" name="factory" id="factory">
                                    @foreach ($listFactory as $factory)
                                        <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-9 d-flex">
                                <div class="input-group">
                                    <input type="text" name="filterDateDaily" id="filterDateDaily"
                                        class="form-control p-2" data-provider="flatpickr" data-date-format="d-m-Y"
                                        data-default-date="{{ $filterDateDaily }}">
                                    <span class="input-group-text p-1">
                                        <i class="ri-calendar-event-fill fs-5"></i>
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary btn-load w-lg p-1">
                                    <span>
                                        <i class="ri-search-line"></i> Filter
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-1">
                    <div class="row p-1">
                        <div class="col-12 col-xl-8 pe-0">
                            <div id="produksiLossPerMesin" class="rounded-3"></div>
                        </div>
                        <div class="col-12 col-xl-4 ps-1">
                            <div id="lossPerMesin" class="rounded-3"></div>
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-12 col-xl-8 pe-0">
                            <div id="kadouJikanFrekuensiTrouble" class="rounded-3"></div>
                        </div>
                        <div class="col-12 col-xl-4 ps-1">
                            <div id="lossPerKasus" class="rounded-3"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Section 1: Mesin Masalah Kiri -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100 bg-orange-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-6 p-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-danger rounded-circle me-2" style="width: 8px; height: 8px;">
                                                </div>
                                                <h5 class="mb-0 text-danger fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Produksi Rendah</small>
                                                <small class="d-block text-muted mb-1">• Kadou Jikan Rendah</small>
                                                <small class="d-block text-muted">• Loss Tinggi (%)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-1 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-2 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-3 fs-5">33</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Mesin Masalah Kanan -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100 bg-orange-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-6 p-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary rounded-circle me-2"
                                                    style="width: 8px; height: 8px;"></div>
                                                <h5 class="mb-0 text-primary fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Masalah Henniku Tertinggi
                                                    (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-1 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-2 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-3 fs-5">33</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-12 col-xl-6 p-1">
            <div class="card mb-0">
                <div class="card-header p-2 border-0 align-items-center">
                    <form action="{{ route('dashboard-infure-monthly') }}" method="get" class="d-flex"
                        id="form-dashboard-monthly">
                        <div class="input-group">
                            <input type="text" name="filterDateMonthly" id="filterDateMonthly"
                                class="form-control p-2" data-provider="flatpickr" data-date-format="d-m-Y"
                                data-range-date="true" data-default-date="{{ $filterDateMonthly }}">
                            <span class="input-group-text p-1">
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
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12 col-xl-6 p-1" style="height: 200px">
                            <h4 class="card-title mb-2 flex-grow-1 fw-bold text-center">
                                Total Produksi Pabrik C (Kg)
                            </h4>
                            <table class="table table-bordered rounded-3" id="table-produksi">
                                <thead>
                                    <tr>
                                        <th>Periode</th>
                                        <th>Target</th>
                                        <th>Aktual</th>
                                        <th>Selisih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lossInfure['lossInfure'] as $data)
                                        @if ($loop->iteration == 4)
                                            @break
                                        @endif
                                        <tr>
                                            <td class="fw-semibold fs-6">Periode {{ $period[$loop->iteration - 1] }}</td>
                                            <td class="fw-bold fs-5">{{ round($data->berat_loss, 2) }} </td>
                                            <td class="fw-bold fs-5">{{ round($data->berat_loss, 2) }}</td>
                                            <td class="fw-bold fs-5 text-danger">
                                                {{ round($data->berat_loss, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="fw-bold fs-5">Total</td>
                                        <td class="fw-bold fs-5">{{ round($lossInfure['totalLossInfure'], 2) }}
                                        </td>
                                        <td class="fw-bold fs-5">210</td>
                                        <td class="fw-bold fs-5 text-danger">200</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-xl-6 p-1" style="height: 200px">
                            <h4 class="card-title mb-2 flex-grow-1 fw-bold text-center text-danger">
                                Peringatan Katagae
                            </h4>
                            <table class="table table-bordered dt-responsive nowrap align-middle mdl-data-table rounded-3"
                                style="width:100%" id="table-peringatan-katagae">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Mesin</th>
                                        <th rowspan="2">LPK</th>
                                        <th rowspan="2">Nama Produk</th>
                                        <th rowspan="2">Sisa Meter</th>
                                        <th colspan="2">Waktu</th>
                                    </tr>
                                    <tr>
                                        <th>Jam</th>
                                        <th>Menit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lossInfure['lossInfure'] as $data)
                                        @if ($loop->iteration == 6)
                                            @break
                                        @endif
                                        <tr>
                                            <td class="">{{ $loop->iteration }} </td>
                                            <td class="">{{ $data->loss_name }} </td>
                                            <td class="">{{ $data->loss_name }} </td>
                                            <td class="">{{ round($data->berat_loss, 2) }}</td>
                                            <td class="">{{ $loop->iteration * 2 }}</td>
                                            <td class="">{{ $loop->iteration * 3 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-green-100">
                <div class="card-body p-1">
                    <div class="row g-0 mb-2">
                        <div class="col-12 col-xl-6 pe-0">
                            <div id="produksiPerBulan" class="rounded-3"></div>
                        </div>
                        <div class="col-12 col-xl-6 ps-1">
                            <div id="lossPerBulan" class="rounded-3">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <!-- Section 1: Mesin Masalah Kiri -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100  bg-green-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-danger rounded-circle me-2"
                                                    style="width: 8px; height: 8px;">
                                                </div>
                                                <h5 class="mb-0 text-danger fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Produksi Rendah</small>
                                                <small class="d-block text-muted mb-1">• Kadou Jikan Rendah</small>
                                                <small class="d-block text-muted">• Loss Tinggi (%)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-1 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-2 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-3 fs-5">33</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Mesin Masalah Kanan -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100  bg-green-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-6 p-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary rounded-circle me-2"
                                                    style="width: 8px; height: 8px;"></div>
                                                <h5 class="mb-0 text-primary fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Masalah Henniku Tertinggi
                                                    (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-1 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-2 fs-5">33</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-masalah-3 fs-5">33</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end card -->
    </div><!-- end col -->
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
            // jQuery untuk handle kedua form tanpa reload
            $(document).ready(function() {
                // Handler untuk form daily
                $('#form-dashboard-daily').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const data = form.serialize();

                    $.ajax({
                        url: '{{ route('dashboard-infure-produksi-loss-per-mesin') }}',
                        method: form.attr('method'),
                        data: data,
                        success: function(res) {
                            console.log('Daily dashboard updated');
                            // $('#daily-container').html(res); // render ulang jika perlu
                        },
                        error: function(err) {
                            console.error('Daily form error', err);
                        }
                    });
                });

                // Handler untuk form monthly
                $('#form-dashboard-monthly').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const dateValue = $('#filterDateMonthly').val();
                    const factoryValue = $('#factory').val(); // ambil dari form daily

                    const data = {
                        filterDateMonthly: dateValue,
                        factory: factoryValue
                    };

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: data,
                        success: function(res) {
                            console.log('Monthly dashboard updated');
                            // $('#monthly-container').html(res); // render ulang jika perlu
                        },
                        error: function(err) {
                            console.error('Monthly form error', err);
                        }
                    });
                });
            });

            /*
            Infure
            */

            // PRODUKSI DAN LOSS PER MESIN
            let counterTroubleInfure = @json($counterTroubleInfure);
            let kadouJikanDepartment = @json($kadouJikanDepartment);
            kadouJikanDepartment = Object.values(kadouJikanDepartment);
            let kadouJikanInfureMesin = @json($kadouJikanInfureMesin);
            kadouJikanInfureMesin = Object.values(kadouJikanInfureMesin);

            Highcharts.setOptions({
                chart: {
                    style: {
                        fontSize: '12px',
                        // fontWeight: '600'
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.05, // default: 0.1
                        groupPadding: 0.05, // default: 0.2
                        borderWidth: 0
                    }
                },
                yAxis: {
                    tickAmount: 5,
                }
            });

            Highcharts.chart('produksiLossPerMesin', {
                chart: {
                    zooming: {
                        type: 'xy'
                    },
                    height: 200
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'PRODUKSI DAN LOSS PER MESIN',
                    style: {
                        fontSize: '12px',
                        fontWeight: '600',
                        fontFamily: 'Public Sans'
                    },
                },
                xAxis: [{
                    categories: [
                        '31', '32', '33', '34', '35', '36',
                        '37', '38', '39', '40', '41', '42'
                    ],
                    crosshair: true
                }],
                yAxis: [{
                    labels: {
                        format: '{value}%',
                        style: {}
                    },
                    title: {
                        text: '(%)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    opposite: true
                }, {
                    gridLineWidth: 0,
                    title: {
                        text: '(Kg)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    labels: {
                        format: '{value}',
                        style: {}
                    }
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'horizontal',
                    align: 'left',
                    x: 80,
                    verticalAlign: 'top',
                    y: 55,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                        'rgba(255,255,255,0.25)',
                    itemStyle: {
                        fontSize: '10px',
                    }
                },
                series: [{
                    name: 'Produksi (Kg)',
                    type: 'column',
                    yAxis: 1,
                    data: [
                        49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1,
                        95.6, 54.4
                    ],
                    tooltip: {
                        valueSuffix: ' Kg'
                    },
                    color: '#29A3FF',
                }, {
                    name: 'Loss (%)',
                    type: 'spline',
                    color: '#ff9900',
                    data: [
                        7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6
                    ],
                    tooltip: {
                        valueSuffix: ' %'
                    }
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                floating: false,
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0
                            },
                            yAxis: [{
                                labels: {
                                    align: 'right',
                                },
                                showLastLabel: true
                            }, {
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }, {
                                visible: false
                            }]
                        }
                    }]
                }
            });

            // Loss per Mesin
            Highcharts.chart('lossPerMesin', {
                chart: {
                    type: 'column',
                    height: 200,
                    backgroundColor: '#FBE5D6'
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'LOSS/MESIN',
                    style: {
                        fontSize: '12px',
                        fontWeight: '600',
                        fontFamily: 'Public Sans'
                    },
                },
                xAxis: {
                    categories: ['31', '32', '33', '34', '35', '36'],
                },
                yAxis: {
                    gridLineWidth: 1,
                    gridLineColor: '#aaaaaa',
                    min: 0,
                    title: {
                        text: '(Kg)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    labels: {
                        format: '{value}',
                        style: {}
                    }
                },
                tooltip: {
                    valueSuffix: ' (Kg)'
                },
                series: [{
                    name: 'Loss',
                    showInLegend: false,
                    data: [64.20, 44.70, 35.50, 27.90, 24.60, 19.10],
                    color: '#ff9900',
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                floating: false,
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0
                            },
                            yAxis: [{
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }]
                        }
                    }]
                }
            });

            Highcharts.chart('kadouJikanFrekuensiTrouble', {
                chart: {
                    zooming: {
                        type: 'xy'
                    },
                    height: 200
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'KADOU JIKAN & FREKUENSI TROUBLE',
                    style: {
                        fontSize: '12px',
                        fontWeight: '600',
                        fontFamily: 'Public Sans'
                    },
                },
                xAxis: [{
                    categories: [
                        '31', '32', '33', '34', '35', '36',
                        '37', '38', '39', '40', '41', '42'
                    ],
                    crosshair: true
                }],
                yAxis: [{
                    labels: {
                        format: '{value}%',
                        style: {}
                    },
                    title: {
                        text: '(%)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    opposite: true

                }, {
                    gridLineWidth: 0,
                    title: {
                        text: '(Kg)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    labels: {
                        format: '{value}',
                        style: {}
                    }
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'horizontal',
                    align: 'left',
                    x: 80,
                    verticalAlign: 'top',
                    y: 55,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                        'rgba(255,255,255,0.25)',
                    itemStyle: {
                        fontSize: '10px',
                    }
                },
                series: [{
                    name: 'Produksi (Kg)',
                    type: 'column',
                    yAxis: 1,
                    data: [
                        49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1,
                        95.6, 54.4
                    ],
                    tooltip: {
                        valueSuffix: ' Kg'
                    },
                    color: '#29A3FF',
                }, {
                    name: 'Loss (%)',
                    type: 'spline',
                    data: [
                        7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6
                    ],
                    color: '#ff9900',
                    tooltip: {
                        valueSuffix: ' %'
                    }
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                floating: false,
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0
                            },
                            yAxis: [{
                                labels: {
                                    align: 'right',
                                },
                                showLastLabel: true
                            }, {
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }]
                        }
                    }]
                }
            });

            // loss Per kasus
            Highcharts.chart('lossPerKasus', {
                chart: {
                    type: 'column',
                    height: 200,
                    backgroundColor: '#FBE5D6'
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'LOSS/KASUS',
                    style: {
                        fontSize: '12px',
                        fontWeight: '600',
                        fontFamily: 'Public Sans'
                    },
                },
                xAxis: {
                    categories: ["Printing ke Printing", "Potong Gentan", "Amigae", "Heniku",
                        "Tachiage Lain-Lain"
                    ],
                },
                yAxis: {
                    gridLineWidth: 1,
                    gridLineColor: '#aaaaaa',
                    min: 0,
                    title: {
                        text: '(Kg)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                        style: {}
                    },
                    labels: {
                        format: '{value}',
                        style: {}
                    }
                },
                tooltip: {
                    valueSuffix: ' (Kg)'
                },
                series: [{
                    name: 'Loss',
                    showInLegend: false,
                    color: '#ff9900',
                    data: [64.20, 44.70, 35.50, 27.90, 24.60]
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                floating: false,
                                layout: 'horizontal',
                                align: 'center',
                                x: 0,
                                y: 0
                            },
                            yAxis: [{
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }]
                        }
                    }]
                }
            });


            //  Produksi Per Bulan
            let produksiPerBulan = [
                49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1,
                95.6, 54.4
            ];
            Highcharts.chart('produksiPerBulan', {
                chart: {
                    type: 'column',
                    height: 200
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'PRODUKSI PER BULAN',
                    align: 'center'
                },
                xAxis: {
                    categories: [31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44]
                },
                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: '(Kg)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20
                    },
                },
                tooltip: {
                    format: '<b>{key}</b><br/>{series.name}: {y}<br/>' +
                        'Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: [{
                    name: 'C',
                    data: produksiPerBulan.map((item, index) => item * 1.5),
                    stack: 'produksi',
                    color: '#93D1FF'
                }, {
                    name: 'B',
                    data: produksiPerBulan.map((item, index) => item * 2),
                    stack: 'produksi',
                    color: '#29A3FF'
                }, {
                    name: 'A',
                    data: produksiPerBulan.map((item, index) => item * 3),
                    stack: 'produksi',
                    color: '#0070C0'
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                floating: false,
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom',
                                x: 0,
                                y: 0
                            },
                            yAxis: [{
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }]
                        }
                    }]
                }
            });

            let lossPerbulan = [0.63, 0.63, 0.57, 0.53, 0.80, 0.83, 0.63, 0.60, 1.10, 1.43, 1.40, 1.30, 0.93, 0.77];
            Highcharts.chart('lossPerBulan', {
                chart: {
                    height: 200,
                    backgroundColor: '#FBE5D6'
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'LOSS PER BULAN',
                    align: 'center',
                },
                yAxis: {
                    gridLineWidth: 1,
                    gridLineColor: '#aaaaaa',
                    title: {
                        text: '(%)',
                        align: 'high',
                        offset: 0,
                        rotation: 0,
                        y: -20,
                    },
                },
                xAxis: {
                    categories: [31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44],
                    title: {
                        text: 'Loss',
                    },
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                },
                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                    }
                },
                series: [{
                    name: 'Loss A',
                    color: '#d35400',
                    data: lossPerbulan.map(item => Math.round(item * 100) / 100)
                }, {
                    name: 'Loss B',
                    color: '#ff9900',
                    data: lossPerbulan.map(item => Math.round(item * 1.5 * 100) / 100)
                }, {
                    name: 'Loss C',
                    color: '#ffbd53',
                    data: lossPerbulan.map(item => Math.round(item * 2 * 100) / 100)
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            },
                            yAxis: [{
                                labels: {
                                    align: 'left',
                                },
                                showLastLabel: true
                            }]
                        },
                    }]
                },
            });
        });
    </script>
@endsection
