@extends('layouts/layoutMaster')

@section('title', 'Employee List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
    <script src="{{ asset('assets/js/app-user-list.js') }}"></script>
    <script>
        $(document).ready(function() {
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var section = getUrlParameter('section');
            var nationality_id = getUrlParameter('nationality_id');
            let job_id = null;
            let role_id = getUrlParameter('role_id');
            window.deleteOptions('User');
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
            $('.job_select2').select2({
                placeholder: '{{ _t('Filter Job') }}',
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
            // Initialize DataTable
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
                    url: "{{ route('employee.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.job_id = job_id;
                        d.section = section;
                        d.role_id = role_id;
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type.job.title',
                        name: 'type.job.title',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type.nationality.name',
                        name: 'type.nationality.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'type.vacation_days',
                        name: 'type.vacation_days',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.used_vacation_days',
                        name: 'type.used_vacation_days',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.remaining_vacation_days',
                        name: 'type.remaining_vacation_days',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'holiday_name',
                        name: 'holiday_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type.birthday',
                        name: 'type.birthday',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.start_work',
                        name: 'type.start_work',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.residence_number',
                        name: 'type.residence_number',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.residence_expiration',
                        name: 'type.residence_expiration',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.insurance_company',
                        name: 'type.insurance_company',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.insurance_number',
                        name: 'type.insurance_number',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.insurance_expiration',
                        name: 'type.insurance_expiration',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.insurance_card_expiration',
                        name: 'type.insurance_card_expiration',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.health_number',
                        name: 'type.health_number',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.passport_number',
                        name: 'type.passport_number',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'type.passport_expiration',
                        name: 'type.passport_expiration',
                        searchable: false,
                        orderable: false
                    },
                    {
                        name: "status",
                        data: function(data, type, full, meta) {
                            if (data.phone != '0992990128') {
                                return `
            <label class="switch switch-primary">
              <input type="checkbox" title="Activate" class="soft-delete activate-object switch-input" data-object-id="${data.id}" ${data.deleted_at != null ? "" : "checked"} />
              <span class="switch-toggle-slider">
                <span class="switch-on">
                  <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                  <i class="ti ti-x"></i>
                </span>
              </span>
            </label>`;
                            } else {
                                return '';
                            }
                        },
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            let sendEmail = '';
                            if (data.email) {
                                sendEmail = '<a href="mailto:' + data.email +
                                    '" class="text-body"><i class="ti ti-mail ti-sm me-2"></i></a>';
                            }
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('employee.edit', data.type_id) +
                                '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                '<a href="' + route('employee.show', data.type_id) +
                                '" class="text-body" title="{{ _t('Show') }}"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                sendEmail +
                                '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                '<a href="' + route('salary.create', {
                                    employee_id: data.type_id
                                }) +
                                '" class="dropdown-item">{{ _t('Salary & Percentage') }}</a>' +
                                '<a href="' + route('vacation.index', {
                                    employee_id: data.type_id
                                }) + '" class="dropdown-item">{{ _t('Vacations') }}</a>' +
                                '</div>' +
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
                }, ],
            });
            $('#dateFilterFrom, #dateFilterTo').on('change', function() {
                dataTable.ajax.reload();
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

            $('.job_select2').change(function() {
                job_id = $(this).val();
                dataTable.ajax.reload();
            });

            $('.role_select2').change(function() {
                role_id = $(this).val();
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

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a
                                href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::MANAGEMENT->value]) }}">
                                <span>{{ _t('Management') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['management'] }}</h3>
                                <!-- <p class="text-success mb-0">(+29%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-user ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a
                                href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::PROCUREMENT->value]) }}">
                                <span>{{ _t('Procurement') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['procurement'] }}</h3>
                                <!-- <p class="text-success mb-0">(+18%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-user-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::SALES->value]) }}">
                                <span>{{ _t('Sales') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['sales'] }}</h3>
                                <!-- <p class="text-danger mb-0">(-14%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-user-check ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::STAFF->value]) }}">
                                <span>{{ _t('Staff') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['staff'] }}</h3>
                                <!-- <p class="text-success mb-0">(+42%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-user-exclamation ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a
                                href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::WAREHOUSE->value]) }}">
                                <span>{{ _t('Warehouse') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['warehouse'] }}</h3>
                                <!-- <p class="text-success mb-0">(+42%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-user-exclamation ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <a
                                href="{{ route('employee.index', ['section' => App\Enums\SectionEnum::FINANCIAL->value]) }}">
                                <span>{{ _t('Financial') }}</span>
                            </a>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2">{{ $statistics['financial'] }}</h3>
                                <!-- <p class="text-success mb-0">(+42%)</p> -->
                            </div>
                            <p class="mb-0">{{ _t('Total') }}</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-user-exclamation ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Employees') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 mb-2 user_role">
                    <select id="roleFilter" name="role_id" class="role_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4 mb-2 user_plan">
                    <select id="jobFilter" name="job_id" class="job_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4 mb-2 user_status">
                    <select id="nationalityFilter" name="nationality_id" class="nationality_select2 form-select"
                        data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <a href="{{ route('employee.create') }}" class="btn btn-primary mb-3"
                        id="newUserButton">{{ _t('New Employee') }}</a>
                </div>
                <div class="col-md-4 mb-2">
                    <a href="javascript:;" class="btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#importDataModal">{{ _t('Import') }}</a>
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
                        <th>{{ _t('Image') }}</th>
                        <th>{{ _t('Name') }}</th>
                        <th>{{ _t('Phone') }}</th>
                        <th>
                            {{ _t('Role') }}
                        </th>
                        <th>
                            {{ _t('Job') }}

                        </th>
                        <th>
                            {{ _t('Nationality') }}
                        </th>
                        <th>{{ _t('Email') }}</th>
                        <th>{{ _t('Vacations') }}</th>
                        <th>{{ _t('Taken Vacations') }}</th>
                        <th>{{ _t('Left Vacations') }}</th>
                        <th>{{ _t('Holiday') }}</th>
                        <th>{{ _t('Birthday') }}</th>
                        <th>{{ _t('Start Date') }}</th>
                        <th>{{ _t('Residence Number') }}</th>
                        <th>{{ _t('Residence Expires') }}</th>
                        <th>{{ _t('Insurance Company') }}</th>
                        <th>{{ _t('Insurance Number') }}</th>
                        <th>{{ _t('Insurance Expires') }}</th>
                        <th>{{ _t('Insurance Card Expires') }}</th>
                        <th>{{ _t('Health Number') }}</th>
                        <th>{{ _t('Passport Number') }}</th>
                        <th>{{ _t('Passport Expires') }}</th>
                        <th>{{ _t('Archive') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
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
                        <h4 class="onboarding-title text-body">{{ _t('Employees Import') }}</h4>
                        <form method="POST" action="{{ route('employee.import') }}" enctype="multipart/form-data">
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
