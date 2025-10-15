@extends('layouts/layoutMaster')

@section('title', 'Vacation List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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

@section('page-script')
    <script>
        $(document).ready(function() {
            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }
            $('.type_select2').select2({});
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

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var employeeId = getUrlParameter('employee_id');
            var vacationType = getUrlParameter('type');
            var date = getUrlParameter('date');
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
                    url: "{{ route('vacation.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employeeId;
                        d.type = vacationType;
                        d.date = date;
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
                        name: 'employee.user.name',
                        orderable: true,
                        searchable: true,
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
                        data: 'start_date',
                        name: 'start_date',
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                    },
                    {
                        data: 'from_hour',
                        name: 'from_hour',
                    },
                    {
                        data: 'to_hour',
                        name: 'to_hour',
                    },
                    {
                        data: 'days',
                        name: 'days',
                    },
                    {
                        data: 'type_name',
                        name: 'type_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_name',
                        name: 'status_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee.used_vacation_days',
                        name: 'employee.used_vacation_days',
                    },
                    {
                        data: 'employee.remaining_vacation_days',
                        name: 'employee.remaining_vacation_days',
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                    },
                    {
                        data: 'reject_reason',
                        name: 'reject_reason',
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.file) {
                                var file_output =
                                    `<a target="_blank" href="${full.file}" class="text-body text-truncate">
                  <i class="fa-solid fa-file"></i>
                  </a>`
                            } else {
                                return ''
                            }
                            return file_output;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'approved_at',
                        name: 'approved_at',
                    },
                    {
                        // ' + route('vacation.update_status', {
                        //       'id': data.id,
                        //       'status': 'DECLINED'
                        //     }) + '
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            let actions = '';
                            if (data.status == 1) {
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'APPROVED'
                                        }) +
                                    '"><i class="fa-solid fa-circle-check"></i> {{ _t('APPROVE') }}</a>';
                                actions +=
                                    '<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#declineVacationModal" data-id="' +
                                    data.id +
                                    '" href="#"><i class="fa-solid fa-circle-xmark"></i> {{ _t('DECLINE') }}</a>';
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'CANCELLED'
                                        }) +
                                    '"><i class="fa-solid fa-circle-xmark"></i> {{ _t('CANCEL') }}</a>';
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'PENDING_REPORT'
                                        }) +
                                    '"><i class="fa-solid fa-file-circle-xmark"></i> {{ _t('Missing Report') }}</a>';
                            } else if (data.status == 2) {
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'CANCELLED'
                                        }) +
                                    '"><i class="fa-solid fa-circle-xmark"></i> {{ _t('CANCEL') }}</a>';
                            } else if (data.status == 4) {
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'APPROVED'
                                        }) +
                                    '"><i class="fa-solid fa-circle-check"></i> {{ _t('APPROVE') }}</a>';
                                actions +=
                                    '<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#declineVacationModal" data-id="' +
                                    data.id +
                                    '" href="#"><i class="fa-solid fa-circle-xmark"></i> {{ _t('DECLINE') }}</a>';
                                actions += '<a class="dropdown-item" href="' + route(
                                        'vacation.update_status', {
                                            'id': data.id,
                                            'status': 'CANCELLED'
                                        }) +
                                    '"><i class="fa-solid fa-circle-xmark"></i> {{ _t('CANCEL') }}</a>';
                            }
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-id="' +
                                data.id +
                                '" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                actions +
                                '</div>' +
                                '</div>'
                            );
                        }
                    }
                ],
                dom: `
                <"card-header border-bottom p-3 d-flex justify-content-between align-items-center"
                    <"head-label"><"dt-action-buttons"B>
                >
                <"d-flex justify-content-between align-items-center mx-0 row"
                    <"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"fr>
                >
                <"table-responsive"t>
                <"d-flex justify-content-between mx-0 row"
                    <"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>
                >
                `,
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
            $('.type_select2').change(function() {
                vacationType = $(this).val();
                dataTable.ajax.reload();
            });
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });

            $('#declineVacationModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var vacation_id = button.data('id');
                var modal = $(this);
                modal.find('#form-decline').prop('action', route('vacation.update_status', {
                    'id': vacation_id
                }))
            });
        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Vacations') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-3">
                    <a href="{{ route('vacation.create') }}" class="btn btn-primary mb-3"
                        id="newUserButton">{{ _t('New Vacation') }}</a>
                </div>
                <div class="col-md-3">
                    <select id="typeFilter" name="type" class="type_select2 form-select" data-allow-clear="true" required>
                        <option value="" selecte>{{ _t('All Types') }}</option>
                        @foreach (App\Enums\VacationTypeEnum::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control datepicker" value="{{ old('date') }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                </div>
                <div class="col-md-3">
                    <select id="employeeFilter" name="employee_id" class="employee_select2 form-select"
                        data-allow-clear="true" required>
                    </select>
                </div>
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
                        </th>
                        <th>{{ _t('Start Date') }}</th>
                        <th>
                            {{ _t('End Date') }}
                        </th>
                        <th>
                            {{ _t('From Hour') }}
                        </th>
                        <th>
                            {{ _t('To Hour') }}
                        </th>
                        <th>{{ _t('Days') }}</th>
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Previews Vacations') }}</th>
                        <th>{{ _t('Remaining Vacations') }}</th>
                        <th>{{ _t('Reason') }}</th>
                        <th>{{ _t('Rejecting Reason') }}</th>
                        <th>{{ _t('File') }}</th>
                        <th>{{ _t('Created At') }}</th>
                        <th>{{ _t('Approved At') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="modal-onboarding modal fade animate__animated" id="declineVacationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Decline Vacation') }}</h4>
                        <form method="get" id="form-decline" action="" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="DECLINED" name="status" id="status" />
                            <div class=" row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Reject Reason') }}</label>
                                <div class="col-sm-10">
                                    <textarea rows="3" name="reject_reason" id="reject_reason" class="form-control"
                                        placeholder="{{ _t('Reject Reason') }}" required>{{ old('reject_reason') }}</textarea>
                                    @error('reject_reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
@endsection
