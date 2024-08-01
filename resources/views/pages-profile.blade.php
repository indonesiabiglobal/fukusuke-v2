@extends('layouts.master')
@section('title')
@lang('translation.profile')
@endsection
@section('css')
<link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card overflow-hidden">
            <div class="rounded profile-basic position-relative" style="background-image: url('build/images/profile-bg.jpg');background-size: cover;background-position: center;">
                <div class="bg-overlay bg-primary"></div>
            </div>
            <div class="card-body">
                <div class="position-relative">
                    <div class="mt-n5">
                        <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}" alt="" class="avatar-lg rounded-circle p-1 mt-n4">
                    </div>
                </div>
                <div class="pt-3">
                    <div class="row justify-content-between gy-4">
                        <div class="col-xl-5 col-lg-5">
                            <h5 class="fs-17">{{ Auth::user()->username }}</h5>
                            <div class="hstack gap-1 mb-3 text-muted">
                                <div class="me-2">
                                    <i class="ri-map-pin-user-line me-1 fs-16 align-middle"></i>{{ Auth::user()->email }}
                                </div>
                                {{-- <div>
                                    <i class="ri-building-line me-1 fs-16 align-middle"></i>Themesbrand
                                </div> --}}
                            </div>
                            <p>Employee ID : {{ Auth::user()->userid }}</p>

                            {{-- <div class="hstack gap-2">
                                <button type="button" class="btn btn-success custom-toggle" data-bs-toggle="button" aria-pressed="false">
                                    <span class="icon-on"><i class="ri-user-add-line align-bottom me-1"></i> Connect</span>
                                    <span class="icon-off"><i class="ri-check-fill align-bottom me-1"></i> Unconnect</span>
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-icon"><i class="bi bi-chat-left-text"></i></button>
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
                            </div> --}}
                        </div>
                        <div class="col-xl-3 col-lg-5">
                            {{-- <div>
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
                            </div> --}}

                            <div>
                                <p class="text-muted fw-medium mb-2">Roles :</p>
                                <ul class="d-flex gap-2 flex-wrap list-unstyled mb-0">
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Admin Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Master Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">NippoInfure-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">NippoSeitai-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Order-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">JamKerja-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Kenpin-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Warehouse-Read</span>
                                    </li>
                                    <li>
                                        <span class="badge text-body-emphasis  bg-dark-subtle">Process-Read</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{ URL::asset('build/js/pages/profile.init.js')}}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
