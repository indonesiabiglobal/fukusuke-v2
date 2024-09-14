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
    {{-- kadou jikan infure --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    {{-- kadou jikan infure --}}
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <div class="card-header border-0 align-items-center d-flex">
                                    <h4 class="card-title mb-0">
                                        <a href="#" id="kadouJikanTitle">
                                            INFURE Machine Running Rate (Kadou Jikan)
                                        </a>
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanInfure"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">INFURE Hasil Produksi Tertinggi dan Terendah</h4>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <div id="hasilProduksiInfure" data-colors='["--tb-primary", "--tb-success"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Loss --}}
    <div class="row">
        <div class="col-12 col-xl-7">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0">INFURE Loss</h4>
                </div>
                <div class="card-body">
                    <table id="scroll-vertical"
                        class="table table-bordered dt-responsive nowrap align-middle mdl-data-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Berat</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lossInfure['lossInfure'] as $data)
                                <tr>
                                    <td>{{ $loop->iteration }} </td>
                                    <td>{{ $data->loss_name }} </td>
                                    <td>{{ round($data->berat_loss, 2) }} Kg</td>
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
        <div class="col-12 col-xl-5">
            <div class="row">
                <div class="col-12 col-md-6 col-xl-12 mb-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between col-7 align-self-center">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Loss Tertinggi</h5>
                                        <span class="badge bg-warning rounded-pill">{{ $filterDate }}</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <h4 class="mb-0">{{ $higherLossName }}</h4>
                                        <p class="mb-0 fw-bold text-muted">{{ $higherLoss }} Kg
                                            <small class="text-danger text-nowrap fw-semibold"><i
                                                    class="bx bx-chevrons-down"></i>
                                                {{ $higherLossPercentage }}%
                                                dari loss
                                            </small></p>
                                    </div>
                                </div>
                                {{-- <div id="profileReportChart"></div> --}}
                                <div id="growthChart" class="col-5"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TOP Trouble Infure --}}
                <div class="col-12 col-md-6 col-xl-12">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">INFURE Top 3 Loss </h4>
                            <div class="flex-shrink-0">
                            </div>
                        </div><!-- end card header -->
                        <div class="card-body pt-0">
                            <ul class="list-group list-group-flush border-dashed" id="topLossInfure">
                            </ul>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
            </div>
        </div><!-- end col -->
    </div>

    <div class="row">
        {{-- infure counter trouble --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">INFURE Counter Trouble </h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="counterTroubleInfure"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    {{-- end Loss --}}

    <!-- end row-->
    {{-- Modal Infure Kadou jikan  --}}
    <div class="modal  fade bs-example-modal-center" id="modalListMesinInfure" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-1">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">INFURE Machine Running Rate (Kadou Jikan)</h4>
                        </div>
                        <div class="card-body">
                            <div style="max-height: 400px; overflow-y: auto; max-width: 100%; overflow-x: auto;">
                                <table class="table">
                                    @foreach ($listMachineInfure['listDepartment'] as $department)
                                        <tr>
                                            <td>{{ $department['department_name'] }}</td>
                                            @foreach ($kadouJikanInfureMesin as $machine)
                                                @if ($machine->department_id == $department['department_id'])
                                                    <td style="padding: 1px;">
                                                        <div class="{{ $machine->persenmesinkerja > 50 ? 'bg-success' : ($machine->persenmesinkerja > 0 ? 'bg-warning' : 'bg-danger') }}"
                                                            style="padding: 10px; width: 100%; height: 100%;"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="{{ $machine->machineno . ': ' . $machine->persenmesinkerja }}%">
                                                            {{ $machine->machine_no }}
                                                        </div>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
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

    {{-- <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"
        integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>


    <script>
        $(document).ready(function() {
            // $('#filterDate').flatpickr({
            //     mode: "range",
            //     dateFormat: "d-m-Y",
            //     defaultDate: ['today to today'],
            // });

            // $('#data-table-basic').DataTable();

            let table = $('#scroll-vertical').DataTable({
                "scrollY": "250px",
                "scrollCollapse": true,
                "paging": false
            });

            /*
            Infure
            */
            let kadouJikanDepartment = @json($kadouJikanDepartment);
            kadouJikanDepartment = Object.values(kadouJikanDepartment);
            let kadouJikanInfureMesin = @json($kadouJikanInfureMesin);
            kadouJikanInfureMesin = Object.values(kadouJikanInfureMesin);


            // Kadou Jikan Infure
            Highcharts.chart('kadouJikanInfure', {
                chart: {
                    type: 'column',
                    height: 300,
                },
                title: {
                    align: 'left',
                    text: ``,
                    style: {
                        fontWeight: 500,
                    },
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category',
                    title: {
                        text: 'Department'
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
                            format: '{point.y:.1f}%'
                        },
                        borderRadius: 8
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color};">{point.name}</span>: ' +
                        '<b>{point.y:.2f}%</b> of total<br/>'
                },
                series: [{
                    name: 'Division Infure',
                    colorByPoint: false,
                    data: kadouJikanDepartment.map(item => {
                        return {
                            name: item.departmentName,
                            y: parseFloat(item.persenMesinDepartment),
                            drilldown: item.departmentId
                        }
                    })
                }],
                drilldown: {
                    breadcrumbs: {
                        position: {
                            align: 'right'
                        }
                    },
                    series: kadouJikanDepartment.map(item => {
                        return {
                            name: item.departmentName,
                            id: item.departmentId,
                            data: kadouJikanInfureMesin.filter(mesin => mesin.department_id == item
                                .departmentId).map(mesin => {
                                return [mesin.machine_no, parseFloat(mesin
                                    .persenmesinkerja)]
                            })
                        }
                    })
                }
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
                        height: 280,
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
                    title: {
                        text: 'Hasil Produksi',
                        align: 'left',
                        style: {
                            fontWeight: 500,
                        },
                    },
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
                                        <span class="badge text bg-danger">Berat</span> ${parseFloat(item.berat_loss).toFixed(3)} Kg
                                    </a>
                                </div>
                            </div>
                        </li>`;
            });
            $('#topLossInfure').html(html);


            // Growth Chart - Radial Bar Chart
            // --------------------------------------------------------------------
            const growthChartEl = document.querySelector('#growthChart'),
                growthChartOptions = {
                    series: [{{ $higherLossPercentage }}],
                    labels: ['Loss'],
                    chart: {
                        height: 210,
                        type: 'radialBar'
                    },
                    plotOptions: {
                        radialBar: {
                            size: 150,
                            offsetY: 10,
                            startAngle: -150,
                            endAngle: 150,
                            hollow: {
                                size: '55%'
                            },
                            track: {
                                // background: "--tb-danger",
                                strokeWidth: '100%'
                            },
                            dataLabels: {
                                name: {
                                    offsetY: 15,
                                    color: "#000",
                                    fontSize: '15px',
                                    fontWeight: '600',
                                    fontFamily: 'Public Sans'
                                },
                                value: {
                                    offsetY: -25,
                                    color: "#000",
                                    fontSize: '22px',
                                    fontWeight: '500',
                                    fontFamily: 'Public Sans'
                                }
                            }
                        }
                    },
                    colors: ["#FF0000"],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            shadeIntensity: 0.5,
                            gradientToColors: ["#FF0000"],
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 0.6,
                            stops: [5, 20, 50, 100]
                        }
                    },
                    stroke: {
                        dashArray: 5
                    },
                    grid: {
                        padding: {
                            top: -35,
                            bottom: -10
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        },
                        active: {
                            filter: {
                                type: 'none'
                            }
                        }
                    }
                };
            if (typeof growthChartEl !== undefined && growthChartEl !== null) {
                const growthChart = new ApexCharts(growthChartEl, growthChartOptions);
                growthChart.render();
            }
            //  end top loss infure

        });

        // Counter Table Infure
        let counterTroubleInfure = @json($counterTroubleInfure);
        Highcharts.chart('counterTroubleInfure', {
            chart: {
                zooming: {
                    type: 'xy'
                }
            },
            title: {
                text: '',
                align: 'left'
            },
            xAxis: [{
                categories: counterTroubleInfure.map(item => item.loss_name),
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Counter Loss',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: 'Counter Loss',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                align: 'left',
                verticalAlign: 'top',
                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                    'rgba(255,255,255,0.25)'
            },
            series: [{
                name: 'Loss',
                type: 'column',
                yAxis: 1,
                data: counterTroubleInfure.map(item => parseFloat(item.counterloss)),

            }, {
                name: 'Loss',
                type: 'spline',
                data: counterTroubleInfure.map(item => parseFloat(item.counterloss)),
            }]
        });
        // Highcharts.chart('counterTroubleInfure', {
        //     chart: {
        //         type: 'column',
        //         height: 330,
        //     },
        //     title: {
        //         text: '',
        //         align: 'left'
        //     },
        //     // subtitle: {
        //     //     text: 'Source: <a target="_blank" ' +
        //     //         'href="https://www.indexmundi.com/agriculture/?commodity=corn">indexmundi</a>',
        //     //     align: 'left'
        //     // },
        //     xAxis: {
        //         categories: counterTroubleInfure.map(item => item.loss_name),
        //         crosshair: true,
        //         // accessibility: {
        //         //     description: 'Countries'
        //         // }
        //     },
        //     yAxis: {
        //         min: 0,
        //         title: {
        //             text: 'Counter Loss'
        //         }
        //     },
        //     tooltip: {
        //         valueSuffix: ''
        //     },
        //     plotOptions: {
        //         column: {
        //             pointPadding: 0.2,
        //             borderWidth: 0
        //         }
        //     },
        //     series: [{
        //         name: 'Loss',
        //         data: counterTroubleInfure.map(item => parseFloat(item.counterloss))
        //     }, ]
        // });

        // end Counter Table Infure
    </script>
@endsection
