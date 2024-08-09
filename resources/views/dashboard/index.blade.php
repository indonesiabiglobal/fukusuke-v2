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
                            <div class="card-header border-0 align-items-center d-flex">
                            </div>
                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="kadouJikanInfure"></div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- INFURE Machine Running Rate (Kadou Jikan) --}}
        <div class="col-12 col-xl-9">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">INFURE Machine Running Rate (Kadou Jikan)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
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
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        {{-- TOP Trouble Infure --}}
        <div class="col-12 col-xl-3">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">INFURE Top 3 Loss</h4>
                    <div class="flex-shrink-0">
                    </div>
                </div><!-- end card header -->
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush border-dashed" id="topLossInfure">
                    </ul>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>
    {{-- end Loss --}}


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


    <div class="row">
        {{-- SEITAI Machine Running Rate (Kadou Jikan) --}}
        <div class="col-12 col-xl-9">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">SEITAI Machine Running Rate (Kadou Jikan)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
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
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        {{-- TOP Trouble Infure --}}
        <div class="col-12 col-xl-3">
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
        </div><!-- end col -->
    </div>
    {{-- end Loss --}}

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
                               INFURE Machine Running Rate (Kadou Jikan)
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
                    name: 'Division Infure',
                    colorByPoint: false,
                    data: kadouJikanInfureMesin.map(item => {
                        return {
                            name: item.machine_no,
                            y: parseFloat(item.persenmesinkerja),
                        };
                    })
                }]
            });

            document.getElementById('kadouJikanTitle').addEventListener('click', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalListMesinInfure'));
                myModal.show();
            });


            // top loss infure
            let topLossInfure = @json($topLossInfure);
            let htmlLossInfure = '';
            topLossInfure.map((item, index) => {
                htmlLossInfure += `<li class="list-group-item ps-0">
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
            $('#topLossInfure').html(htmlLossInfure);



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


            // top loss seitai
            let topLossSeitai = @json($topLossSeitai);
            let htmlLossSeitai = '';
            topLossSeitai.map((item, index) => {
                htmlLossSeitai += `<li class="list-group-item ps-0">
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
            $('#topLossSeitai').html(htmlLossSeitai);
        });
    </script>
@endsection
