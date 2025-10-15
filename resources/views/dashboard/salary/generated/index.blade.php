@extends('layouts/layoutMaster')

@section('title', 'Salary List')

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
        // Echo.private('new-order')
        //   .listen('NewOrderEvent', (event) => {
        //     toastr.success("{{ _t('New order placed') }} #" + event.data.id);
        //     $('.datatables-users-test').DataTable().ajax.reload();
        //   });
        $(document).ready(function() {
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
                    url: "{{ route('salary.generated.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employeeId;
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
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                `<div class="d-flex align-items-center">
              <a href="${route('employee.show', data.employee_id)}" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>
              </div>`
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
                        //   extend: 'csv',
                        //   text: 'Excel',
                        //   className: 'dropdown-item',
                        // },
                        // {
                        //   extend: 'pdf',
                        //   text: 'PDF',
                        //   className: 'dropdown-item',
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
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });



        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Generated Salaries') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    @can('create_generated_salary')
                        <a href="{{ route('generated-salary.create') }}" class="btn btn-primary mb-3"
                            id="newUserButton">{{ _t('Generate Salaries') }}</a>
                    @endcan
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
                            <select id="employeeFilter" name="employee_id" class="employee_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
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
                        <th>{{ _t('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
