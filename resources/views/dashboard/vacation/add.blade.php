@extends('layouts/layoutMaster')

@section('title', 'New Vacation')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss', 'resources/assets/vendor/libs/pickr/pickr-themes.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            let status = 0;
            $('.type_select2').select2({});
            $('.status_select2').select2({});
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employees') }}',
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
            });
            $('.is_hourly').change(function() {
                let isHourly = $(this).is(':checked');
                if (isHourly) {
                    $('.end-date-div').addClass('d-none');
                    $('.hours-div').removeClass('d-none');
                } else {
                    $('.end-date-div').removeClass('d-none');
                    $('.hours-div').addClass('d-none');
                }
                $('#end_date').prop('required', !isHourly);
                $('#from_hour').prop('required', isHourly);
                $('#to_hour').prop('required', isHourly);
            });
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('vacation.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 employee-div {{ $employee ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Employee') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employees[]" name="employees[]"
                                            class="employee_select2 form-select readonly-select" multiple>
                                            @if ($employee)
                                                <option value="{{ $employee->id }}" selected>{{ $employee->user->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('employees')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @foreach ($errors->get('employees.*') as $key => $messages)
                                    @foreach ($messages as $message)
                                        <span class="text-danger">{{ $message }}</span>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div class="row mb-3 {{ $employee ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('All Employees') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input all_employees" name="all_employees"
                                            id="all_employees" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('all_employees')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type" name="type" class="type_select2 form-select" required>
                                            @foreach (App\Enums\VacationTypeEnum::cases() as $type)
                                                <option value="{{ $type->value }}"
                                                    @if (old('type') == $type->value) selected @endif>{{ _t($type->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 {{ $employee ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Status') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="status" name="status" class="status_select2 form-select" required>
                                            @foreach (App\Enums\VacationStatusEnum::cases() as $status)
                                                <option value="{{ $status->value }}"
                                                    @if (old('status') == $status->value) selected @endif>
                                                    {{ _t($status->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Hourly') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input is_hourly" name="is_hourly"
                                            id="is_hourly" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('is_hourly')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Start Date') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" value="{{ old('start_date') }}"
                                    name="start_date" required />
                                @error('start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 end-date-div">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('End Date') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" value="{{ old('end_date') }}"
                                    name="end_date" id="end_date" required />
                                @error('end_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="hours-div d-none">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('From Hour') }} *</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" value="{{ old('from_hour') }}"
                                        name="from_hour" id="from_hour" />
                                    @error('from_hour')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('To Hour') }} *</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" value="{{ old('to_hour') }}"
                                        name="to_hour" id="to_hour" />
                                    @error('to_hour')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mb-3 hours-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Hours') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="number" min="1" max="8" class="form-control"
                                    value="{{ old('hours') }}" name="hours" id="hours" />
                                @error('hours')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('File') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-file"></i></span>
                                    <input type="file" name="file" value="{{ old('file') }}"
                                        value="{{ old('file') }}" id="basic-icon-default-email"
                                        class="form-control" />
                                </div>
                                @error('file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Reason') }}</label>
                            <div class="col-sm-10">
                                <textarea rows="4" class="form-control" placeholder="{{ _t('reason') }}" name="reason" id="reason-editor">{{ old('reason') ?? '' }}</textarea>
                                @error('reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
