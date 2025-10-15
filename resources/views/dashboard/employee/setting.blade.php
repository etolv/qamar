@extends('layouts/layoutMaster')

@section('title', 'Employee profit Setting')

@section('vendor-style')

@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')

@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/apex-charts/apexcharts.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {});
</script>
@endsection
@section('content')
<!-- <h6 class="pb-1 mb-4 text-muted">{{_t('Sliders')}}</h6> -->
<div class="card mb-2">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-3">{{_t('Employee Profit Settings')}}</h5>
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
            <div class="col-md-4">
                @can('create_setting')
                <!-- <a href="{{route('setting.create')}}" class="btn btn-primary mb-3" id="newUserButton">{{_t('New Setting')}}</a> -->
                @endcan
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Basic with Icons -->
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{_t('Update')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                @php
                $employee_minimum_profit = $settings->where('key', 'employee_minimum_profit')->first()?->value ?? 0;
                $employee_profit_percentage = $settings->where('key', 'employee_profit_percentage')->first()?->value ?? 0;
                //$arrow = session()->get('locale') == 'ar' ? 'fa-arrow-left' : 'fa-arrow-right';
                @endphp
                <form method="post" action="{{route('employee.settings.update')}}" id="myForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="col-sm-6 col-form-label" for="basic-icon-default-fullname">{{_t('Employee Minimum Profit')}} </label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="text" name="employee_minimum_profit" value="{{old('employee_minimum_profit') ?? $employee_minimum_profit}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Employee Minimum Profit')}}" aria-label="{{_t('Employee Minimum Profit')}}" aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('employee_minimum_profit')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="col-md-1 text-center">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div> -->
                        <div class="col-md-6">
                            <label class="col-sm-6 col-form-label" for="basic-icon-default-fullname">{{_t('Employee Profit Percentage')}}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-gift"></i></span>
                                    <input type="text" name="employee_profit_percentage" value="{{old('employee_profit_percentage') ?? $employee_profit_percentage}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Employee Profit Percentage')}}" aria-label="{{_t('Employee Profit Percentage')}}" aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('employee_profit_percentage')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{_t('Update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection