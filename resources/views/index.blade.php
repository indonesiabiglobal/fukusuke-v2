@extends('layouts.master')
@section('title')
@lang('translation.dashboards')
@endsection
@section('css')
<link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')


<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1 col-7">KADOU JIKAN</h4>
                            <div class="input-group">
                                <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true">
                                <span class="input-group-text py-0">
                                    <i class="ri-calendar-event-fill fs-4"></i>
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0 pb-2">
                            <div class="w-100">
                                <div id="customer_impression_charts" data-colors='["--tb-dark", "--tb-primary", "--tb-secondary"]' class="apex-charts" dir="ltr"></div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-3">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">TOP 3 TROUBLE</h4>
                <div class="flex-shrink-0">
                </div>
            </div><!-- end card header -->
            <div class="card-body pt-0">
                <ul class="list-group list-group-flush border-dashed">
                    <li class="list-group-item ps-0">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                    <div class="text-center">
                                        <h5 class="mb-0">1</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="text-muted mt-0 mb-1 fs-13">
                                    <span class="badge text-bg-primary">LOSS</span> BUBBLE PUTUS</h5>
                                <a href="#" class="text-reset fs-14 mb-0">
                                    <span class="badge text-bg-danger">COUNTER</span> 20</a>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item ps-0">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                    <div class="text-center">
                                        <h5 class="mb-0">2</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="text-muted mt-0 mb-1 fs-13">
                                    <span class="badge text-bg-primary">LOSS</span> WINDER</h5>
                                <a href="#" class="text-reset fs-14 mb-0">
                                    <span class="badge text-bg-danger">COUNTER</span> 17</a>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item ps-0">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                    <div class="text-center">
                                        <h5 class="mb-0">3</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="text-muted mt-0 mb-1 fs-13">
                                    <span class="badge text-bg-primary">LOSS</span> SHIWA</h5>
                                <a href="#" class="text-reset fs-14 mb-0">
                                    <span class="badge text-bg-danger">COUNTER</span> 11</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div>
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
</div> <!-- end row-->

@endsection

@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js')}}"></script>

<!-- dashboard init -->
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>
{{-- <script src="{{ URL::asset('build/js/app.js') }}"></script> --}}

<script src="https://img.themesbrand.com/velzon/apexchart-js/stock-prices.js"></script>
<script src="{{ URL::asset('build/libs/jsvectormap/maps/us-merc-en.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/widgets.init.js') }}"></script>
@endsection
