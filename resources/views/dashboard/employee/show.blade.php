@extends('layouts/layoutMaster')

@section('title', 'User View - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-user-view.js', 'resources/assets/js/app-user-view-account.js'])
    <script>
        $(document).ready(function() {
            let employee_id = "{{ $employee->id }}";
            let hourly_rate = "{{ $hourly_rate }}";
            let days = $('#days').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var dataTable = $('.datatables-vacations').DataTable({
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
                        d.employee_id = employee_id;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
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
                        data: 'days',
                        name: 'days',
                    },
                    {
                        data: 'reason',
                        name: 'reason',
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'csv',
                            text: 'Excel',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'excel',
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
            var dataTable_salaries = $('.datatables-salaries').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/{{ session()->get('locale') ?? app()->getLocale() }}.json" // Arabic language file
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('salary.generated.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employee_id;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'month_name',
                        name: 'month_name',
                    },
                    {
                        data: 'base_salary',
                        name: 'base_salary',
                    },
                    {
                        data: 'working_hours',
                        name: 'working_hours',
                    },
                    {
                        data: 'missing_hours',
                        name: 'missing_hours',
                    },
                    {
                        data: 'extra_hours',
                        name: 'extra_hours',
                    },
                    {
                        data: 'rounded_hours',
                        name: 'rounded_hours',
                    },
                    {
                        data: 'total',
                        name: 'total',
                    },
                    {
                        data: 'total_extra',
                        name: 'total_extra',
                    },
                    {
                        data: 'total_deduction',
                        name: 'total_deduction',
                    },
                    {
                        data: 'target',
                        name: 'target',
                    },
                    {
                        data: 'target_total',
                        name: 'target_total',
                    },
                    {
                        data: 'profit_percentage',
                        name: 'profit_percentage',
                    },
                    {
                        data: 'profit_total',
                        name: 'profit_total',
                    },
                    {
                        data: 'overtime',
                        name: 'overtime',
                    },
                    {
                        data: 'gift',
                        name: 'gift',
                    },
                    {
                        data: 'advance',
                        name: 'advance',
                    },
                    {
                        data: 'deduction',
                        name: 'deduction',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'csv',
                            text: 'Excel',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'excel',
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
            var attendance_dataTable = $('.datatables-attendance').DataTable({
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
                        d.employee_id = employee_id;
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
                        data: 'total',
                        name: 'total',
                    },
                    {
                        data: 'missing_hours',
                        name: 'missing_hours',
                    },
                    {
                        data: 'extra_hours',
                        name: 'extra_hours',
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
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
            var dataTable_custody = $('.datatables-custody').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: `https://cdn.datatables.net/plug-ins/1.11.5/i18n/${current_system_locale}.json`
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('custody.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employee_id;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var name = full.stock.product.name,
                                image = full.stock.product_image ||
                                "{{ asset('assets/img/illustrations/default.png') }}",
                                sku = full.stock.product.sku,
                                baseUrl = route('product.edit', full.stock.product_id);
                            var row_output =
                                '<div class="d-flex justify-content-start align-items-center">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar avatar-sm me-2">' +
                                '<img src="' + image + '" alt="Avatar" class="rounded-circle">' +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<a href="' +
                                baseUrl +
                                '" class="text-body text-truncate"><span class="fw-semibold">' +
                                name +
                                '</span></a>' +
                                '<small class="text-truncate text-muted">' +
                                sku +
                                '</small>' +
                                '</div>' +
                                '</div>';
                            return row_output;
                        }
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'status_text',
                        name: 'status_text'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
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
                }, ],
            });
            var shift_dataTable = $('.datatables-shift').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/{{ session()->get('locale') ?? app()->getLocale() }}.json" // Arabic language file
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employee-shift.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employee_id;
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
                            var name = full.shift.name,
                                baseUrl = route('shift.show', full.shift_id),
                                number = full.shift.start_time + " to " + full.shift.end_time;
                            var row_output =
                                '<div class="d-flex justify-content-start align-items-center">' +
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
                            return row_output;
                        }
                    },
                    {
                        orderable: true,
                        searchable: true,
                        data: 'date',
                        name: 'date'
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'created_at',
                        name: 'created_at'
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
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: "{{ _t(' Export ') }}",
                    buttons: [{
                            extend: 'print',
                            text: "{{ _t(' Print ') }}",
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'csv',
                            text: 'Excel',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'excel',
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

            $('.type_select2').change(function() {
                updateCashedAmount();
            });
            $('#days').change(function() {
                days = $(this).val();
                updateCashedAmount();
            });

            function updateCashedAmount() {
                let show = $('.type_select2').val() == 8 ? true : false;
                let amount = days * hourly_rate * 8;
                console.log(show, amount);
                $('#cashed_amount').text(amount + " {{ _t('SAR') }}");
                if (show) {
                    $('#cashed_amount').removeClass('d-none');
                } else {
                    $('#cashed_amount').addClass('d-none');
                }
            }

        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ _t('Employees') }} /
            {{ _t('View') }}</span>
    </h4>
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            <img class="img-fluid rounded mb-3 pt-1 mt-4"
                                src="{{ $employee->user->getFirstMedia('profile') ? $employee->user->getFirstMedia('profile')->getUrl() : asset('assets/img/illustrations/NoImage.png') }}"
                                height="100" width="100" alt="User avatar" />
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $employee->user->name }}</h4>
                                <span class="badge bg-label-secondary mt-1">{{ $employee->job->title }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 small text-uppercase text-muted">{{ _t('Details') }}</p>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-medium me-1">{{ _t('Name') }}:</span>
                                <span>{{ $employee->user->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Email') }}:</span>
                                <span>{{ $employee->user->email }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Phone') }}:</span>
                                <span>{{ $employee->user->phone }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Status') }}:</span>
                                <span
                                    class="badge bg-label-{{ $employee->user->deleted_at ? 'danger' : 'success' }}">{{ $employee->user->deleted_at ? _t('Archived') : _t('Active') }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Rule') }}:</span>
                                <span>{{ $employee->job->title }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Nationality') }}:</span>
                                <span>{{ $employee->nationality->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Annual Vacations') }}:</span>
                                <span>{{ $employee->vacation_days }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Taken Vacations') }}:</span>
                                <span>{{ $employee->used_vacation_days }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Left Vacations') }}:</span>
                                <span>{{ $employee->remaining_vacation_days }}</span>
                            </li>
                        </ul>
                        @can('update_employee')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-start">
                                        <a href="{{ route('employee.edit', $employee->id) }}"
                                            class="btn btn-primary me-3">{{ _t('Edit') }}</a>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#roundVacationModal">
                                            {{ _t('Round Vacations') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @if ($authenticated_user->type_id == $employee->id)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <a href="{{ route('vacation.create', ['employee_id' => $employee->id]) }}"
                                        class="btn btn-primary me-3">{{ _t('Request Vacation') }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-2">{{ _t('Information') }}</h4>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            @foreach ($employee->employeeInfos as $info)
                                <li class="mb-2">
                                    <span class="fw-medium me-1">{{ $info->name }}:</span>
                                    <span>{{ $info->value }}</span>
                                    @if ($info->getFirstMedia('file'))
                                        <a href="{{ $info->getFirstMediaUrl('file') }}" download>
                                            <i class="fa-solid fa-file"></i></a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
            <!-- Plan Card -->
            {{-- <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="badge bg-label-primary">Standard</span>
                        <div class="d-flex justify-content-center">
                            <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary fw-normal">$</sup>
                            <h1 class="mb-0 text-primary">99</h1>
                            <sub class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                        </div>
                    </div>
                    <ul class="ps-3 g-2 my-3">
                        <li class="mb-2">10 Users</li>
                        <li class="mb-2">Up to 10 GB storage</li>
                        <li>Basic Support</li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mb-1 fw-medium text-heading">
                        <span>Days</span>
                        <span>65% Completed</span>
                    </div>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span>4 days remaining</span>
                    <div class="d-grid w-100 mt-4">
                        <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">Upgrade
                            Plan</button>
                    </div>
                </div>
            </div> --}}
            <!-- /Plan Card -->
        </div>
        <!--/ User Sidebar -->


        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Pills -->
            {{-- <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
                            class="ti ti-user-check ti-xs me-1"></i>Account</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/security') }}"><i
                            class="ti ti-lock ti-xs me-1"></i>Security</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/billing') }}"><i
                            class="ti ti-currency-dollar ti-xs me-1"></i>Billing & Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/notifications') }}"><i
                            class="ti ti-bell ti-xs me-1"></i>Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/connections') }}"><i
                            class="ti ti-link ti-xs me-1"></i>Connections</a></li>
            </ul> --}}
            <!--/ User Pills -->

            <!-- Project table -->
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Vacations') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatables-vacations border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    {{ _t('Start Date') }}
                                </th>
                                <th>
                                    {{ _t('End Date') }}
                                </th>
                                <th>{{ _t('Days') }}</th>
                                <th>{{ _t('Reason') }}</th>
                                <th>
                                    {{ _t('Type') }}
                                </th>
                                <th>{{ _t('Status') }}</th>
                                <th>{{ _t('Rejecting Reason') }}</th>
                                <th>{{ _t('File') }}</th>
                                <th>{{ _t('Created At') }}</th>
                                <th>{{ _t('Approved At') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /Project table -->

            <!-- shift table -->
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Shifts') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-shift table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <!-- Filter -->
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Shift') }}</th>
                                <th>{{ _t('Day') }} </th>
                                <th>{{ _t('Created') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /shift table -->


            {{-- attendances --}}
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Attendance') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatables-attendance border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Employee') }}</th>
                                <th>{{ _t('Shift') }} </th>
                                <th>{{ _t('Type') }}</th>
                                <th>{{ _t('Status') }}</th>
                                <th>{{ _t('Date') }}</th>
                                <th>{{ _t('Start') }}</th>
                                <th>{{ _t('End') }}</th>
                                <th>{{ _t('Total') }}</th>
                                <th>{{ _t('Missing Hours') }}</th>
                                <th>{{ _t('Extra Hours') }}</th>
                                <th>{{ _t('Overtime Status') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            {{-- /attendances --}}

            <!-- salaries table -->
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Salaries') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatables-salaries border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Month') }}</th>
                                <th>{{ _t('Base Salary') }}</th>
                                <th>{{ _t('Working Hours') }}</th>
                                <th>{{ _t('Missing Hours') }}</th>
                                <th>{{ _t('Extra Hours') }}</th>
                                <th>{{ _t('Rounded Hours') }}</th>
                                <th>{{ _t('Total Paid') }}</th>
                                <th>{{ _t('Total Extra') }}</th>
                                <th>{{ _t('Total Deductions') }}</th>
                                <th>{{ _t('Target') }}</th>
                                <th>{{ _t('Target Total') }}</th>
                                <th>{{ _t('Profit Percentage') }}</th>
                                <th>{{ _t('Profit') }}</th>
                                <th>{{ _t('Overtime') }}</th>
                                <th>{{ _t('Gifts') }}</th>
                                <th>{{ _t('Advance') }}</th>
                                <th>{{ _t('Deductions') }}</th>
                                <th>{{ _t('Created At') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /salaries table -->

            <!-- custodies table -->
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Custodies') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-custody table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <!-- Filter -->
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Product') }}</th>
                                <th>{{ _t('Qty') }}</th>
                                <th>{{ _t('Price') }}</th>
                                <th>{{ _t('status') }}</th>
                                <th>{{ _t('Created at') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /custodies table -->
        </div>
        <!--/ User Content -->
    </div>

    <!-- Modal -->
    <div class="modal-onboarding modal fade animate__animated" id="roundVacationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Round Vacations') }}</h4>
                        <form method="POST" action="{{ route('vacation.round', $employee->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Days') }}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="1" name="days" id="days"
                                        class="form-control" step="any"
                                        max="{{ $employee->remaining_vacation_days }}"
                                        placeholder="{{ _t('Days') }}"
                                        value="{{ $employee->remaining_vacation_days }}" />
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Rounding Type') }}</label>
                                <div class="col-sm-10">
                                    <select id="type" name="type" class="type_select2 form-select"
                                        data-allow-clear="true">
                                        <option value="{{ App\Enums\VacationTypeEnum::ROUNDED->value }}">
                                            {{ _t('Round Vacations') }}</option>
                                        <option value="{{ App\Enums\VacationTypeEnum::CASHED->value }}">
                                            {{ _t('Cash Vacations') }}</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <span class="fw-bold d-none" id="cashed_amount"></span>
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
    <!-- end modal -->
@endsection
