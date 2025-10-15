@extends('layouts/layoutMaster')

@section('title', 'Shift')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            const bsDatepickerMultidate = $('#bs-datepicker-multidate');
            if (bsDatepickerMultidate.length) {
                bsDatepickerMultidate.datepicker({
                    multidate: true,
                    todayHighlight: true,
                    orientation: isRtl ? 'auto right' : 'auto left'
                });
            }
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employees') }}',
                ajax: {
                    url: route('employee.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            // excluded_ids: @json($employee_ids) // your custom parameter
                        };
                    },
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
            $('.print-button').click(function(event) {
                event.preventDefault();
                window.print();
            })
        });
    </script>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

            <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{ _t('shift') }} #{{ $shift->id }}</h5>
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">
                    <span>{{ _t('Working Hours') }}:</span> <span
                        class="badge bg-label-info">{{ $shift->start_time . ' - ' . $shift->end_time }}</span>
                </h5>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-2">
                <!-- <p class="text-body"><a target="_blank" class="btn btn-primary" title="{{ _t('Download PDF') }}" href="{{ route('order.pdf', $shift->id) }}"><i class="fa-solid fa-file-pdf"></i></a></p> -->
                <!-- <p class="text-body"><a target="_blank" class="btn btn-warning" title="{{ _t('Rate') }}" href="{{ route('order.rate', $shift->id) }}"><i class="fa-regular fa-star"></i> </a></p> -->
                <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-3">
                            <h5 class="card-title m-0">{{ _t('Shift Employees') }}</h5>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-primary mb-3" id="newUserButton" data-bs-toggle="modal"
                                data-bs-target="#addEmployeeModal">{{ _t('Add Employees') }}</a>
                        </div>
                        <!-- <div class="col">
                                                                                                                                                                        <div class="mb-1 mt-1">
                                                                                                                                                                            <div class="col-sm-10">
                                                                                                                                                                                <label class="col-sm-2 col-form-label text-nowrap" for="status">{{ _t('Status') }}</label>
                                                                                                                                                                                <select id="status" name="status" class="status_select2 form-select" data-allow-clear="false">
                                                                                                                                                                                    @foreach ([] as $status)
    <option value="{{ $status->value }}" {{ $order->status == $status ? 'selected' : '' }}>{{ $status->name }}</option>
    @endforeach
                                                                                                                                                                                </select>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div> -->
                        <!-- <div class="col"> -->
                        <!-- <div class="mb-1 mt-1"> -->
                        <!-- <div class="col-sm-10"> -->
                        <!-- <label class="col-sm-2 col-form-label text-nowrap" for="status">{{ _t('Delivery') }}</label> -->
                        <!-- <select id="delivery" name="delivery" class="delivery_select2 form-select" data-allow-clear="false"> -->
                        <!-- @foreach ([] as $status)
    -->
                        <!-- <option value="{{ $status->value }}" {{ $order->delivery_status == $status ? 'selected' : '' }}>{{ $status->name }}</option> -->
                        <!--
    @endforeach -->
                        <!-- </select> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- </div> -->
                    </div>
                    <div class="card-datatable table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table class="datatables-order-details table border-top dataTable no-footer dtr-column"
                                id="DataTables_Table_0">
                                <thead>
                                    <tr>
                                        <th class="control sorting_disabled dtr-hidden" style="display: none;"
                                            aria-label=""></th>
                                        <th class="sorting_disabled" aria-label="image">
                                            {{ _t('Image') }}</th>
                                        <th class="sorting_disabled" aria-label="name">
                                            {{ _t('Name') }}</th>
                                        <th class="sorting_disabled" aria-label="phone">
                                            {{ _t('phone') }}</th>
                                        <th class="sorting_disabled text-nowrap" aria-label="email">
                                            {{ _t('Date') }}</th>
                                        <th class="sorting_disabled" aria-label="role">
                                            {{ _t('Role') }}</th>
                                        <th class="sorting_disabled" aria-label="role">
                                            {{ _t('job') }}</th>
                                        <th class="sorting_disabled" aria-label="role">
                                            {{ _t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($shift->employeeShifts as $employeeShift)
                                        <tr class="odd">
                                            <td class="  control" tabindex="0" style="display: none;"></td>
                                            <td class="sorting_1">
                                                <div class="d-flex justify-content-start align-items-center text-nowrap">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar me-2"><img
                                                                src="{{ $employeeShift->employee->user->getFirstMedia('profile') ? $employeeShift->employee->user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/default.png') }}"
                                                                alt="" class="rounded-2"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span>{{ $employeeShift->employee->user->name }}</span></td>
                                            <td><span>{{ $employeeShift->employee->user->phone }}</span></td>
                                            <td class="text-nowrap">
                                                {{ Carbon\Carbon::parse($employeeShift->date)->format('Y-m-d D') }}
                                            </td>
                                            <td><span>{{ $employeeShift->employee->user->roles()->first()?->name }}</span>
                                            </td>
                                            <td><span>{{ $employeeShift->employee->job->title }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('employee-shift.delete', $employeeShift->id) }}"
                                                        class="text-body delete-record"><i
                                                            class="ti ti-trash ti-sm mx-2"></i></a>
                                                    <a href="{{ route('employee-shift.edit', $employeeShift->id) }}"
                                                        class="text-body delete-record">
                                                        <i class="ti ti-edit ti-sm me-2"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-start align-items-center m-3 mb-2 p-1">
                            <div class="mt-3">
                                {{ $shift->employeeShifts->links() }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center m-3 mb-2 p-1">
                            <div class="order-calculations">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="w-px-100 text-heading">{{ _t('Total Employees') }}:</span>
                                    <h6 class="mb-0">{{ $shift->employees()->count() }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{ _t('Shift details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <h6 class="text-body text-nowrap mb-0">{{ _t('Type') }} : {{ _t($shift->type->name) }}
                            </h6>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <h6 class="text-body text-nowrap mb-0">{{ _t('Holiday') }} : {{ _t($shift->holiday->name) }}
                            </h6>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <h6 class="text-body text-nowrap mb-0">{{ _t('Working Hours') }} :
                                {{ $shift->start_time . ' - ' . $shift->end_time }}</h6>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <h6 class="text-body text-nowrap mb-0">{{ _t('Break Hours') }} :
                                {{ $shift->start_break . ' - ' . $shift->end_break }}</h6>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <h6 class="text-body text-nowrap mb-0">{{ _t('Working Hours') }} : {{ $shift->daily_hours }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <div class="modal-onboarding modal fade animate__animated" id="addEmployeeModal" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ _t('Add Employees') }}</h4>
                            <form method="POST" action="{{ route('employee-shift.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label"
                                        for="basic-icon-default-email">{{ _t('Days') }}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                            <div class="col-sm-10">
                                                <input type="text" name="days" id="bs-datepicker-multidate"
                                                    placeholder="MM/DD/YYYY, MM/DD/YYYY" class="form-control" />
                                            </div>
                                        </div>
                                        @error('days')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label"
                                        for="basic-icon-default-email">{{ _t('Employees') }}</label>
                                    <div class="col-sm-10">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="ti ti-user"></i></span>
                                            <div class="col-sm-10">
                                                <select id="employees" name="employees[]"
                                                    class="employee_select2 form-select" data-allow-clear="true" required
                                                    multiple>
                                                </select>
                                            </div>
                                        </div>
                                        @error('employees')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">{{ _t('Submit') }}</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
