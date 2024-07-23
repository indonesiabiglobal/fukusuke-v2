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
                {{-- <div class="col-xl-4">
                    <div class="row">
                        <div class="col-xl-12 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Total Earnings</p>
                                            <h4 class="fs-22 fw-semibold mb-3">$<span class="counter-value" data-target="745.35">0</span></h4>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="text-success fs-12 mb-0">
                                                    <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +18.30 %
                                                </h5>
                                                <p class="text-muted mb-0">than last week</p>
                                            </div>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                                <i class="bx bx-dollar-circle text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="animation-effect-6 text-success opacity-25 fs-18">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="animation-effect-4 text-success opacity-25 fs-18">
                                    <i class="bi bi-currency-pound"></i>
                                </div>
                                <div class="animation-effect-3 text-success opacity-25 fs-18">
                                    <i class="bi bi-currency-euro"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                                <i class="bx bx-shopping-bag text-info"></i>
                                            </span>
                                        </div>
                                        <div class="text-end flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Orders</p>
                                            <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="698.36">0</span>k </h4>
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <h5 class="text-danger fs-12 mb-0">
                                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -2.74 %
                                                </h5>
                                                <p class="text-muted mb-0">than last week</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="animation-effect-6 text-info opacity-25 left fs-18">
                                    <i class="bi bi-handbag"></i>
                                </div>
                                <div class="animation-effect-4 text-info opacity-25 left fs-18">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <div class="animation-effect-3 text-info opacity-25 left fs-18">
                                    <i class="bi bi-bag-check"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Customers</p>
                                            <h4 class="fs-22 fw-semibold mb-3"><span class="counter-value" data-target="183.35">0</span>M </h4>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="text-success fs-12 mb-0">
                                                    <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 %
                                                </h5>
                                                <p class="text-muted mb-0">than last week</p>
                                            </div>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                <i class="bx bx-user-circle text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                                <div class="animation-effect-6 text-warning opacity-25 fs-18">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="animation-effect-4 text-warning opacity-25 fs-18">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="animation-effect-3 text-warning opacity-25 fs-18">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
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
