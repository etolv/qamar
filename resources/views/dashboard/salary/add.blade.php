@extends('layouts/layoutMaster')

@section('title', 'New Salary')

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
            $('.profit_type_select2').select2({});
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employee') }}',
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
            const flatpickrTimeStart = document.querySelector('#flatpickr-time-start'),
                flatpickrTimeEnd = document.querySelector('#flatpickr-time-end');
            if (flatpickrTimeStart) {
                flatpickrTimeStart.flatpickr({
                    enableTime: true,
                    noCalendar: true
                });
            }
            if (flatpickrTimeEnd) {
                flatpickrTimeEnd.flatpickr({
                    enableTime: true,
                    noCalendar: true
                });
            }
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
                    <form method="post" action="{{ route('salary.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="salary_id" value="{{ $salary?->id }}" />
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Employee') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employee_id" name="employee_id" class="employee_select2 form-select"
                                            data-allow-clear="true">
                                            @if ($employee)
                                                <option value="{{ $employee->id }}" selected>{{ $employee->user->name }} -
                                                    {{ $employee->user->phone }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('employee_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Amount') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="number" step="any" class="form-control"
                                    value="{{ old('amount') ?? $salary?->amount }}" placeholder="{{ _t('Amount') }}"
                                    name="amount" required />
                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Working Hours') }} *</label>
                            <div class="col-sm-10">
                                <input type="number" step="any" class="form-control"
                                    value="{{ old('working_hours') ?? $salary?->working_hours }}"
                                    placeholder="{{ _t('Working Hours') }}" name="working_hours" required />
                                @error('working_hours')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Profit Percentage') }} *</label>
                            <div class="col-sm-10">
                                <input type="number" step="any" class="form-control"
                                    value="{{ old('profit_percentage') ?? ($salary?->profit_percentage ?? 0) }}"
                                    placeholder="{{ _t('Profit Percentage') }}" name="profit_percentage" />
                                @error('profit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Profit Type') }} *</label>
                            <div class="col-sm-10">
                                <select id="profit_type" name="profit_type" class="profit_type_select2 form-select">
                                    @foreach (App\Enums\ProfitTypeEnum::cases() as $type)
                                        <option value="{{ $type->value }}"
                                            {{ $salary?->type == $type ? 'selected' : '' }}>
                                            {{ _t(strtolower(str_replace('_', ' ', $type->name))) }}</option>
                                    @endforeach
                                </select>
                                @error('profit_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Profit Percentage') }} *</label>
                            <div class="col-sm-10">
                                <input type="number" step="any" class="form-control"
                                    value="{{ old('profit_percentage') ?? ($salary?->profit_percentage ?? 0) }}"
                                    placeholder="{{ _t('Profit Percentage') }}" name="profit_percentage" />
                                @error('profit_percentage')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Profit Type') }} *</label>
                            <div class="col-sm-10">
                                <select id="profit_type" name="profit_type" class="profit_type_select2 form-select">
                                    @foreach (App\Enums\ProfitTypeEnum::cases() as $type)
                                        <option value="{{ $type->value }}"
                                            {{ $salary?->type == $type ? 'selected' : '' }}>
                                            {{ _t(strtolower(str_replace('_', ' ', $type->name))) }}</option>
                                    @endforeach
                                </select>
                                @error('profit_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Monthly Target') }} *</label>
                            <div class="col-sm-10">
                                <input type="number" step="any" class="form-control"
                                    value="{{ old('target') ?? ($salary?->target ?? 0) }}"
                                    placeholder="{{ _t('Monthly Target') }}" name="target" />
                                @error('target')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Start Date') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control"
                                    value="{{ old('start_date') ?? ($salary?->start_date ?? now()->format('Y-m-d')) }}"
                                    name="start_date" />
                                @error('start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="row mb-3">
                                                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('End Date') }}</label>
                                                            <div class="col-sm-10">
                                                                <input type="date" class="form-control" value="{{ old('end') }}" name="end_date" />
                                                                @error('end_date')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                                                            </div>
                                                        </div> -->
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
