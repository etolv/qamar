@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Cover - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/qamar_samaya_logo-' . $configData['style'] . '.svg') }}"
                        alt="auth-login-cover" class="img-fluid my-5 auth-illustration">
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="app-brand mb-4">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <img src="{{ asset('assets/img/qamar_samaya_logo-' . $configData['style'] . '.svg') }}"
                                width="100" alt="auth-login-cover" class="img-fluid my-5 auth-illustration">

                            <!-- <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])</span> -->
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h3 class=" mb-1">{{ _t('Welcome') }}</h3>
                    <p class="mb-4">{{ _t('Please sign-in to your account and start the adventure') }}</p>

                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ _t('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                placeholder="{{ _t('Enter your phone') }}" autofocus>
                        </div>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">{{ _t('Password') }}</label>
                                <!-- <a href="{{ url('auth/forgot-password-cover') }}"> -->
                                <!-- <small>Forgot Password?</small> -->
                                <!-- </a> -->
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember-me">
                                <label class="form-check-label" for="remember-me">
                                    {{ _t('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100">
                            {{ _t('Sign in') }}
                        </button>
                    </form>

                    <!-- <p class="text-center">
                          <span>New on our platform?</span>
                          <a href="{{ url('auth/register-cover') }}">
                            <span>Create an account</span>
                          </a>
                        </p> -->

                    <!-- <div class="divider my-4">
                          <div class="divider-text">or</div>
                        </div> -->

                    <!-- <div class="d-flex justify-content-center">
                          <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                            <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
                          </a>

                          <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                            <i class="tf-icons fa-brands fa-google fs-5"></i>
                          </a>

                          <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                            <i class="tf-icons fa-brands fa-twitter fs-5"></i>
                          </a>
                        </div> -->
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
@endsection
