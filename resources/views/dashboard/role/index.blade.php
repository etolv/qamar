@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>

@endsection

@section('page-script')
    <script src="{{ asset('assets/js/app-access-roles.js') }}"></script>
@endsection

@section('content')

    <!-- Role cards -->
    <div class="row">
        @foreach ($roles as $role)
            @if (!in_array($role->name, ['admin', 'customer', 'company']))
                <div class="col-xl-4 col-lg-6 col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span> {{ $role->users()->count() }} {{ _t('Role users') }}</span>
                                <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                    @foreach ($role->users()->take(5)->get() as $user)
                                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                            title="{{ $user->name }}" class="avatar avatar-sm pull-up">
                                            <img class="h-auto rounded-circle"
                                                src="{{ $user->getFirstMedia('profile') ? $user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png') }}"
                                                alt="Avatar" />
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-1 pt-25">
                                <div class="role-heading">
                                    <h4 class="fw-bolder">{{ $role->name }}</h4>
                                    <a href="{{ route('roles.edit', $role->id) }}" data-bs-target=".modal-create">
                                        <small class="fw-bolder">{{ _t('Edit') }}</small>
                                    </a>
                                </div>
                                <a href="javascript:void(0);" class="text-body delete" data-id={{ $role->id }}>
                                    <i data-feather="trash" class="font-medium-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="d-flex align-items-end justify-content-center h-100">
                            <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}" class="img-fluid mt-2"
                                alt="Image" width="85" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                            <a href="{{ route('roles.create') }}" class="stretched-link text-nowrap add-new-role">
                                <span class="btn btn-primary mb-1">{{ _t('New role') }}</span>
                            </a>
                            <p class="mb-0 mt-1">{{ _t('Add new role') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Role cards -->

@endsection
