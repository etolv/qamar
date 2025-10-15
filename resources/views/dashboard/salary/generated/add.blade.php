@extends('layouts/layoutMaster')

@section('title', 'Generate Salaries')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
'resources/assets/vendor/libs/pickr/pickr.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        let status = 0;
        $('.month_select2').select2({});
        $('.employee_select2').select2({
            placeholder: '{{_t("Select Employee")}}',
            ajax: {
                url: route('employee.search'),
                headers: {
                    'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.type_id,
                                text: item.name + ' - ' + item.phone
                            };
                        })
                    };
                },
                cache: false
            }
        });

        $('.all_employees').change(function() {
            if ($(this).is(':checked')) {
                $('.employee_select2').val(null).trigger('change');
                $('.employee_select2').prop('disabled', true);
            } else {
                $('.employee_select2').prop('disabled', false);
            }
        })
    });
</script>
@endsection

@section('content')
<div class="row">
    <!-- Basic with Icons -->
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{_t('Create')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('generated-salary.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Employees')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <div class="col-sm-10">
                                    <select id="employees" name="employees[]" class="employee_select2 form-select" multiple>
                                    </select>
                                </div>
                            </div>
                            @error('employees')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                            @foreach ($errors->get('employees.*') as $key => $messages)
                            @foreach ($messages as $message)
                            <span class="text-danger">{{ $message }}</span>
                            @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('All Employees')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <label class="switch switch-primary me-0">
                                    <input type="checkbox" class="switch-input all_employees" name="all_employees" id="all_employees" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label"></span>
                                </label>
                            </div>
                            @error('all_employees')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Month')}} *</label>
                        <div class="col-sm-10">
                            <select id="month" name="month" class="month_select2 form-select" required>
                                @foreach(App\Enums\MonthsEnum::cases() as $month)
                                <option value="{{$month->value}}" {{old('month') == $month->value ? 'selected' : (Carbon\Carbon::now()->month == $month->value ? 'selected' : '')}}>{{$month->value . ' - ' ._t($month->name)}}</option>
                                @endforeach
                            </select>
                            @error('month')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('End Date')}}</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" value="{{old('end')}}" name="end_date" />
                            @error('end_date')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div> -->
                    <div class="row justify-content-start">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">{{_t('Create')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection