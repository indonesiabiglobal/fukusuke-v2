@extends('layouts.master-components')
@section('title') @lang('translation.profile') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Custom UI @endslot
@slot('title') Profile @endslot
@endcomponent


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Basic Example</h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-none mb-0">
                    <div class="card-body rounded profile-basic" style="background-image: url('build/images/small/img-4.jpg');background-size: cover;"></div>
                    <div class="card-body">
                        <div class="mt-n5">
                            <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-lg rounded-circle p-1 mt-n3">
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row justify-content-between gy-4">
                            <div class="col-xl-5 col-md-7">
                                <h5 class="fs-17">Edward Diana</h5>
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-geo-alt"></i> Phoenix, USA
                                </div>
                                <p>Product visual designer, expert in UI design</p>

                                <div class="hstack gap-2">
                                    <button type="button" class="btn btn-primary">Invite to Project</button>
                                    <button type="button" class="btn btn-soft-info btn-icon"><i class="bi bi-chat-left-text"></i></button>
                                    <div class="dropdown">
                                        <button class="btn btn-soft-danger btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-5">
                                <div>
                                    <p class="text-muted fw-medium mb-2">Language Knows</p>
                                    <ul class="list-inline mb-4">
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">English</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">Russian</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">Chinese</span>
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <p class="text-muted fw-medium mb-2">Featured Skills</p>
                                    <ul class="d-flex gap-2 flex-wrap list-unstyled mb-0">
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Business Marketing</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Google Ads Management</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Social Ads Management</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Content SEO</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Center Profile</h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-none mb-0">
                    <div class="card-body rounded profile-basic" style="background-image: url('build/images/small/img-4.jpg');background-size: cover;"></div>
                    <div class="card-body">
                        <div class="mt-n5 text-center">
                            <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-lg rounded-circle p-1 mt-n3 mx-auto">
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row justify-content-center gy-4">
                            <div class="col-lg-5 text-center">
                                <h5 class="fs-17">Edward Diana</h5>
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-geo-alt"></i> Phoenix, USA
                                </div>
                                <p>Product visual designer, expert in UI design</p>

                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-primary">Invite to Project</button>
                                    <button type="button" class="btn btn-soft-info btn-icon"><i class="bi bi-chat-left-text"></i></button>
                                    <div class="dropdown">
                                        <button class="btn btn-soft-danger btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">End Profile</h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-none mb-0">
                    <div class="card-body rounded profile-basic" style="background-image: url('build/images/small/img-4.jpg');background-size: cover;"></div>
                    <div class="card-body">
                        <div class="mt-n5 text-end">
                            <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-lg rounded-circle p-1 mt-n3">
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row justify-content-between gy-4">
                            <div class="col-xl-3 col-md-5 order-last order-lg-first">
                                <div class="text-end text-lg-start">
                                    <p class="text-muted fw-medium mb-2">Language Knows</p>
                                    <ul class="list-inline mb-4">
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">English</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">Russian</span>
                                        </li>
                                        <li class="list-inline-item">
                                            <span class="badge text-info bg-info-subtle">Chinese</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="text-end text-lg-start">
                                    <p class="text-muted fw-medium mb-2">Featured Skills</p>
                                    <ul class="d-flex gap-2 flex-wrap list-unstyled mb-0 justify-content-end justify-content-lg-start">
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Business Marketing</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Google Ads Management</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Social Ads Management</span>
                                        </li>
                                        <li>
                                            <span class="badge text-secondary bg-secondary-subtle">Content SEO</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-5 col-md-7 text-end">
                                <h5 class="fs-17">Edward Diana</h5>
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-geo-alt"></i> Phoenix, USA
                                </div>
                                <p>Product visual designer, expert in UI design</p>

                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-primary">Invite to Project</button>
                                    <button type="button" class="btn btn-soft-info btn-icon"><i class="bi bi-chat-left-text"></i></button>
                                    <div class="dropdown">
                                        <button class="btn btn-soft-danger btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <img src="{{ URL::asset('build/images/small/img-4.jpg') }}" alt="" class="w-100 rounded object-cover" height="120">

                <div class="text-center">
                    <div class="mt-n4">
                        <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-md rounded-circle p-1 mt-n2">
                    </div>
                    <div class="mt-4 border-bottom border-bottom-dashed">
                        <h5 class="fs-17">Edward Diana</h5>
                        <p class="mb-3 text-muted">Marketing Expert</p>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="254"></span></h5>
                                    <p class="text-muted mb-0">Posts</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="12"></span>k</h5>
                                    <p class="text-muted mb-0">Followers</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="3501"></span></h5>
                                    <p class="text-muted mb-0">Follwing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="w-100 rounded object-cover bg-secondary" style="height: 120px;"></div>

                <div class="text-center">
                    <div class="mt-n4">
                        <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-md rounded-circle p-1 mt-n2">
                    </div>
                    <div class="mt-4 border-bottom border-bottom-dashed">
                        <h5 class="fs-17">Edward Diana</h5>
                        <p class="mb-3 text-muted">Marketing Expert</p>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="254"></span></h5>
                                    <p class="text-muted mb-0">Posts</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="12"></span>k</h5>
                                    <p class="text-muted mb-0">Followers</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="3501"></span></h5>
                                    <p class="text-muted mb-0">Follwing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="w-100 rounded object-cover bg-success-subtle" style="height: 120px;"></div>

                <div class="text-center">
                    <div class="mt-n4">
                        <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-md rounded-circle p-1 mt-n2">
                    </div>
                    <div class="mt-4 border-bottom border-bottom-dashed">
                        <h5 class="fs-17">Edward Diana</h5>
                        <p class="mb-3 text-muted">Marketing Expert</p>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="254"></span></h5>
                                    <p class="text-muted mb-0">Posts</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="12"></span>k</h5>
                                    <p class="text-muted mb-0">Followers</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="3501"></span></h5>
                                    <p class="text-muted mb-0">Follwing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xl-3 col-md-6">
        <div class="card border-danger border-opacity-25">
            <div class="card-body">
                <div class="w-100 rounded object-cover bg-danger-subtle" style="height: 120px;"></div>

                <div class="text-center">
                    <div class="mt-n4">
                        <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt="" class="avatar-md rounded-circle p-1 mt-n2">
                    </div>
                    <div class="mt-4 border-bottom border-bottom-dashed border-danger border-opacity-25">
                        <h5 class="fs-17">Edward Diana</h5>
                        <p class="mb-3 text-muted">@edward_diana</p>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="254"></span></h5>
                                    <p class="text-muted mb-0">Posts</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="12"></span>k</h5>
                                    <p class="text-muted mb-0">Followers</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div>
                                    <h5><span class="counter-value" data-target="3501"></span></h5>
                                    <p class="text-muted mb-0">Follwing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/tom-select/js/tom-select.base.min.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
