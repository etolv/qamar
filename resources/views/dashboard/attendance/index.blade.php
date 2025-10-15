@extends('layouts/layoutMaster')

@section('title', 'Attendance List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/fixedColumns.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>
    <script type="module" src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.colVis.min.js"></script>
@endsection

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            window.deleteOptions('Attendance');
            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }
            $('.overtime_status_select2').select2({});
            $('.employee_select2').select2({
                placeholder: '{{ _t('Filter Employee') }}',
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
            $('.shift_select2').select2({
                placeholder: '{{ _t('Filter Shift') }}',
                ajax: {
                    url: route('shift.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name + ' - ' + item.start_time + " to " + item
                                        .end_time
                                };
                            })
                        };
                    },
                    cache: false
                }
            });

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var employeeId = getUrlParameter('employee_id');
            var shiftId = getUrlParameter('employee_id');
            var date = getUrlParameter('date');
            var overtime = getUrlParameter('overtime');
            if (overtime) {
                $('.page-title').text("{{ _t('Attendance List - Overtime') }}");
            }
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var dataTable = $('.datatables-users-test').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/{{ session()->get('locale') ?? app()->getLocale() }}.json" // Arabic language file
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('attendance.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employeeId;
                        d.date = date;
                        d.overtime = overtime;
                        d.shift_id = shiftId;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.employee) {
                                var name = full.employee.user.name,
                                    image = full.employee_image ||
                                    "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                    number = full.employee.user.phone,
                                    baseUrl = route('employee.show', full.employee.id);
                                var row_output =
                                    '<div class="d-flex justify-content-start align-items-center">' +
                                    '<div class="avatar-wrapper">' +
                                    '<div class="avatar avatar-sm me-2">' +
                                    '<img src="' + image +
                                    '" alt="Avatar" class="rounded-circle">' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="d-flex flex-column">' +
                                    '<a href="' +
                                    baseUrl +
                                    '" class="text-body text-truncate"><span class="fw-semibold">' +
                                    name +
                                    '</span></a>' +
                                    '<small class="text-truncate text-muted">' +
                                    number +
                                    '</small>' +
                                    '</div>' +
                                    '</div>';
                            } else {
                                return '{{ _t('Not assigned') }}'
                            }
                            return row_output;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.shift) {
                                var name = full.shift.name,
                                    periodTime = full.shift.start_time + " to " + full.shift
                                    .end_time,
                                    baseUrl = route('shift.show', full.shift.id);
                                var row_output =
                                    '<div class="d-flex justify-content-start align-items-center">' +
                                    '<div class="d-flex flex-column">' +
                                    '<a href="' +
                                    baseUrl +
                                    '" class="text-body text-truncate"><span class="fw-semibold">' +
                                    name +
                                    '</span></a>' +
                                    '<small class="text-truncate text-muted">' +
                                    periodTime +
                                    '</small>' +
                                    '</div>' +
                                    '</div>';
                            } else {
                                return '{{ _t('Not assigned') }}'
                            }
                            return row_output;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var type = data.is_holiday == 1 ? '{{ _t('HOLIDAY') }}' :
                                '{{ _t('NORMAL') }}';
                            var color = data.is_holiday == 1 ? 'warning' : 'primary';
                            return (
                                `<span class="badge bg-label-${color}">` +
                                type + '</span>'
                            );
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var color = data.status == 1 ? 'success' : 'danger';
                            return (
                                `<span class="badge bg-label-${color}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ _t('Click to change status') }}">` +
                                data.status_name + '</span>'
                            );
                        }
                    },
                    {
                        data: 'date',
                        name: 'date',
                    },
                    {
                        data: 'start',
                        name: 'start',
                    },
                    {
                        data: 'end',
                        name: 'end',
                    },
                    {
                        data: null,
                        name: 'total',
                        render: function(data, type, row) {
                            let hours = Math.floor(data.total);
                            let minutes = Math.round((data.total - hours) * 60);
                            return `${hours}h ${minutes}m`;
                        }
                    },
                    {
                        data: null,
                        name: 'missing_hours',
                        render: function(data, type, row) {
                            let hours = Math.floor(data.missing_hours);
                            let minutes = Math.round((data.missing_hours - hours) * 60);
                            return `${hours}h ${minutes}m`;
                        }
                    },
                    {
                        data: null,
                        name: 'extra_hours',
                        render: function(data, type, row) {
                            let hours = Math.floor(data.extra_hours);
                            let minutes = Math.round((data.extra_hours - hours) * 60);
                            return `${hours}h ${minutes}m`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (data.overtime_status_name) {
                                var color = data.overtime_status == 1 ? 'warning' : (data
                                    .overtime_status == 2 ? 'success' : (data.overtime_status ==
                                        3 ? 'info' : 'danger'));
                                return (
                                    `<a href="javascript:;" class="text-body change-overtime-status" data-id="${data.id}" data-status="${data.overtime_status}" data-bs-toggle="modal" data-bs-target="#changeOvertimeStatusModal">
                <span class="badge bg-label-${color}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ _t('Click to change status') }}">` +
                                    data.overtime_status_name + '</span></a>'
                                );
                            } else {
                                return ('');
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('attendance.edit', data.id) +
                                '" class="text-body"><i class="fa-solid fa-pen-to-square me-2"></i></a>' +
                                // '<a href="' + route('shift.duplicate', data.id) + '" class="text-body"><i class="fa-solid fa-clone"></i></a>' +
                                `<a href="javascript:;" class="text-body item-delete" data-with-trashed="false" data-object-id="${data.id}"><i class="ti ti-trash ti-sm mx-2"></i></a>` +
                                // '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                // '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                // '<a href="' + route('order.rate', data.id) + '" class="dropdown-item"><i class="fa-regular fa-star"></i> {{ _t('Rate') }}</a>' +
                                // completeAction +
                                // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                                // '</div>' +
                                '</div>'
                            );
                        }
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
                    '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
                    '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row mb-1"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [{
                    extend: 'colvis',
                    text: "{{ _t('Column Visibility') }}"
                }, {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
                        // {
                        //     extend: 'csv',
                        //     text: 'Excel',
                        //     className: 'dropdown-item',
                        // },
                        // {
                        //     extend: 'pdf',
                        //     text: 'PDF',
                        //     className: 'dropdown-item',
                        // },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            className: 'dropdown-item',
                        }
                    ],
                    init: function(api, node, config) {
                        $(node).removeClass('btn-secondary');
                        $(node).parent().removeClass('btn-group');
                        setTimeout(function() {
                            $(node).closest('.dt-buttons').removeClass('btn-group')
                                .addClass('d-inline-flex mt-50');
                        }, 50);
                    }
                }],
                // order: [9, 'asc']
            });

            $('.employee_select2').change(function() {
                employeeId = $(this).val();
                dataTable.ajax.reload();
            });
            $('.shift_select2').change(function() {
                shiftId = $(this).val();
                dataTable.ajax.reload();
            });
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });

            $('#changeOvertimeStatusModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var attendance_id = button.data('id');
                var status = button.data('status');
                if (status != 1) {
                    event.preventDefault();
                }
                $('#overtime_status').val(status).trigger('change');
                var url = route('attendance.update', attendance_id);
                var modal = $(this);
                modal.find('#form-status').attr('action', url);
            });

            $('#file').change(function(event) {
                const allowedExtensions = ['.xls', '.xlsx'];
                const file = event.target.files[0];
                const fileName = file.name;
                const fileExtension = fileName.slice((Math.max(0, fileName.lastIndexOf(".")) || Infinity) +
                    1).toLowerCase();
                if (!allowedExtensions.includes('.' + fileExtension)) {
                    alert('Only Excel files (.xls, .xlsx) are allowed.');
                    event.target.value = '';
                }
            });

        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3 page-title">{{ _t('Attendance') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                @can('create_attendance')
                    <div class="col-md-3 justify-content-start">
                        <a href="javascript:;" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#importDataModal">{{ _t('Import') }}</a>
                    </div>
                    <div class="col-md-3 justify-content-end">

                        <a href="{{ route('attendance.create') }}" class="btn btn-primary mb-3"
                            id="newUserButton">{{ _t('New Attendance') }}</a>
                    </div>
                @endcan
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-users-test table dt-responsive">
                <thead class="border-top">
                    <tr>
                        <!-- Filter -->
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>
                            {{ _t('Employee') }}
                            <select id="employeeFilter" name="employee_id" class="employee_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
                        <th>
                            {{ _t('Shift') }}
                            <select id="shiftFilter" name="shift_id" class="shift_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>
                            {{ _t('Date') }}
                            <input type="text" class="form-control datepicker" value="{{ old('date') }}"
                                name="date" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                        </th>
                        <th>{{ _t('Start') }}</th>
                        <th>{{ _t('End') }}</th>
                        <th>{{ _t('Total') }}</th>
                        <th>{{ _t('Missing Hours') }}</th>
                        <th>{{ _t('Extra Hours') }}</th>
                        <th>{{ _t('Overtime Status') }}</th>
                        <th>{{ _t('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal-onboarding modal fade animate__animated" id="changeOvertimeStatusModal" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Edit Service Employee') }}</h4>
                        <form method="POST" action="#" enctype="multipart/form-data" id="form-status">
                            @csrf
                            @method('PUT')
                            <div class=" row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Employee') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                                        <div class="col-sm-10">
                                            <select id="overtime_status" name="overtime_status"
                                                class="overtime_status_select2 form-select" data-allow-clear="false"
                                                required>
                                                @foreach (App\Enums\OverTimeStatusEnum::cases() as $status)
                                                    <option value="{{ $status->value }}">{{ _t($status->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('overtime_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">{{ _t('Update') }}</button>
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
    <div class="modal-onboarding modal fade animate__animated" id="importDataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('New category') }}</h4>
                        <form method="POST" action="{{ route('attendance.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('File') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-image"></i></span>
                                        <input type="file" name="file" value="{{ old('File') }}" id="file"
                                            accept=".xls,.xlsx" class="form-control"
                                            aria-describedby="basic-icon-default-email2" required />
                                    </div>
                                    @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">{{ _t('Store') }}</button>
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
@endsection
