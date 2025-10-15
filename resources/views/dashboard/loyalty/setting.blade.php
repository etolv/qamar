@extends('layouts/layoutMaster')

@section('title', 'Loyalty Setting')

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
        <h5 class="card-title mb-3">{{_t('Loyalty Settings')}}</h5>
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
                $cash_to_points = $settings->where('key', 'cash_to_points')->first()?->value ?? 0;
                $points_to_cash = $settings->where('key', 'points_to_cash')->first()?->value ?? 0;
                $cash = 100;
                $points = $cash * $cash_to_points;
                $return = $points_to_cash * $points;
                $arrow = session()->get('locale') == 'ar' ? 'fa-arrow-left' : 'fa-arrow-right';
                @endphp
                <form method="post" action="{{route('loyalty.settings.update')}}" id="myForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="col-sm-3 col-form-label" for="basic-icon-default-fullname">{{_t('Cash')}} <i class="fa-solid {{$arrow}}"></i></label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-money-bill"></i></span>
                                    <input type="text" name="cash" value="{{old('cash') ?? $cash}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Cash')}}" aria-label="{{_t('Cash')}}" aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('cash')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="col-md-1 text-center">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div> -->
                        <div class="col-md-4">
                            <label class="col-sm-3 col-form-label" for="basic-icon-default-fullname">{{_t('Points')}} <i class="fa-solid {{$arrow}}"></i></label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-gift"></i></span>
                                    <input type="text" name="points" value="{{old('points') ?? $points}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('points')}}" aria-label="{{_t('points')}}" aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('points')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="col-md-1 text-center">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div> -->
                        <div class="col-md-4">
                            <label class="col-sm-3 col-form-label" for="basic-icon-default-fullname">{{_t('Return')}}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                                    <input type="text" name="return" value="{{old('return') ?? $return}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('return')}}" aria-label="{{_t('return')}}" aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('return')
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