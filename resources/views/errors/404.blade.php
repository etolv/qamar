@php
    $customizerHidden = 'customizer-hide';
    $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Error - Pages')

@section('page-style')
    <!-- Page -->
    @vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <img src="{{ asset('assets/img/qamar_samaya_logo-' . $configData['style'] . '.svg') }}" width="500"
                alt="auth-login-cover" class="img-fluid my-5 auth-illustration">
            <h2 class="mb-1 mt-4">{{ _t('Page Not Found') }} :(</h2>
            <p class="mb-4 mx-2">{{ _t('Oops') }}! 😖 {{ _t('The requested URL was not found on this server') }}.</p>
            <a href="{{ url('/') }}" class="btn btn-primary mb-4">{{ _t('Back to home') }}</a>
            <div class="mt-4">
                <span class="app-brand-logo demo">
                </span>
            </div>
        </div>
    </div>
    <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['style'] . '.png') }}"
            alt="page-misc-error" data-app-light-img="illustrations/bg-shape-image-light.png"
            data-app-dark-img="illustrations/bg-shape-image-dark.png">
    </div>
<!-- /Error -->@endsection
