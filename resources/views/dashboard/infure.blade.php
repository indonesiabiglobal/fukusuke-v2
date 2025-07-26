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
            font-size: 10px;
            padding: 3px !important;
        }

        tbody td {
            text-align: center !important;
            font-size: 10px;
            padding: 3px !important;
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
    </style>
@endsection
@section('content')
    <div class="row h-100">
        <div class="col-12 col-xl-6 p-1">
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
                <div class="card-body p-1">
                    <div class="row">
                        <div class="col-12 col-xl-8 pe-0">
                            <div id="produksiLossPerMesin"></div>
                        </div>
                        <div class="col-12 col-xl-4 ps-0">
                            <div id="lossPerMesin"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-xl-8 pe-0">
                            <div id="kadouJikanFrekuensiTrouble"></div>
                        </div>
                        <div class="col-12 col-xl-4 ps-0">
                            <div id="lossPerKasus"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Section 1: Mesin Masalah Kiri -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-8">
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
                                        <div class="col-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-danger fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-orange fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning fs-4">33</span>
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
                            <div class="card card-mesin-masalah shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary rounded-circle me-2"
                                                    style="width: 8px; height: 8px;"></div>
                                                <h5 class="mb-0 text-primary fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Masalah Henniku</small>
                                                <small class="d-block text-muted">• Tertinggi (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-danger fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning fs-4">33</span>
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
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12 col-xl-6 p-1" style="height: 200px">
                            <h4 class="card-title mb-2 flex-grow-1 fw-bold text-center">
                                Total Produksi Pabrik C (Kg)
                            </h4>
                            <table class="table table-bordered">
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
                                            <td class="text-center fw-semibold">{{ $loop->iteration }} </td>
                                            <td class="text-center fw-bold">{{ round($data->berat_loss, 2) }} </td>
                                            <td class="text-center fw-bold">{{ round($data->berat_loss, 2) }}</td>
                                            <td class="text-center fw-bold">
                                                {{ round($data->berat_loss, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-center fw-bold">Total</td>
                                        <td class="text-center fw-bold">{{ round($lossInfure['totalLossInfure'], 2) }}
                                        </td>
                                        <td class="text-center fw-bold">210</td>
                                        <td class="text-center fw-bold">200</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-xl-6 p-1" style="height: 200px">
                            <h4 class="card-title mb-2 flex-grow-1 fw-bold text-center text-danger">
                                Peringatan Katagae
                            </h4>
                            <table class="table table-bordered dt-responsive nowrap align-middle mdl-data-table"
                                style="width:100%">
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
                                        @if ($loop->iteration == 4)
                                            @break
                                        @endif
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }} </td>
                                            <td class="text-center">{{ $data->loss_name }} </td>
                                            <td class="text-center">{{ $data->loss_name }} </td>
                                            <td class="text-center">{{ round($data->berat_loss, 2) }}</td>
                                            <td class="text-center">{{ round($data->berat_loss, 2) }}</td>
                                            <td class="text-center">{{ round($data->berat_loss, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-12 col-xl-6 pe-0">
                            <div id="produksiPerBulan"></div>
                        </div>
                        <div class="col-12 col-xl-6 ps-0">
                            <div id="lossPerBulan">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <!-- Section 1: Mesin Masalah Kiri -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-8">
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
                                        <div class="col-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-danger fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-orange fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning fs-4">33</span>
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
                            <div class="card card-mesin-masalah shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- Problem Categories -->
                                        <div class="col-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary rounded-circle me-2"
                                                    style="width: 8px; height: 8px;"></div>
                                                <h5 class="mb-0 text-primary fw-bold fs-6">MESIN MASALAH</h5>
                                            </div>
                                            <div class="problem-list">
                                                <small class="d-block text-muted mb-1">• Masalah Henniku</small>
                                                <small class="d-block text-muted">• Tertinggi (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-4">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-danger fs-4">33</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning fs-4">33</span>
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
                }, {
                    name: 'Loss (%)',
                    type: 'spline',
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
            // Kadou Jikan Infure
            Highcharts.chart('lossPerMesin', {
                chart: {
                    type: 'column',
                    height: 200
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
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Loss',
                    showInLegend: false,
                    data: [64.20, 44.70, 35.50, 27.90, 24.60, 19.10]
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
                }, {
                    name: 'Loss (%)',
                    type: 'spline',
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
                            }]
                        }
                    }]
                }
            });

            // loss Per kasus
            Highcharts.chart('lossPerKasus', {
                chart: {
                    type: 'column',
                    height: 200
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
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Loss',
                    showInLegend: false,
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
                    name: 'A',
                    data: [148, 133, 124],
                    stack: 'produksi'
                }, {
                    name: 'B',
                    data: [102, 98, 65],
                    stack: 'produksi'
                }, {
                    name: 'C',
                    data: [113, 122, 95],
                    stack: 'produksi'
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
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'LOSS PER BULAN',
                    align: 'center',
                },
                yAxis: {
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
                    data: lossPerbulan.map(item => Math.round(item * 100) / 100)
                }, {
                    name: 'Loss B',
                    data: lossPerbulan.map(item => Math.round(item * 1.5 * 100) / 100)
                }, {
                    name: 'Loss C',
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
