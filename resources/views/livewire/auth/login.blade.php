@section('title')
    Login
@endsection

<section class="min-vh-100 p-4 p-lg-5 d-flex align-items-center justify-content-center">
    {{-- <div class="top-tagbar bg-light text-dark" style="padding: 30px;">
        <div class="w-100">
            <div class="row justify-content-between mt-3">
                <div class="col-8">                        
                    <h4>
                        <img width="70px" src="{{ URL::asset('build/images/production.png') }}" alt="Header Avatar">
                        Production Control
                    </h4>
                </div>
                <div class="col-4">
                    <p>Need an Account ?
                        <button class="btn btn-danger w-50 ms-3" type="button">CREATE ACCOUNT</button>
                    </p>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-xl-8 col-lg-6">
                <div class="h-100 mb-0 p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            {{-- <img src="{{ URL::asset('build/images/logo-production.png') }}" alt=""
                                height="32" /> --}}
                                <h1 class="text-dark">Production Control System</h1>
                                <h5 class="text-dark mt-3">Sistem aplikasi monitoring dan <br>
                                pengendalian produksi, untuk mengatur <br>
                                output atau hasil produksi yang optimal <br>
                                dan memperoleh jaminan yang wajar <br>
                                bahwa spesifikasi akan terpenuhi.</h5>
                        </div>
                        <img src="{{ URL::asset('build/images/phone.png') }}" width="250" alt="" />
                    </div>

                    <div class="text-dark mt-4">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1 fw-bold">PT Fukusuke Kogyo Indonesia</p>
                                <span>Blok M-3-2 Kawasan Berikat Mm2100 Cibitung, Bekasi, Jawa Barat, Indonesia.</span>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 fw-bold">Produk Usaha</p>
                                <span>Kantong Plastik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-xl-4 col-lg-6">
                <div class="card mb-0 py-2 border border-3">
                    <div class="card-body p-4 p-sm-5 m-lg-2">
                        <h5 class="fs-22">Sign In</h5>
                        
                        @if (session()->has('error'))
                            <div class="alert alert-borderless alert-danger alert-dismissible mb-2 mx-2">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="alert alert-borderless alert-success alert-dismissible mb-2 mx-2">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="p-2 mt-3">
                            <form method="POST" wire:submit="submit">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" wire:model.live="email" required autocomplete="email" autofocus placeholder="Enter your email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password-input">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                        <input id="password" type="password"
                                            class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                            wire:model.live="password" id="password-input" required value=""
                                            autocomplete="current-password" placeholder="Enter your password">
                                        <button
                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                            type="button" id="password-addon"><i
                                                class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <p>
                                    @if (Route::has('password.reset'))
                                        <a class="text-muted" href="{{ route('password.reset') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </p>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <div class="mt-5">
                                    <button class="btn btn-primary w-100" type="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end conatiner-->
</section>
@section('script')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
@endsection
