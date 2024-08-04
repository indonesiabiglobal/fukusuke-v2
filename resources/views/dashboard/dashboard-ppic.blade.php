@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center">
                                <form action="{{ route('dashboard-ppic') }}" method="get" class=" d-flex">
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
    {{-- kadou jikan seitai --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1 col-12">
                                    <a class="link-primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#modalListMesinInfure">
                                        INFURE, Kadou Jikan
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanInfure" data-colors='["--tb-success"]' class="apex-charts"
                                        dir="ltr"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
            </div>
        </div>
    </div>
    {{-- kadou jikan seitai --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1 col-12">
                                    <a class="link-primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#modalListMesinSeitai">
                                        SEITAI, Kadou Jikan
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanSeitai" data-colors='["--tb-success"]' class="apex-charts"
                                        dir="ltr"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
            </div>
        </div>
    </div>
    {{-- Hsail Produksi --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">INFURE, Hasil Produksi Tertinggi dan Terendah</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div id="hasilProduksiInfure" data-colors='["--tb-primary", "--tb-success"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI, Hasil Produksi Tertinggi dan Terendah</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div id="hasilProduksiSeitai" data-colors='["--tb-primary", "--tb-success"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    {{-- Loss --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">INFURE, Loss</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="lossInfure" data-colors='["--tb-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI, Loss </h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="lossSeitai" data-colors='["--tb-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    <div class="row">
        {{-- Presentase Loss Infure --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">INFURE, Presentase Loss</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="presentaseLossInfure"
                        data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-info"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end Presentase Loss Infure -->
        {{-- Presentase Loss Seitai --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI, Presentase Loss</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="presentaseLossSeitai"
                        data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-info"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end Presentase Loss Seitai -->

    </div>
    <!-- end row -->
    <!-- end Loss -->
    {{-- TOP Trouble --}}
    <div class="row">
        {{-- TOP Trouble Infure --}}
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">INFURE, Top 3 Loss </h4>
                    <div class="flex-shrink-0">
                    </div>
                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed" id="topLossInfure">
                    </ul>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
        {{-- TOP Trouble Seitai --}}
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">SEITAI, Top 3 Loss </h4>
                    <div class="flex-shrink-0">
                    </div>
                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed" id="topLossSeitai">
                    </ul>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div> <!-- end row-->
    {{-- Counter Trouble --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">INFURE, Counter Trouble </h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="courterTroubleInfure" data-colors='["--tb-info"]' class="apex-charts" dir="ltr">
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI, Counter Trouble</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="courterTroubleSeitai" data-colors='["--tb-info"]' class="apex-charts" dir="ltr">
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div> <!-- end row-->
    {{-- List mesin --}}
    {{-- <div class="row">
        <div class="col-xxl-9">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">LIST MESIN</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-primary btn-sm">
                            Export Report
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    <div id="listMesinHidup"
                        data-colors='["--tb-success", "--tb-info", "--tb-primary", "--tb-warning", "--tb-secondary"]'
                        class="apex-charts mt-n3" dir="ltr"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <p class="text-truncate text-muted fs-14 mb-0">
                                        <i class="mdi mdi-circle align-middle text-primary me-2"></i>www.google.com
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="mb-0">24.58%</p>
                                </div>
                            </div><!-- end -->
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <p class="text-truncate text-muted fs-14 mb-0">
                                        <i class="mdi mdi-circle align-middle text-warning me-2"></i>www.medium.com
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="mb-0">12.22%</p>
                                </div>
                            </div><!-- end -->
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-truncate text-muted fs-14 mb-0">
                                        <i class="mdi mdi-circle align-middle text-secondary me-2"></i>Other
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="mb-0">17.58%</p>
                                </div>
                            </div><!-- end -->
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <p class="text-truncate text-muted fs-14 mb-0">
                                        <i class="mdi mdi-circle align-middle text-info me-2"></i>www.youtube.com
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="mb-0">17.51%</p>
                                </div>
                            </div><!-- end -->
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <p class="text-truncate text-muted fs-14 mb-0">
                                        <i class="mdi mdi-circle align-middle text-success me-2"></i>www.meta.com
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="mb-0">23.05%</p>
                                </div>
                            </div><!-- end -->
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
    </div> --}}
    <!-- end row-->
    {{-- Modal Infure Kadou jikan  --}}
    <div class="modal  fade bs-example-modal-center" id="modalListMesinInfure" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">INFURE Machine Running Rate (Kadou Jikan)</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                @foreach ($listMachineInfure['listDepartment'] as $department)
                                    <tr>
                                        <td>{{ $department['department_name'] }}</td>
                                        @foreach ($listMachineInfure['listMachineInfure'] as $machine)
                                            @if ($machine->department_id == $department['department_id'])
                                                <td class="bg-danger">{{ $machine->machineno }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>
                        </div><!-- end card body -->
                    </div>
                    <div class="mt-4">
                        <div class="hstack gap-2 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            {{-- <a href="javascript:void(0);" class="btn btn-danger">Try Again</a> --}}
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- Modal Seitai Kadou jikan  --}}
    <div class="modal  fade bs-example-modal-center" id="modalListMesinSeitai" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">SEITAI Machine Running Rate (Kadou Jikan)</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                @foreach ($listMachineSeitai['listDepartment'] as $department)
                                    <tr>
                                        <td>{{ $department['department_name'] }}</td>
                                        @foreach ($listMachineSeitai['listMachineSeitai'] as $machine)
                                            @if ($machine->department_id == $department['department_id'])
                                                <td class="bg-danger">{{ $machine->machineno }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>
                        </div><!-- end card body -->
                    </div>
                    <div class="mt-4">
                        <div class="hstack gap-2 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            {{-- <a href="javascript:void(0);" class="btn btn-danger">Try Again</a> --}}
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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

    <script>
        $(document).ready(function() {
            $('#filterDate').flatpickr({
                mode: "range",
                dateFormat: "d-m-Y",
                defaultDate: ['today to today'],
            });
            /*
            Infure
            */
            //    Kadou Jikan Infure
            let kadouJikanInfureMesin = @json($kadouJikanInfureMesin);
            let chartColumnDatatalabelColors = getChartColorsArray("kadouJikanInfure");
            if (chartColumnDatatalabelColors) {
                let options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val + "%";
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#adb5bd"]
                        }
                    },
                    series: [{
                        name: 'Mesin Kerja',
                        data: kadouJikanInfureMesin.map(item => parseFloat(item.persenmesinkerja))
                    }],
                    colors: chartColumnDatatalabelColors,
                    grid: {
                        borderColor: '#f1f1f1',
                    },
                    xaxis: {
                        categories: kadouJikanInfureMesin.map(item => item.machine_no),
                        position: 'top',
                        labels: {
                            offsetY: -18,
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        crosshairs: {
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    colorFrom: '#D8E3F0',
                                    colorTo: '#BED1E6',
                                    stops: [0, 100],
                                    opacityFrom: 0.4,
                                    opacityTo: 0.5,
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            offsetY: -35,

                        }
                    },
                    fill: {
                        gradient: {
                            shade: 'light',
                            type: "horizontal",
                            shadeIntensity: 0.25,
                            gradientToColors: undefined,
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [50, 0, 100, 100]
                        },
                    },
                    yaxis: {
                        axisBorder: {
                            show: true
                        },
                        axisTicks: {
                            show: true,
                        },
                        labels: {
                            show: true,
                            formatter: function(val) {
                                return val + "%";
                            }
                        }
                    },
                    title: {
                        text: 'INFURE Machine Running Rate (Kadou Jikan)',
                        floating: true,
                        offsetY: 320,
                        align: 'center',
                        style: {
                            color: '#444'
                        },
                        style: {
                            fontWeight: 500,
                        },
                    },
                }

                let chart = new ApexCharts(
                    document.querySelector("#kadouJikanInfure"),
                    options
                );

                chart.render();
            }
            // end Kadou Jikan Infure

            // Hasil Produksi Infure
            let hasilProduksiInfure = @json($hasilProduksiInfure);
            let linechartDatalabelColors = getChartColorsArray("hasilProduksiInfure");
            if (linechartDatalabelColors) {
                let options = {
                    chart: {
                        height: 380,
                        type: 'line',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: linechartDatalabelColors,
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        width: [3, 3],
                        curve: 'straight'
                    },
                    series: [{
                            name: "Tertinggi",
                            data: hasilProduksiInfure.map(item => parseFloat(item.max))
                        },
                        {
                            name: "Terendah",
                            data: hasilProduksiInfure.map(item => parseFloat(item.min))
                        }
                    ],
                    // title: {
                    //     text: 'Hasil Produksi',
                    //     align: 'left',
                    //     style: {
                    //         fontWeight: 500,
                    //     },
                    // },
                    grid: {
                        row: {
                            colors: ['transparent',
                                'transparent'
                            ], // takes an array which will be repeated on columns
                            opacity: 0.2
                        },
                        borderColor: '#f1f1f1'
                    },
                    markers: {
                        style: 'inverted',
                        size: 6
                    },
                    xaxis: {
                        categories: hasilProduksiInfure.map(item => item.machine_no),
                        title: {
                            text: 'Nomer Mesin'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Hasil Produksi'
                        },
                        // min: 5,
                        // max: 40
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    },
                    responsive: [{
                        breakpoint: 600,
                        options: {
                            chart: {
                                toolbar: {
                                    show: false
                                }
                            },
                            legend: {
                                show: false
                            },
                        }
                    }]
                }

                let chart = new ApexCharts(
                    document.querySelector("#hasilProduksiInfure"),
                    options
                );
                chart.render();
            }
            // end Hasil Produksi Infure

            // Loss Infure
            let lossInfure = @json($lossInfure);
            let linechartBasicColors = getChartColorsArray("lossInfure");
            if (linechartBasicColors) {
                let options = {
                    series: [{
                        name: "Berat Loss",
                        data: lossInfure.lossInfure.map(item => parseFloat(item.berat_loss).toFixed(2))
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    markers: {
                        size: 4,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    colors: linechartBasicColors,
                    // title: {
                    //     text: 'Product Trends by Month',
                    //     align: 'left',
                    //     style: {
                    //         fontWeight: 500,
                    //     },
                    // },

                    xaxis: {
                        categories: lossInfure.lossInfure.map(item => item.loss_name),
                    }
                };

                let chart = new ApexCharts(document.querySelector("#lossInfure"), options);
                chart.render();
            }

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
            //  end top loss infure
        });

        // Counter Table Infure
        let courterTroubleInfure = @json($counterTroubleInfure);
        var chartColumnRotateLabelsColors = getChartColorsArray("courterTroubleInfure");
        if (chartColumnRotateLabelsColors) {
            var options = {
                series: [{
                    name: 'Counter Loss',
                    data: courterTroubleInfure.map(item => parseFloat(item.counterloss))
                }],
                // annotations: {
                //     points: [{
                //         x: 'Bananas',
                //         seriesIndex: 0,
                //         label: {
                //             borderColor: '#775DD0',
                //             offsetY: 0,
                //             style: {
                //                 color: '#fff',
                //                 background: '#775DD0',
                //             },
                //             text: 'Bananas are good',
                //         }
                //     }]
                // },
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2
                },
                colors: chartColumnRotateLabelsColors,
                xaxis: {
                    labels: {
                        rotate: -45
                    },
                    categories: courterTroubleInfure.map(item => item.loss_name),
                    tickPlacement: 'on'
                },
                yaxis: {
                    title: {
                        text: 'Servings',
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 0.85,
                        stops: [50, 0, 100]
                    },
                }
            };

            var chart = new ApexCharts(document.querySelector("#courterTroubleInfure"),
                options);
            chart.render();
        }
        // end Counter Table Infure

        /*
        SEITAI
        */
        //    Kadou Jikan Seitai
        let kadouJikanSeitaiMesin = @json($kadouJikanSeitaiMesin);
        var chartColumnDatatalabelColors = getChartColorsArray("kadouJikanSeitai");
        if (chartColumnDatatalabelColors) {
            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%";
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#adb5bd"]
                    }
                },
                series: [{
                    name: 'Mesin Kerja',
                    data: kadouJikanSeitaiMesin.map(item => parseFloat(item.persenmesinkerja))
                }],
                colors: chartColumnDatatalabelColors,
                grid: {
                    borderColor: '#f1f1f1',
                },
                xaxis: {
                    categories: kadouJikanSeitaiMesin.map(item => item.machine_no),
                    position: 'top',
                    labels: {
                        offsetY: -18,

                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        offsetY: -35,

                    }
                },
                fill: {
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [50, 0, 100, 100]
                    },
                },
                yaxis: {
                    axisBorder: {
                        show: true
                    },
                    axisTicks: {
                        show: true,
                    },
                    labels: {
                        show: true,
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                },
                title: {
                    text: 'SEITAI Machine Running Rate (Kadou Jikan)',
                    floating: true,
                    offsetY: 320,
                    align: 'center',
                    style: {
                        color: '#444'
                    },
                    style: {
                        fontWeight: 500,
                    },
                },
            }

            var chart = new ApexCharts(
                document.querySelector("#kadouJikanSeitai"),
                options
            );

            chart.render();
        }
        // end Kadou Jikan Seitai

        // Hasil Produksi Seitai
        let hasilProduksiSeitai = @json($hasilProduksiSeitai);
        var linechartDatalabelColors = getChartColorsArray("hasilProduksiSeitai");
        if (linechartDatalabelColors) {
            var options = {
                chart: {
                    height: 380,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                colors: linechartDatalabelColors,
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    width: [3, 3],
                    curve: 'straight'
                },
                series: [{
                        name: "Tertinggi",
                        data: hasilProduksiSeitai.map(item => parseFloat(item.max))
                    },
                    {
                        name: "Terendah",
                        data: hasilProduksiSeitai.map(item => parseFloat(item.min))
                    }
                ],
                // title: {
                //     text: 'Average High & Low Temperature',
                //     align: 'left',
                //     style: {
                //         fontWeight: 500,
                //     },
                // },
                grid: {
                    row: {
                        colors: ['transparent',
                            'transparent'
                        ], // takes an array which will be repeated on columns
                        opacity: 0.2
                    },
                    borderColor: '#f1f1f1'
                },
                markers: {
                    style: 'inverted',
                    size: 6
                },
                xaxis: {
                    categories: hasilProduksiSeitai.map(item => item.machine_no),
                    title: {
                        text: 'Nomer Mesin'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Hasil Produksi'
                    },
                    // min: 5,
                    // max: 40
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    floating: true,
                    offsetY: -25,
                    offsetX: -5
                },
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            toolbar: {
                                show: false
                            }
                        },
                        legend: {
                            show: false
                        },
                    }
                }]
            }

            var chart = new ApexCharts(
                document.querySelector("#hasilProduksiSeitai"),
                options
            );
            chart.render();
        }
        // end Hasil Produksi Seitai

        // Loss Seitai
        let lossSeitai = @json($lossSeitai);
        var linechartBasicColors = getChartColorsArray("lossSeitai");
        if (linechartBasicColors) {
            var options = {
                series: [{
                    name: "Berat Loss",
                    data: lossSeitai.lossSeitai.map(item => parseFloat(item.berat_loss).toFixed(2))
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                markers: {
                    size: 4,
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                colors: linechartBasicColors,
                // title: {
                //     text: 'Product Trends by Month',
                //     align: 'left',
                //     style: {
                //         fontWeight: 500,
                //     },
                // },

                xaxis: {
                    categories: lossSeitai.lossSeitai.map(item => item.loss_name),
                }
            };

            var chart = new ApexCharts(document.querySelector("#lossSeitai"), options);
            chart.render();
        }

        // pie chart presentase loss
        var chartPieBasicColors = getChartColorsArray("presentaseLossSeitai");
        if (chartPieBasicColors) {
            var options = {
                series: lossSeitai.lossSeitai.map(item => parseFloat(parseFloat(item.berat_loss / lossSeitai
                    .totalLossSeitai * 100).toFixed(2))),
                chart: {
                    height: 300,
                    type: 'pie',
                },
                labels: lossSeitai.lossSeitai.map(item => item.loss_name),
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

            var chart = new ApexCharts(document.querySelector("#presentaseLossSeitai"),
                options);
            chart.render();
        }

        // top loss seitai
        let topLossSeitai = @json($topLossSeitai);
        var html = '';
        topLossSeitai.map((item, index) => {
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
        $('#topLossSeitai').html(html);
        //  end top loss seitai

        // Counter Table Seitai
        let courterTroubleSeitai = @json($counterTroubleSeitai);
        var chartColumnRotateLabelsColors = getChartColorsArray("courterTroubleSeitai");
        if (chartColumnRotateLabelsColors) {
            var options = {
                series: [{
                    name: 'Counter Loss',
                    data: courterTroubleSeitai.map(item => parseFloat(item.counterloss))
                }],
                // annotations: {
                //     points: [{
                //         x: 'Bananas',
                //         seriesIndex: 0,
                //         label: {
                //             borderColor: '#775DD0',
                //             offsetY: 0,
                //             style: {
                //                 color: '#fff',
                //                 background: '#775DD0',
                //             },
                //             text: 'Bananas are good',
                //         }
                //     }]
                // },
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2
                },
                colors: chartColumnRotateLabelsColors,
                xaxis: {
                    labels: {
                        rotate: -45
                    },
                    categories: courterTroubleSeitai.map(item => item.loss_name),
                    tickPlacement: 'on'
                },
                yaxis: {
                    title: {
                        text: 'Servings',
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 0.85,
                        stops: [50, 0, 100]
                    },
                }
            };

            var chart = new ApexCharts(document.querySelector("#courterTroubleSeitai"),
                options);
            chart.render();
        }
    </script>
@endsection
