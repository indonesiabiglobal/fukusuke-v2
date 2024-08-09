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
                            <th>Name</th>
                            <th>Berat</th>
                            <th>Presentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lossInfure['lossInfure'] as $data)
                            <tr>
                                <td>{{ $data->loss_name }} </td>
                                <td>{{ round($data->berat_loss, 2) }}</td>
                                <td>
                                    @php
                                        $loss = round(
                                            ($data->berat_loss / $lossInfure['totalLossInfure']) * 100,
                                            2,
                                        );
                                    @endphp
                                    {{ $loss }}%
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            style="width: {{ $loss }}%;" aria-valuenow="{{ $loss }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
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
                            <th>Name</th>
                            <th>Berat</th>
                            <th>Presentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lossInfure['lossInfure'] as $data)
                            <tr>
                                <td>{{ $data->loss_name }} </td>
                                <td>{{ round($data->berat_loss, 2) }}</td>
                                <td>
                                    @php
                                        $loss = round(
                                            ($data->berat_loss / $lossInfure['totalLossInfure']) * 100,
                                            2,
                                        );
                                    @endphp
                                    {{ $loss }}%
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            style="width: {{ $loss }}%;" aria-valuenow="{{ $loss }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
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


            // Growth Chart - Radial Bar Chart
            // --------------------------------------------------------------------
            
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
    </script>
@endsection
