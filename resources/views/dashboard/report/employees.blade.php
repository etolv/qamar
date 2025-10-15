@extends('layouts/layoutMaster')

@section('title', 'Employees Report')

@section('vendor-style')

    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')

    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }
            let role_id = null;
            let job_id = null;
            let name = null;
            let phone = null;
            let email = null;
            let date = null;
            let nationality_id = null;
            $('.job_select2').select2({
                placeholder: '{{ _t('Filter Product') }}',
                ajax: {
                    url: route('job.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.title + ' - ' + item.section_name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.nationality_select2').select2({
                placeholder: '{{ _t('Filter Nationality') }}',
                ajax: {
                    url: route('nationality.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name + ' - ' + item.country
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.role_select2').select2({
                placeholder: '{{ _t('Filter Role') }}',
                ajax: {
                    url: route('role.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
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
            var supplierId = getUrlParameter('supplier_id');
            var department = getUrlParameter('department');
            // window.deleteOptions('Stock');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var dataTable = $('.datatables-users-test').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: `https://cdn.datatables.net/plug-ins/1.11.5/i18n/${current_system_locale}.json`
                },
                processing: true,
                serverSide: true,
                paging: false, // pagination
                searching: false, // search box
                ajax: {
                    url: "{{ route('report.employee.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.role_id = role_id;
                        d.job_id = job_id;
                        d.name = name;
                        d.phone = phone;
                        d.email = email;
                        d.date = date;
                        d.nationality_id = nationality_id;
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
                            var profileImage = full.profile_image ||
                                "{{ asset('assets/img/illustrations/NoImage.png') }}"; // Replace 'default.jpg' with your default image path
                            return '<img src="' + profileImage +
                                '" alt="Profile Image" class="rounded-circle" style="width: 30px; height: 30px;">';
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'user.email',
                        name: 'user.email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'job.title',
                        name: 'job.title',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nationality.name',
                        name: 'nationality.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'vacation_days',
                        name: 'vacation_days',
                    },
                    {
                        data: 'taken_vacations',
                        name: 'taken_vacations',
                    },
                    {
                        data: 'remaining_vacation_days',
                        name: 'remaining_vacation_days',
                    },
                    {
                        data: 'holiday',
                        name: 'holiday',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.base_salary',
                        name: 'salary_data.base_salary',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.working_hours',
                        name: 'salary_data.working_hours',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.missing_hours',
                        name: 'salary_data.missing_hours',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.extra_hours',
                        name: 'salary_data.extra_hours',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.target',
                        name: 'salary_data.target',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.target_total',
                        name: 'salary_data.target_total',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.profit_percentage',
                        name: 'salary_data.profit_percentage',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.profit_total',
                        name: 'salary_data.profit_total',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.overtime',
                        name: 'salary_data.overtime',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.gift',
                        name: 'salary_data.gift',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.advance',
                        name: 'salary_data.advance',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.total_extra',
                        name: 'salary_data.total_extra',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'salary_data.total_deduction',
                        name: 'salary_data.total_deduction',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false
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
                        {
                            extend: 'csv',
                            text: 'CSV',
                            className: 'dropdown-item',
                        },
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
                }, ],
            });

            // Add index column
            dataTable.on('order.dt search.dt', function() {
                dataTable.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            $('.role_select2').change(function() {
                role_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.job_select2').change(function() {
                job_id = $(this).val();
                dataTable.ajax.reload();
            });

            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });
            $('.name').on('input', function() {
                name = $(this).val();
                dataTable.ajax.reload();
            });
            $('.email').on('input', function() {
                email = $(this).val();
                dataTable.ajax.reload();
            });
            $('.phone').on('input', function() {
                phone = $(this).val();
                dataTable.ajax.reload();
            });
            $('.nationality_select2').change(function() {
                nationality_id = $(this).val();
                dataTable.ajax.reload();
            });
        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Employees') }}</h5>
            <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ _t('CSV only support english characters use Excel for arabic') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Date Range') }}</label>
                    <input type="text" class="form-control datepicker" value="{{ old('date') ?? $date }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Name') }}</label>
                    <input type="text" id="name" name="name" class="mt-2 name form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Phone') }}</label>
                    <input type="text" id="phone" name="phone" class="mt-2 phone form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Email') }}</label>
                    <input type="text" id="email" name="email" class="mt-2 email form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Role') }}</label>
                    <select id="roleFilter" name="role_id" class="role_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Job') }}</label>
                    <select id="jobFilter" name="job_id" class="job_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Nationality') }}</label>
                    <select id="nationalityFilter" name="nationality_id" class="nationality_select2 form-select"
                        data-allow-clear="true">
                    </select>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-users-test table"> {{-- add class 'dt-responsive' for responsive --}}
                <thead class="border-top">
                    <tr>
                        <!-- Filter -->
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>{{ _t('Image') }}</th>
                        <th>{{ _t('Name') }}</th>
                        <th>{{ _t('Phone') }}</th>
                        <th>{{ _t('Email') }}</th>
                        <th>{{ _t('Role') }}</th>
                        <th>{{ _t('Job') }}</th>
                        <th>{{ _t('Nationality') }}</th>
                        <th>{{ _t('Total Vacations') }}</th>
                        <th>{{ _t('Used Vacations') }}</th>
                        <th>{{ _t('Total Left Vacations') }}</th>
                        <th>{{ _t('Holiday') }}</th>
                        <th>{{ _t('Base Salary') }}</th>
                        <th>{{ _t('Working Hours') }}</th>
                        <th>{{ _t('Missing Hours') }}</th>
                        <th>{{ _t('Extra Hours') }}</th>
                        <th>{{ _t('Employee Target') }}</th>
                        <th>{{ _t('Target Reached') }}</th>
                        <th>{{ _t('Profit Percentage') }}</th>
                        <th>{{ _t('Profit Total') }}</th>
                        <th>{{ _t('Overtime') }}</th>
                        <th>{{ _t('Gift') }}</th>
                        <th>{{ _t('Advance') }}</th>
                        <th>{{ _t('Total Extra') }}</th>
                        <th>{{ _t('Total Deduction') }}</th>
                        <th>{{ _t('Created At') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--  -->

@endsection
