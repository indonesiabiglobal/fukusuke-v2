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
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="w-100">
                                <figure class="highcharts-figure">
                                    <div id="infureJenis"></div>
                                </figure>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="w-100">
                                <figure class="highcharts-figure">
                                    <div id="seitaiJenis"></div>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- kadou jikan seitai --}}
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="w-100">
                                <figure class="highcharts-figure">
                                    <div id="tipeInfure"></div>
                                </figure>
                            </div>                            
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="w-100">
                                <figure class="highcharts-figure">
                                    <div id="tipeSeitai"></div>
                                </figure>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

<style>
.highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#infureJenis {
    height: 400px;
}

#seitaiJenis {
    height: 400px;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>

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

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
    Highcharts.chart('infureJenis', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Hasil produksi per-jenis (Seitai-Infure) EG-Arm-Gomi',
            align: 'left'
        },
        accessibility: {
            announceNewData: {
                enabled: true
            },
            point: {
                valueSuffix: '%'
            }
        },

        plotOptions: {
            series: {
                borderRadius: 5,
                dataLabels: [
                {
                    enabled: true,
                    distance: 15,
                    format: '{point.name}'
                }, {
                    enabled: true,
                    distance: '-30%',
                    filter: {
                        property: 'percentage',
                        operator: '>',
                        value: 5
                    },
                    format: '{point.y:f}',
                    style: {
                        fontSize: '0.9em',
                        textOutline: 'none'
                    }
                }]
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                '<b>{point.y:.2f}%</b> of total<br/>'
        },
        series: [
            {
                name: 'Browsers',
                colorByPoint: true,
                data: [
                    {
                        name: 'HD ARM',
                        y: 2877465,
                    },
                    {
                        name: 'HD EG',
                        y: 525287644,
                    },
                    {
                        name: 'HD GOMI',
                        y: 149489116,
                    },
                    {
                        name: 'LD GOMI',
                        y: 21427751,
                    }
                ]
            }
        ],        
    });

    Highcharts.chart('seitaiJenis', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Hasil produksi per-tipe (Seitai-Infure) Standard-Khusus',
            align: 'left'
        },
        accessibility: {
            announceNewData: {
                enabled: true
            },
            point: {
                valueSuffix: '%'
            }
        },

        plotOptions: {
            series: {
                borderRadius: 5,
                dataLabels: [
                {
                    enabled: true,
                    distance: 15,
                    format: '{point.name}'
                }, {
                    enabled: true,
                    distance: '-30%',
                    filter: {
                        property: 'percentage',
                        operator: '>',
                        value: 5
                    },
                    format: '{point.y:f}',
                    style: {
                        fontSize: '0.9em',
                        textOutline: 'none'
                    }
                }]
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                '<b>{point.y:.2f}%</b> of total<br/>'
        },
        series: [
            {
                name: 'Browsers',
                colorByPoint: true,
                data: [
                    {
                        name: 'HD EG',
                        y: 7119100,
                    },
                    {
                        name: 'HD GOMI',
                        y: 1260050,
                    },
                    {
                        name: 'LD GOMI',
                        y: 70000,
                    }
                ]
            }
        ],
        
    });

    Highcharts.chart('tipeInfure', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Hasil produksi per-tipe Infure',
            align: 'left'
        },
        xAxis: {
            categories: ['Juli']
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
        series: 
        [
            {
                name: 'Berat Produksi 2023',
                data: [47006],
                stack: 'Europe'
            }, {
                name: 'Berat Produksi 2024',
                data: [62388],
                stack: 'Europe'
            }, {
                name: 'Panjang Produksi 2023',
                data: [3187670],
                stack: 'North America'
            }, {
                name: 'Panjang Produksi 2024',
                data: [4281250],
                stack: 'North America'
            }
        ]
    });

    Highcharts.chart('tipeSeitai', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Hasil produksi per-tipe Seitai',
            align: 'left'
        },
        xAxis: {
            categories: ['Juli']
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
        series: 
        [
            {
                name: 'Berat Produksi 2023',
                data: [47006],
                stack: 'Europe'
            }, {
                name: 'Berat Produksi 2024',
                data: [62388],
                stack: 'Europe'
            }, {
                name: 'Panjang Produksi 2023',
                data: [3187670],
                stack: 'North America'
            }, {
                name: 'Panjang Produksi 2024',
                data: [4281250],
                stack: 'North America'
            }
        ]
    });
    </script>
@endsection
