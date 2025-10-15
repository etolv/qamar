@extends('layouts/layoutMaster')

@section('title', 'New Shift')

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
            $('.status_select2').change(function() {
                status = $(this).val();
            });
            $('.type_select2').select2({});
            $('.holiday_select2').select2({});
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
            // const flatpickrRange = document.querySelector('#flatpickr-range'),
            const flatpickrTimeStart = document.querySelector('#flatpickr-time-start'),
                flatpickrTimeEnd = document.querySelector('#flatpickr-time-end'),
                flatpickrTimeStartBreak = document.querySelector('#flatpickr-time-start-break'),
                flatpickrTimeEndBreak = document.querySelector('#flatpickr-time-end-break');
            // if (typeof flatpickrRange != undefined) {
            //     flatpickrRange.flatpickr({
            //         mode: 'range'
            //     });
            // }
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
            if (flatpickrTimeStartBreak) {
                flatpickrTimeStartBreak.flatpickr({
                    enableTime: true,
                    noCalendar: true
                });
            }
            if (flatpickrTimeEndBreak) {
                flatpickrTimeEndBreak.flatpickr({
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
                    <form method="post" action="{{ route('shift.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Title') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="text" name="name" value="{{ old('name') ?? $shift?->name }}" required
                                        class="form-control" id="basic-icon-default-fullname"
                                        placeholder="{{ _t('Title') }}" aria-label="{{ _t('Title') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('name')
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
                                        <select id="type" name="type" class="type_select2 form-select"
                                            data-allow-clear="true" required>
                                            @foreach (App\Enums\ShiftTypeEnum::cases() as $type)
                                                <option value="{{ $type->value }}"
                                                    {{ old('type') == $type->value ? 'selected' : ($shift?->type == $type ? 'selected' : '') }}>
                                                    {{ _t($type->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Holiday')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="holiday" name="holiday" class="holiday_select2 form-select" data-allow-clear="true" required>
                                        @foreach (App\Enums\WeekDaysEnum::cases() as $day)
                                        <option value="{{$day->value}}" {{old('holiday') == $day->value ? 'selected' : ($shift?->holiday == $day ? 'selected' : '')}}>{{_t($day->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('holiday')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div> --}}
                        @php
                            $date_range = null;
                            if ($shift) {
                                $date_range = $shift->start . ' to ' . $shift->end;
                            }
                        @endphp
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Date') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('date') ?? $date_range }}"
                                    name="date" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                                @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Start Hour') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ old('start_time') ?? $shift?->start_time }}" name="start_time"
                                    placeholder="HH:MM" id="flatpickr-time-start" />
                                @error('start_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('End Hour') }}
                                *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ old('end_time') ?? $shift?->end_time }}" name="end_time" placeholder="HH:MM"
                                    id="flatpickr-time-end" />
                                @error('end_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Start Break') }}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ old('start_break') ?? $shift?->start_break }}" name="start_break"
                                    placeholder="HH:MM" id="flatpickr-time-start-break" />
                                @error('start_break')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('End Break') }}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ old('end_break') ?? $shift?->end_break }}" name="end_break"
                                    placeholder="HH:MM" id="flatpickr-time-end-break" />
                                @error('end_break')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Employees') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employees" name="employees[]" class="employee_select2 form-select"
                                            data-allow-clear="true" multiple>
                                            @if ($shift)
                                                @foreach ($shift?->employees as $employee)
                                                    <option value="{{ $employee->id }}" selected>
                                                        {{ $employee->user->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('employees')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
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
