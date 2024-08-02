@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    {{-- kadou jikan seitai --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1 col-7">KADOU JIKAN INFURE</h4>
                                <div class="input-group">
                                    <input type="text" id="filterDate" class="form-control" data-provider="flatpickr"
                                        data-date-format="d-m-Y" data-range-date="true" onchange="reloadChart()">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanInfure" data-colors='["--tb-dark", "--tb-primary", "--tb-secondary"]'
                                        class="apex-charts" dir="ltr"></div>
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
                                <h4 class="card-title mb-0 flex-grow-1 col-7">KADOU JIKAN SEITAI</h4>
                                {{-- <div class="input-group">
                                    <input type="text" id="filterDate" class="form-control" data-provider="flatpickr"
                                        data-date-format="d-m-Y" data-range-date="true"
                                        onchange="getKadouJikanSeitai(); getTopLossSeitai(); getCounterTroubleSeitai()">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                </div> --}}
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanSeitai" data-colors='["--tb-dark", "--tb-primary", "--tb-secondary"]'
                                        class="apex-charts" dir="ltr"></div>
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
                    <h4 class="card-title mb-0">Hasil Produksi Infure Tertinggi dan Terendah</h4>
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
                    <h4 class="card-title mb-0">Hasil Produksi Seitai Tertinggi dan Terendah</h4>
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
                    <h4 class="card-title mb-0">Loss Infure</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="lossInfure" data-colors='["--tb-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Loss Seitai</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="lossSeitai" data-colors='["--tb-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    <!-- end row -->
    {{-- TOP Trouble --}}
    <div class="row">
        {{-- TOP Trouble Infure --}}
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">TOP 3 LOSS INFURE</h4>
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
                    <h4 class="card-title mb-0 flex-grow-1">TOP 3 LOSS SEITAI</h4>
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
                    <h4 class="card-title mb-0">COUNTER TROUBLE INFURE</h4>
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
                    <h4 class="card-title mb-0">COUNTER TROUBLE SEITAI</h4>
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

                    <div id="color_heatmap"
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
                defaultDate: ['today'],
            });
            reloadChart();
        });

        const reloadChart = () => {
            getKadouJikanInfure();
            getHasilProduksiInfure();
            getLossInfure()
            getTopLossInfure();
            getCounterTroubleInfure();

            getKadouJikanSeitai();
            getHasilProduksiSeitai();
            getLossSeitai();
            getTopLossSeitai();
            getCounterTroubleSeitai();
        }

        /*
        Infure
        */
        const getKadouJikanInfure = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('kadou-jikan-infure') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {

                    var chartColumnDatatalabelColors = getChartColorsArray("kadouJikanInfure");
                    if (chartColumnDatatalabelColors) {
                        let persenmesinkerja = [];
                        let machine_name = [];
                        let machine_no = [];

                        response.data.map((item, index) => {
                            persenmesinkerja.push(parseFloat(item.persenmesinkerja));
                            machine_name.push(item.machine_name.substr(2));
                            machine_no.push(item.machine_no);
                        });
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
                                enabled: false,
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
                                data: persenmesinkerja
                            }],
                            colors: chartColumnDatatalabelColors,
                            grid: {
                                borderColor: '#f1f1f1',
                            },
                            xaxis: {
                                categories: machine_name,
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
                                    show: false
                                },
                                axisTicks: {
                                    show: false,
                                },
                                labels: {
                                    show: false,
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

                        var chart = new ApexCharts(
                            document.querySelector("#kadouJikanInfure"),
                            options
                        );

                        chart.render();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getHasilProduksiInfure = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('hasil-produksi-infure') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    var linechartDatalabelColors = getChartColorsArray("hasilProduksiInfure");
                    if (linechartDatalabelColors) {
                        let max = [];
                        let min = [];
                        let machine_no = [];

                        response.data.map((item, index) => {
                            max.push(parseFloat(item.max));
                            min.push(parseFloat(item.min));
                            machine_no.push(item.machine_no);
                        });
                        console.log(max);
                        console.log(min);
                        console.log(machine_no);
                        console.log([26, 24, 32, 36, 33, 31, 33]);
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
                                    data: max
                                },
                                {
                                    name: "Terendah",
                                    data: min
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
                                categories: machine_no,
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
                            document.querySelector("#hasilProduksiInfure"),
                            options
                        );
                        chart.render();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getLossInfure = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('loss-infure') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    var linechartBasicColors = getChartColorsArray("lossInfure");
                    if (linechartBasicColors) {
                        let loss_name = [];
                        let berat_loss = [];

                        response.data.map((item, index) => {
                            loss_name.push(item.loss_name);
                            berat_loss.push(parseFloat(parseFloat(item.berat_loss).toFixed(2)));
                        });
                        var options = {
                            series: [{
                                name: "Berat Loss",
                                data: berat_loss
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
                                categories: loss_name,
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#lossInfure"), options);
                        chart.render();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getTopLossInfure = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('top-loss-infure') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    let topLoss = response.data;
                    let html = '';
                    topLoss.map((item, index) => {
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

                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getCounterTroubleInfure = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('counter-trouble-infure') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    //  Column with Rotated Labels
                    var chartColumnRotateLabelsColors = getChartColorsArray("courterTroubleInfure");
                    if (chartColumnRotateLabelsColors) {
                        let counterLoss = [];
                        let loss_name = [];
                        let loss_code = [];

                        response.data.map((item, index) => {
                            counterLoss.push(parseFloat(item.counterloss));
                            loss_name.push(item.loss_name);
                            loss_code.push(item.loss_code);
                        });

                        var options = {
                            series: [{
                                name: 'Counter Loss',
                                data: counterLoss
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
                                categories: loss_name,
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
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        /*
        SEITAI
        */
        const getKadouJikanSeitai = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('kadou-jikan-seitai') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {

                    var chartColumnDatatalabelColors = getChartColorsArray("kadouJikanSeitai");
                    if (chartColumnDatatalabelColors) {
                        let persenmesinkerja = [];
                        let machine_name = [];
                        let machine_no = [];

                        response.data.map((item, index) => {
                            persenmesinkerja.push(parseFloat(item.persenmesinkerja));
                            machine_name.push(item.machine_name.substr(2));
                            machine_no.push(item.machine_no);
                        });
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
                                enabled: false,
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
                                data: persenmesinkerja
                            }],
                            colors: chartColumnDatatalabelColors,
                            grid: {
                                borderColor: '#f1f1f1',
                            },
                            xaxis: {
                                categories: machine_name,
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
                                    show: false
                                },
                                axisTicks: {
                                    show: false,
                                },
                                labels: {
                                    show: false,
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
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getHasilProduksiSeitai = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('hasil-produksi-seitai') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    var linechartDatalabelColors = getChartColorsArray("hasilProduksiSeitai");
                    if (linechartDatalabelColors) {
                        let max = [];
                        let min = [];
                        let machine_no = [];

                        response.data.map((item, index) => {
                            max.push(parseFloat(item.max));
                            min.push(parseFloat(item.min));
                            machine_no.push(item.machine_no);
                        });
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
                                    data: max
                                },
                                {
                                    name: "Terendah",
                                    data: min
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
                                categories: machine_no,
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
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getLossSeitai = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('loss-seitai') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    var linechartBasicColors = getChartColorsArray("lossSeitai");
                    if (linechartBasicColors) {
                        let loss_name = [];
                        let berat_loss = [];

                        response.data.map((item, index) => {
                            loss_name.push(item.loss_name);
                            berat_loss.push(parseFloat(parseFloat(item.berat_loss).toFixed(2)));
                        });
                        var options = {
                            series: [{
                                name: "Berat Loss",
                                data: berat_loss
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
                                categories: loss_name,
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#lossSeitai"), options);
                        chart.render();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getTopLossSeitai = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('top-loss-seitai') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    let topLoss = response.data;
                    let html = '';
                    topLoss.map((item, index) => {
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

                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        const getCounterTroubleSeitai = () => {
            $.ajax({
                type: "GET",
                url: "{{ route('counter-trouble-seitai') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    filterDate: $('#filterDate').val()
                },
                success: function(response) {
                    //  Column with Rotated Labels
                    var chartColumnRotateLabelsColors = getChartColorsArray("courterTroubleSeitai");
                    if (chartColumnRotateLabelsColors) {
                        let counterLoss = [];
                        let loss_name = [];
                        let loss_code = [];

                        response.data.map((item, index) => {
                            counterLoss.push(parseFloat(item.counterloss));
                            loss_name.push(item.loss_name);
                            loss_code.push(item.loss_code);
                        });

                        var options = {
                            series: [{
                                name: 'Counter Loss',
                                data: counterLoss
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
                                categories: loss_name,
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
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endsection
