@extends('layouts.master')
@section('title')
@lang('translation.settings')
@endsection
@section('content')

<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="tab-content">  
                    <form method="POST" action="pages-profile-settings" wire:submit="resetPassword">
                        <div class="row g-2 justify-content-lg-between align-items-center">
                            <div class="col-lg-4">
                                <div>
                                    <label for="oldpasswordInput" class="form-label">Old Password*</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="oldpasswordInput" placeholder="Enter current password">
                                        <button class="btn btn-link position-absolute top-0 end-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>

                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="auth-pass-inputgroup">
                                    <label for="password-input" class="form-label">New Password*</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control password-input" id="password-input" onpaste="return false" placeholder="Enter new password" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="auth-pass-inputgroup">
                                    <label for="confirm-password-input" class="form-label">Confirm Password*</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control password-input" onpaste="return false" id="confirm-password-input" placeholder="Confirm password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="confirm-password-input"><i class="ri-eye-fill align-middle"></i></button>
                                    </div>

                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0);" class="link-primary text-decoration-underline">Forgot Password ?</a>
                                <div class="">

                                    <button type="submit" class="btn btn-success">Change Password</button>
                                </div>
                            </div>

                            <!--end col-->

                            <div class="col-lg-12">
                                <div class="card bg-light passwd-bg" id="password-contain">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h5 class="fs-13">Password must contain:</h5>
                                        </div>
                                        <div class="">
                                            <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                            <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                            <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                            <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection
@section('script')
<script src="{{ URL::asset('build/js/pages/passowrd-create.init.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/profile-setting.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
