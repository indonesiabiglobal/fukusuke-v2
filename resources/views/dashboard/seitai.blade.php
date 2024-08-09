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
                                <form action="{{ route('dashboard-seitai') }}" method="get" class=" d-flex">
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
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanSeitai"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
            </div>
        </div>
    </div>

    {{-- Loss --}}
    <div class="row">
        <div class="col-12 col-xl-7">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0">SEITAI Loss</h4>
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
                            @foreach ($lossSeitai['lossSeitai'] as $data)
                                <tr>
                                    <td>{{ $loop->iteration }} </td>
                                    <td>{{ $data->loss_name }} </td>
                                    <td>{{ round($data->berat_loss, 2) }}</td>
                                    <td>
                                        @php
                                            $loss = round(
                                                ($data->berat_loss / $lossSeitai['totalLossSeitai']) * 100,
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
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Loss Tertinggi</h5>
                                        <span class="badge bg-warning rounded-pill">{{ $filterDate }}</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <small class="text-danger text-nowrap fw-semibold"><i
                                                class="bx bx-chevron-down"></i>
                                            {{ $higherLossPercentage }}%
                                            dari loss
                                        </small>
                                        <h3 class="mb-0">{{ $higherLoss }}</h3>
                                    </div>
                                </div>
                                <div id="profileReportChart"></div>
                                <div id="growthChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TOP Trouble Seitai --}}
                <div class="col-12 col-md-6 col-xl-12">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">SEITAI Top 3 Loss </h4>
                            <div class="flex-shrink-0">
                            </div>
                        </div><!-- end card header -->
                        <div class="card-body pt-0">
                            <ul class="list-group list-group-flush border-dashed" id="topLossSeitai">
                            </ul>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
            </div>
        </div><!-- end col -->
    </div>
    {{-- end Loss --}}

    {{-- TOP Trouble --}}
    <div class="row">
        {{-- Counter Trouble --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI Counter Trouble </h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="counterTroubleSeitai" data-colors='["--tb-info"]' class="apex-charts" dir="ltr">
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div> <!-- end row-->

    {{-- Hsail Produksi --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEITAI Hasil Produksi Tertinggi dan Terendah</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div id="hasilProduksiSeitai" data-colors='["--tb-primary", "--tb-success"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
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

            let table = $('#scroll-vertical').DataTable({
                "scrollY": "250px",
                "scrollCollapse": true,
                "paging": false
            });

            /*
            Seitai
            */
            let kadouJikanSeitaiMesin = @json($kadouJikanSeitaiMesin);


            // Kadou Jikan Seitai
            Highcharts.chart('kadouJikanSeitai', {
                chart: {
                    type: 'column'
                },
                title: {
                    align: 'left',
                    text: `<a href="#" id="kadouJikanTitle" class="text-muted">
                               SEITAI Machine Running Rate (Kadou Jikan)
                            </a>`,
                    useHTML: true
                },
                // subtitle: {
                //     align: 'left',
                //     text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
                // },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category',
                    title: {
                        text: 'Machine No'
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
                    name: 'Division Seitai',
                    colorByPoint: false,
                    data: kadouJikanSeitaiMesin.map(item => {
                        return {
                            name: item.machine_no,
                            y: parseFloat(item.persenmesinkerja),
                        };
                    })
                }]
            });

            document.getElementById('kadouJikanTitle').addEventListener('click', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalListMesinSeitai'));
                myModal.show();
            });

            // Hasil Produksi Seitai
            let hasilProduksiSeitai = @json($hasilProduksiSeitai);
            let linechartDatalabelColors = getChartColorsArray("hasilProduksiSeitai");
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
                            data: hasilProduksiSeitai.map(item => parseFloat(item.max))
                        },
                        {
                            name: "Terendah",
                            data: hasilProduksiSeitai.map(item => parseFloat(item.min))
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

                let chart = new ApexCharts(
                    document.querySelector("#hasilProduksiSeitai"),
                    options
                );
                chart.render();
            }
            // end Hasil Produksi Seitai

            // top loss seitai
            let topLossSeitai = @json($topLossSeitai);
            let html = '';
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
            //  end top loss seitai
        });

        // Counter Table Seitai
        let counterTroubleSeitai = @json($counterTroubleSeitai);
        var chartColumnRotateLabelsColors = getChartColorsArray("counterTroubleSeitai");
        if (chartColumnRotateLabelsColors) {
            var options = {
                series: [{
                    name: 'Counter Loss',
                    data: counterTroubleSeitai.map(item => parseFloat(item.counterloss))
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
                    categories: counterTroubleSeitai.map(item => item.loss_name),
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

            var chart = new ApexCharts(document.querySelector("#counterTroubleSeitai"),
                options);
            chart.render();
        }

        // end Counter Table Seitai
    </script>
@endsection
