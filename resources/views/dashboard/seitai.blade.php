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

        #totalProduksiPerBulan tbody td {
            padding: 4px !important;
        }

        #totalProduksiPerBulan {
            height: 80%;
            table-layout: fixed;
            margin-bottom: 0px !important;
        }

        #totalProduksiPerBulan tbody tr {
            height: calc(100% / 5);
            /* contoh: 5 baris */
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
                    <form method="get" class="row g-2 align-items-center" id="form-dashboard-daily">
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
                                <button onclick="loadInitialDailyData()" type="submit"
                                    class="btn btn-primary btn-load w-lg p-1" id="form-dashboard-daily-button">
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
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100 bg-orange-100 mb-0">
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
                                                <table class="table table-sm table-borderless mb-0"
                                                    id="rankingProblemMachineDailyTable">
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Mesin Masalah Kanan -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100 bg-orange-100 mb-0">
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
                                                <small class="d-block text-muted mb-1">• Masalah
                                                    <span id="mesinMasalahLossDaily">
                                                    </span>
                                                    Tertinggi (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0"
                                                    id="mesinMasalahLossDailyTable">
                                                    <tbody>
                                                    </tbody>
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
                    <form method="GET" class="d-flex" id="form-dashboard-monthly">
                        <div class="input-group">
                            <input type="month" name="filterDateMonthly" id="filterDateMonthly" class="form-control p-2"
                                value="{{ $filterDateMonthly }}">
                        </div>
                        <button onclick="loadInitialMonthlyData()" type="submit"
                            class="btn btn-primary btn-load w-lg p-1" id="form-dashboard-monthly-button">
                            <span>
                                <i class="ri-search-line"></i> Filter
                            </span>
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12 col-xl-6 p-1">
                            <h4 class="card-title mb-2 flex-grow-1 fw-bold text-center">
                                Total Produksi <span id="factory-period"></span>
                            </h4>
                            <table class="table table-bordered rounded-3 align-middle" id="totalProduksiPerBulan">
                                <thead>
                                    <tr>
                                        <th colspan="2">Periode</th>
                                        <th>Target</th>
                                        <th>Aktual</th>
                                        <th>Selisih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div>
                                <small class="text-dark fst-italic fw-semibold">*satuan lembar = juta</small>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6 p-1">
                            <div id="topLossPerKasusMonthly" class="rounded-3"></div>
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
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100  bg-green-100 mb-0">
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
                                                <table class="table table-sm table-borderless mb-0"
                                                    id="rankingProblemMachineMonthlyTable">
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Mesin Masalah Kanan -->
                        <div class="col-12 col-xl-6">
                            <div class="card card-mesin-masalah shadow-sm border-1 h-100  bg-green-100 mb-0">
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
                                                <small class="d-block text-muted mb-1">• Masalah
                                                    <span id="mesinMasalahLossMonthly">
                                                    </span>
                                                    Tertinggi (Kg)</small>
                                            </div>
                                        </div>

                                        <!-- Problem Values -->
                                        <div class="col-6 p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0"
                                                    id="mesinMasalahLossMonthlyTable">
                                                    <tbody>
                                                    </tbody>
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
    {{-- <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script> --}}

    {{-- <script src="https://img.themesbrand.com/velzon/apexchart-js/stock-prices.js"></script> --}}
    <script src="{{ URL::asset('build/libs/jsvectormap/maps/us-merc-en.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/widgets.init.js') }}"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // expose listFactory to JavaScript: keyed by id for easy lookup
        const listFactory = @json($listFactory->keyBy('id'));
        // Get refresh interval from env (in minutes) with fallback to 5 minutes
        const refreshIntervalMinutes = {{ env('REFRESH_DASHBOARD_MINUTES', 5) }};
        const refreshIntervalMs = refreshIntervalMinutes * 60 * 1000; // Convert to milliseconds
    </script>

    <script>
        function fetchData(url, data, method = 'POST') {
            return $.ajax({
                url,
                method,
                data
            });
        }

        function setButtonLoading(isLoading) {
            const button = $('#form-dashboard-daily-button');
            if (isLoading) {
                button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
            } else {
                button.prop('disabled', false).html('Filter');
            }
        }

        let kadouJikanFrekuensiTrouble = [];

        function createKadouJikanFrekuensiTroubleChart() {
            // compute sensible max for cases axis so single-digit data doesn't scale to large ticks
            let maxCases = 0;
            if (Array.isArray(kadouJikanFrekuensiTrouble) && kadouJikanFrekuensiTrouble.length) {
                maxCases = Math.max(...kadouJikanFrekuensiTrouble.map(item => parseFloat(item.frekuensi_trouble) || 0));
            }
            const casesAxisConfig = {
                labels: { format: '{value}', style: {} },
                title: { text: '(Kasus)', align: 'high', offset: 0, rotation: 0, y: -20, style: {} },
                // place cases on the left
                opposite: false
            };
            if (maxCases > 0) {
                casesAxisConfig.max = Math.ceil(maxCases * 1.2);
                if (maxCases <= 10) casesAxisConfig.tickInterval = 1;
            }

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
                    categories: kadouJikanFrekuensiTrouble.map(item => item.machine_no),
                    crosshair: true,
                    labels: {
                        step: 1,
                        autoRotation: [-45, -90],
                        autoRotationLimit: 80,
                        style: {
                            fontSize: '8px'
                        }
                    }
                }],
                // Swap: first axis = cases (right), second = percentage (left)
                yAxis: [casesAxisConfig, {
                    // Percentage axis — show on right
                    labels: { format: '{value}%', style: {} },
                    title: { text: '(%)', align: 'high', offset: 0, rotation: 0, y: -20, style: {} },
                    max: 100,
                    opposite: true
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
                // Map series to correct axes: cases -> yAxis[0] (right, column), percentage -> yAxis[1] (left, line)
                series: [{
                    name: 'Frekuensi Trouble',
                    type: 'column',
                    // cases axis (left)
                    yAxis: 0,
                    data: kadouJikanFrekuensiTrouble.map(item => item.frekuensi_trouble || 0),
                    tooltip: { valueSuffix: ' (cases)' },
                    color: '#29A3FF',
                }, {
                    name: 'Kadou Jikan (%)',
                    type: 'spline',
                    // percentage axis (right)
                    yAxis: 1,
                    data: kadouJikanFrekuensiTrouble.map(item => parseFloat(item.kadou_jikan) || 0),
                    tooltip: { valueSuffix: ' %' },
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
                            xAxis: [{
                                labels: {
                                    rotation: -90,
                                    style: {
                                        fontSize: '11px'
                                    }
                                }
                            }],
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
        }

        let productionLossMachineDaily = [];

        function createProductionLossChart() {
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
                    categories: productionLossMachineDaily.map(item => item.machineno),
                    crosshair: true,
                    labels: {
                        step: 1,
                        autoRotation: [-45, -90],
                        autoRotationLimit: 80,
                        style: {
                            fontSize: '8px',
                        }
                    },
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
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor ||
                        'rgba(255,255,255,0.25)',
                    itemStyle: {
                        fontSize: '10px',
                    }
                },
                series: [{
                    name: 'Produksi (Kg)',
                    type: 'column',
                    yAxis: 1,
                    data: productionLossMachineDaily.map(item => parseFloat(item.berat_produksi || 0)),
                    tooltip: {
                        valueSuffix: ' Kg',
                        valueDecimals: 1
                    },
                    color: '#29A3FF',
                }, {
                    name: 'Loss (%)',
                    type: 'spline',
                    color: '#ff9900',
                    // Asumsi loss adalah persentase dari total produksi
                    data: productionLossMachineDaily.map(item => item.berat_loss_percentage || 0),
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
                            xAxis: [{
                                labels: {
                                    rotation: -90,
                                    style: {
                                        fontSize: '11px'
                                    }
                                }
                            }],
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
        }

        let LossPerMachineDaily = [];

        function createLossPerMesinChart() {
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
                    categories: LossPerMachineDaily.map(item => item.machineno),
                    labels: {
                        step: 1,
                        autoRotation: [-45, -90],
                        autoRotationLimit: 80,
                        style: {
                            fontSize: '8px'
                        }
                    }
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
                    data: LossPerMachineDaily.map(item => parseFloat(item.berat_loss) || 0),
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
                            xAxis: {
                                labels: {
                                    rotation: -45,
                                    style: {
                                        fontSize: '16px'
                                    }
                                }
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
        }

        let LossPerKasusDaily = [];

        function createLossPerKasusChart() {
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
                    categories: LossPerKasusDaily.map(item => item.loss_name),
                    labels: {
                        step: 1,
                        autoRotation: [-45, -90],
                        autoRotationLimit: 80,
                        style: {
                            fontSize: '8px'
                        }
                    }
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
                    data: LossPerKasusDaily.map(item => parseFloat(item.berat_loss) || 0),
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
                            xAxis: {
                                labels: {
                                    rotation: -65,
                                    style: {
                                        fontSize: '14px'
                                    }
                                }
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
        }

        let mesinMasalahLossDaily = [];

        function loadMesinMasalahLossDailyTable() {
            // loss name
            const lossName = $('#mesinMasalahLossDaily');
            lossName.html('');

            const tbody = $('#mesinMasalahLossDailyTable tbody');
            tbody.empty();

            if (!Array.isArray(mesinMasalahLossDaily) || mesinMasalahLossDaily.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            lossName.html(mesinMasalahLossDaily[0].loss_name);

            let row = '<tr>';

            mesinMasalahLossDaily.forEach((item, idx) => {
                // Batasi hanya 3 kolom pertama
                if (idx >= 3) return;

                row += `
                    <td class="text-center">
                        <span class="badge bg-masalah-${idx + 1} fs-5">
                            ${item.machineno}
                        </span>
                    </td>
                `;
            });

            row += '</tr>';

            tbody.append(row);
        }

        let rankingProblemMachineDaily = [];

        function loadRankingProblemMachineDailyTable() {
            const tbody = $('#rankingProblemMachineDailyTable tbody');
            tbody.empty();

            if (!Array.isArray(rankingProblemMachineDaily) || rankingProblemMachineDaily.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            let row = '<tr>';

            rankingProblemMachineDaily.forEach((item, idx) => {
                // Batasi hanya 3 kolom pertama
                if (idx >= 3) return;

                row += `
                    <td class="text-center">
                        <span class="badge bg-masalah-${idx + 1} fs-5">
                            ${item.machineno}
                        </span>
                    </td>
                `;
            });

            row += '</tr>';

            tbody.append(row);
        }

        function loadInitialDailyData() {
            const form = $('#form-dashboard-daily');

            if (form.length === 0) {
                console.warn('Form #form-dashboard-daily tidak ditemukan');
                return;
            }

            const data = form.serialize();
            const method = form.attr('method') || 'POST';

            // Set loading state
            setButtonLoading(true);

            let completedRequests = 0;
            const totalRequests = 6;

            const checkAllComplete = () => {
                completedRequests++;
                if (completedRequests === totalRequests) {
                    setButtonLoading(false);
                }
            };

            // Placeholder loading
            $('#produksiLossPerMesin, #lossPerMesin, #lossPerKasus, #kadouJikanFrekuensiTrouble').html(
                '<div class="text-center p-4">Loading initial data...</div>');

            // placeholder loading table
            $('#mesinMasalahLossDailyTable tbody, #rankingProblemMachineDailyTable tbody').html(
                '<tr><td colspan="3" class="text-center p-4">Loading initial data...</td></tr>');

            // produksi loss per mesin
            fetchData('{{ route('api.dashboard-seitai-produksi-loss-per-mesin') }}', data, method)
                .then(res => {
                    productionLossMachineDaily = res || [];
                    if (productionLossMachineDaily.length > 0) {
                        createProductionLossChart();
                    } else {
                        $('#produksiLossPerMesin').html(
                            '<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#produksiLossPerMesin').html(
                        '<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);

            // top loss per mesin
            fetchData('{{ route('api.dashboard-seitai-top-loss-per-mesin') }}', data, method)
                .then(res => {
                    LossPerMachineDaily = res || [];
                    if (LossPerMachineDaily.length > 0) {
                        createLossPerMesinChart();
                    } else {
                        $('#lossPerMesin').html('<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#lossPerMesin').html('<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);

            // top loss per kasus
            fetchData('{{ route('api.dashboard-seitai-top-loss-per-kasus') }}', data, method)
                .then(res => {
                    LossPerKasusDaily = res || [];
                    if (LossPerKasusDaily.length > 0) {
                        createLossPerKasusChart();
                    } else {
                        $('#lossPerKasus').html('<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#lossPerKasus').html('<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);

            // kadouJikanFrekuensiTrouble
            fetchData('{{ route('api.dashboard-seitai-kadou-jikan-frekuensi-trouble') }}', data, method)
                .then(res => {
                    kadouJikanFrekuensiTrouble = res || [];
                    if (kadouJikanFrekuensiTrouble.length > 0) {
                        createKadouJikanFrekuensiTroubleChart();
                    } else {
                        $('#kadouJikanFrekuensiTrouble').html(
                            '<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#kadouJikanFrekuensiTrouble').html(
                        '<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);
            // top mesin masalah loss
            fetchData('{{ route('api.dashboard-seitai-top-mesin-masalah-loss-daily') }}', data, method)
                .then(res => {
                    mesinMasalahLossDaily = res || [];
                    if (mesinMasalahLossDaily.length > 0) {
                        loadMesinMasalahLossDailyTable();
                    } else {
                        $('#mesinMasalahLossDailyTable tbody').html(
                            '<tr><td colspan="3" class="text-center p-4">Tidak ada data untuk ditampilkan</td></tr>'
                        );
                    }
                })
                .catch(() => {
                    $('#mesinMasalahLossDailyTable tbody').html(
                        '<tr><td colspan="3" class="text-center p-4">Error loading data</td></tr>');
                })
                .always(checkAllComplete);
            // top mesin masalah loss
            fetchData('{{ route('api.dashboard-seitai-ranking-problem-machine-daily') }}', data, method)
                .then(res => {
                    rankingProblemMachineDaily = res || [];
                    if (rankingProblemMachineDaily.length > 0) {
                        loadRankingProblemMachineDailyTable();
                    } else {
                        $('#rankingProblemMachineDailyTable tbody').html(
                            '<tr><td colspan="3" class="text-center p-4">Tidak ada data untuk ditampilkan</td></tr>'
                        );
                    }
                })
                .catch(() => {
                    $('#rankingProblemMachineDailyTable tbody').html(
                        '<tr><td colspan="3" class="text-center p-4">Error loading data</td></tr>');
                })
                .always(checkAllComplete);
        }

        /*
         * Monthly
         */

        const formatNumber = (num, digit = 2) => {
            return parseFloat(num || 0).toLocaleString('id-ID', {
                minimumFractionDigits: digit,
                maximumFractionDigits: digit
            });
        };

        const formatNumberInMillion = (num, digit = 1) => {
            const million = parseFloat(num || 0) / 1000000;
            return million.toLocaleString('id-ID', {
                minimumFractionDigits: digit,
                maximumFractionDigits: digit
            });
        };

        let totalProduksiPerBulan = [];

        function loadTotalProduksiPerBulanTable() {
            const tbody = $('#totalProduksiPerBulan tbody');
            tbody.empty();

            if (!Array.isArray(totalProduksiPerBulan) || totalProduksiPerBulan.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="5" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            const period = ['A', 'B', 'C'];
            let totalTarget = 0;
            let totalAktual = 0;
            let totalSelisih = 0;

            totalProduksiPerBulan.forEach((data, idx) => {
                if (idx >= 3) return; // Hanya 3 periode

                const targetQty = Math.round(data.target_produksi || 0);
                const aktualQty = Math.round(data.total_produksi || 0);
                const selisihQty = aktualQty - targetQty;

                // berat produksi
                const targetBerat = Math.round(data.target_berat_produksi || 0);
                const aktualBerat = Math.round(data.total_berat_produksi || 0);
                const selisihBerat = aktualBerat - targetBerat;

                tbody.append(`
                    <tr>
                        <td class="fw-semibold fs-6" rowspan="2">${period[idx]}</td>
                        <td class="fw-semibold fs-6">Kg</td>
                        <td class="fw-semibold fs-6">${formatNumber(targetBerat, 0)}</td>
                        <td class="fw-semibold fs-6">${formatNumber(aktualBerat, 0)}</td>
                        <td class="fw-semibold fs-6 ${selisihBerat < 0 ? 'text-danger' : 'text-success'}">
                            ${formatNumber(selisihBerat, 0)}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-semibold fs-6">Lembar</td>
                        <td class="fw-semibold fs-6">${formatNumberInMillion(targetQty, 1)}</td>
                        <td class="fw-semibold fs-6">${formatNumberInMillion(aktualQty, 1)}</td>
                        <td class="fw-semibold fs-6 ${selisihQty < 0 ? 'text-danger' : 'text-success'}">
                            ${formatNumberInMillion(selisihQty, 1)}
                        </td>
                    </tr>
                `);
            });

            // Tambahkan baris total
            // tbody.append(`
            //     <tr>
            //         <td class="fw-bold fs-6">Total</td>
            //         <td class="fw-bold fs-6">${formatNumber(totalTarget, 0)}</td>
            //         <td class="fw-bold fs-6">${formatNumber(totalAktual, 0)}</td>
            //         <td class="fw-bold fs-6 ${totalSelisih < 0 ? 'text-danger' : 'text-success'}">
            //             ${formatNumber(totalSelisih, 0)}
            //         </td>
            //     </tr>
            // `);
        }

        let peringatanKatagae = [];

        function loadPeringatanKatagaeTable() {
            const tbody = $('#table-peringatan-katagae tbody');
            tbody.empty();

            if (!Array.isArray(peringatanKatagae) || peringatanKatagae.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            peringatanKatagae.forEach((data, idx) => {
                if (idx >= 5) return; // Hanya 3 periode

                const sisa_meter = parseFloat(data.sisa_meter || 0);

                tbody.append(`
                    <tr>
                        <td>${data.machineno}</td>
                        <td>${data.lpk_no}</td>
                        <td>${data.product_name}</td>
                        <td>${formatNumber(sisa_meter, 0)}</td>
                        <td>${data.jam}</td>
                        <td>${data.menit}</td>
                    </tr>
                `);
            });
        }

        let lossPerBulan = [];

        function loadLossPerBulanChart() {
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
                    categories: lossPerBulan[1].map(item => item.machineno),
                    // title: {
                    //     text: 'Loss',
                    // },
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
                    name: 'Periode A',
                    color: '#d35400',
                    data: lossPerBulan[1].map(item => parseFloat(item.berat_loss) || 0)
                }, {
                    name: 'Periode B',
                    color: '#ff9900',
                    data: lossPerBulan[2] ? lossPerBulan[2].map(item => parseFloat(item.berat_loss) || 0) : []
                }, {
                    name: 'Periode C',
                    color: '#ffbd53',
                    data: lossPerBulan[3] ? lossPerBulan[3].map(item => parseFloat(item.berat_loss) || 0) : []
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
        }

        // Top 10 Loss Per Kasus Monthly
        let topLossPerKasusMonthly = [];

        function loadTopLossPerKasusMonthlyChart() {
            // Ekstrak nama kasus dari periode pertama yang ada
            const firstPeriodKey = Object.keys(topLossPerKasusMonthly)[0];
            const kasusNames = topLossPerKasusMonthly[firstPeriodKey].map(item => item.loss_name);

            Highcharts.chart('topLossPerKasusMonthly', {
                chart: {
                    type: 'column',
                    height: 250
                },
                exporting: {
                    enabled: false,
                },
                title: {
                    text: 'TOP 10 KASUS LOSS PER BULAN',
                    align: 'center'
                },
                xAxis: {
                    categories: kasusNames,
                    labels: {
                        rotation: -45,
                        step: 1,
                        style: {
                            fontSize: '9px'
                        },
                        formatter: function() {
                            const maxLength = 15;
                            if (this.value.length > maxLength) {
                                return this.value.substring(0, maxLength) + '...';
                            }
                            return this.value;
                        }
                    }
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
                    name: 'Periode C',
                    data: topLossPerKasusMonthly[3] ? topLossPerKasusMonthly[3].map((item) => parseFloat(item.berat_loss) || 0) : [],
                    stack: 'loss',
                    color: '#ffbd53'
                }, {
                    name: 'Periode B',
                    data: topLossPerKasusMonthly[2] ? topLossPerKasusMonthly[2].map((item) => parseFloat(item.berat_loss) || 0) : [],
                    stack: 'loss',
                    color: '#ff9900'
                }, {
                    name: 'Periode A',
                    data: topLossPerKasusMonthly[1] ? topLossPerKasusMonthly[1].map((item) => parseFloat(item.berat_loss) || 0) : [],
                    stack: 'loss',
                    color: '#d35400'
                }],
                legend: {
                    reversed: true
                },
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
                            xAxis: {
                                labels: {
                                    rotation: -65,
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
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
        }

        //  Produksi Per Bulan
        let produksiPerBulan = [];

        function loadProduksiPerBulanChart() {
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
                    categories: produksiPerBulan[1].map(item => item.machineno),
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
                    name: 'Periode C',
                    data: produksiPerBulan[3] ? produksiPerBulan[3].map((item) => parseFloat(item.qty_produksi) || 0) : [],
                    stack: 'produksi',
                    color: '#93D1FF'
                }, {
                    name: 'Periode B',
                    data: produksiPerBulan[2] ? produksiPerBulan[2].map((item) => parseFloat(item.qty_produksi) || 0) : [],
                    stack: 'produksi',
                    color: '#29A3FF'
                }, {
                    name: 'Periode A',
                    data: produksiPerBulan[1] ? produksiPerBulan[1].map((item) => parseFloat(item.qty_produksi) || 0) : [],
                    stack: 'produksi',
                    color: '#0070C0'
                }],
                legend: {
                    reversed: true
                },
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
        }

        function setButtonMonthlyLoading(isLoading) {
            const button = $('#form-dashboard-monthly-button');
            if (isLoading) {
                button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
            } else {
                button.prop('disabled', false).html('Filter');
            }
        }

        let mesinMasalahLossMonthly = [];

        function loadMesinMasalahLossMonthlyTable() {
            // loss name
            const lossName = $('#mesinMasalahLossMonthly');
            lossName.html('');

            const tbody = $('#mesinMasalahLossMonthlyTable tbody');
            tbody.empty();

            if (!Array.isArray(mesinMasalahLossMonthly) || mesinMasalahLossMonthly.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            lossName.html(mesinMasalahLossMonthly[0].loss_name);

            tbody.append(`
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-masalah-1 fs-5">${mesinMasalahLossMonthly[0].machineno}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-masalah-2 fs-5">${mesinMasalahLossMonthly[1].machineno}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-masalah-3 fs-5">${mesinMasalahLossMonthly[2].machineno}</span>
                        </td>
                    </tr>
                `);
        }


        let rankingProblemMachineMonthly = [];

        function loadRankingProblemMachineMonthlyTable() {
            const tbody = $('#rankingProblemMachineMonthlyTable tbody');
            tbody.empty();

            if (!Array.isArray(rankingProblemMachineMonthly) || rankingProblemMachineMonthly.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td>
                    </tr>
                `);
                return;
            }

            let row = '<tr>';

            rankingProblemMachineMonthly.forEach((item, idx) => {
                // Batasi hanya 3 kolom pertama
                if (idx >= 3) return;

                row += `
                    <td class="text-center">
                        <span class="badge bg-masalah-${idx + 1} fs-5">
                            ${item.machineno}
                        </span>
                    </td>
                `;
            });

            row += '</tr>';

            tbody.append(row);
        }

        function loadInitialMonthlyData() {
            const form = $('#form-dashboard-monthly');

            if (form.length === 0) {
                console.warn('Form #form-dashboard-monthly tidak ditemukan');
                return;
            }

            const data = {
                filterDateMonthly: form.find('input[name="filterDateMonthly"]').val(),
                factory: $('#factory').val(),
            };
            const method = form.attr('method') || 'GET';

            // Set loading state
            setButtonMonthlyLoading(true);

            let completedRequests = 0;
            const totalRequests = 6;

            const checkAllComplete = () => {
                completedRequests++;
                if (completedRequests === totalRequests) {
                    setButtonMonthlyLoading(false);
                }
            };

            // Placeholder loading
            $('#lossPerBulan, #produksiPerBulan, #topLossPerKasusMonthly').html(
                '<div class="text-center p-4">Loading initial data...</div>');

            // placeholder loading table
            $('#rankingProblemMachineMonthlyTable tbody, #mesinMasalahLossMonthlyTable tbody').html(
                '<tr><td colspan="3" class="text-center p-4">Loading initial data...</td></tr>');

            $('#totalProduksiPerBulan tbody').html(
                '<tr><td colspan="5" class="text-center p-4">Loading initial data...</td></tr>');

            // total produksi per bulan
            fetchData('{{ route('api.dashboard-seitai-total-produksi-per-bulan') }}', data, method)
                .then(res => {
                    totalProduksiPerBulan = res || [];
                    if (totalProduksiPerBulan.length != 0) {
                        loadTotalProduksiPerBulanTable();
                    } else {
                        $('#totalProduksiPerBulan tbody').html(
                            '<tr><td colspan="4" class="text-center p-4">Tidak ada data untuk ditampilkan</td></tr>'
                        );
                    }
                })
                .catch(() => {
                    $('#totalProduksiPerBulan tbody').html(
                        '<tr><td colspan="4" class="text-center p-4 text-danger">Error loading data</td></tr>'
                    );
                })
                .always(checkAllComplete);

            // loss per bulan
            fetchData('{{ route('api.dashboard-seitai-loss-per-bulan') }}', data, method)
                .then(res => {
                    lossPerBulan = res || [];
                    if (lossPerBulan.length != 0) {
                        loadLossPerBulanChart();
                    } else {
                        $('#lossPerBulan').html(
                            '<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#lossPerBulan').html(
                        '<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);

            // produksi per bulan
            fetchData('{{ route('api.dashboard-seitai-produksi-per-bulan') }}', data, method)
                .then(res => {
                    produksiPerBulan = res || [];
                    if (produksiPerBulan.length != 0) {
                        loadProduksiPerBulanChart();
                    } else {
                        $('#produksiPerBulan').html(
                            '<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>');
                    }
                })
                .catch(() => {
                    $('#produksiPerBulan').html(
                        '<div class="text-center p-4 text-danger">Error loading data</div>');
                })
                .always(checkAllComplete);

            // ranking mesin masalah
            fetchData('{{ route('api.dashboard-seitai-ranking-problem-machine-monthly') }}', data, method)
                .then(res => {
                    rankingProblemMachineMonthly = res || [];
                    if (rankingProblemMachineMonthly.length > 0) {
                        loadRankingProblemMachineMonthlyTable();
                    } else {
                        $('#rankingProblemMachineMonthlyTable tbody').html(
                            '<tr><td colspan="3" class="text-center p-4">Tidak ada data untuk ditampilkan</td></tr>'
                        );
                    }
                })
                .catch(() => {
                    $('#rankingProblemMachineMonthlyTable tbody').html(
                        '<tr><td colspan="3" class="text-center p-4">Error loading data</td></tr>');
                })
                .always(checkAllComplete);

            // top mesin masalah loss
            fetchData('{{ route('api.dashboard-seitai-top-mesin-masalah-loss-monthly') }}', data, method)
                .then(res => {
                    mesinMasalahLossMonthly = res || [];
                    if (mesinMasalahLossMonthly.length > 0) {
                        loadMesinMasalahLossMonthlyTable();
                    } else {
                        $('#mesinMasalahLossMonthlyTable tbody').html(
                            '<tr><td colspan="3" class="text-center p-4">Tidak ada data untuk ditampilkan</td></tr>'
                        );
                    }
                })
                .catch(() => {
                    $('#mesinMasalahLossMonthlyTable tbody').html(
                        '<tr><td colspan="3" class="text-center p-4">Error loading data</td></tr>');
                })
                .always(checkAllComplete);

            // top 10 loss per kasus monthly
            fetchData('{{ route('api.dashboard-seitai-top-loss-per-kasus-monthly') }}', data, method)
                .then(res => {
                    topLossPerKasusMonthly = res || [];
                    if (Object.keys(topLossPerKasusMonthly).length > 0) {
                        loadTopLossPerKasusMonthlyChart();
                    } else {
                        $('#topLossPerKasusMonthly').html(
                            '<div class="text-center p-4">Tidak ada data untuk ditampilkan</div>'
                        );
                    }
                })
                .catch(() => {
                    $('#topLossPerKasusMonthly').html(
                        '<div class="text-center p-4 text-danger">Error loading data</div>'
                    );
                })
                .always(checkAllComplete);

                // Set factory period text
                const factoryPeriodText = data.factory ? ` - ${listFactory[data.factory]['name']}` : '';
                $('#factory-period').text(factoryPeriodText);
        }

        $(document).ready(function() {

            setTimeout(function() {
                loadInitialDailyData();
                loadInitialMonthlyData();
            }, 500);

            // Auto-refresh dashboard data based on REFRESH_DASHBOARD_MINUTES from env
            setInterval(function() {
                console.log('Auto-refreshing dashboard data...');
                loadInitialDailyData();
                loadInitialMonthlyData();
            }, refreshIntervalMs);

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
        });
    </script>
@endsection
