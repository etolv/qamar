@extends('layouts/layoutMaster')

@section('title', 'Suppliers Report')

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
            let type = null;
            let supplier_id = null;
            let name = null;
            let phone = null;
            let email = null;
            let date = null;
            $('.supplier_select2').select2({
                placeholder: "{{ _t('Filter Supplier') }}",
                ajax: {
                    url: route('supplier.search'),
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
                                    text: item.name + ' - ' + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.type_select2').select2({});

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
                    url: "{{ route('report.supplier.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.supplier_id = supplier_id;
                        d.type = type;
                        d.name = name;
                        d.phone = phone;
                        d.email = email;
                        d.date = date;
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
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            row_output = "<span>{{ _t('N/A') }}</span>";
                            if (full.supplier) {
                                var name = full.supplier?.name,
                                    image = full.supplier_image ||
                                    "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                    number = full.supplier?.phone,
                                    baseUrl = route('supplier.edit', full.supplier?.id || 1);
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
                            }
                            return row_output;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'city.name',
                        name: 'city.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'company',
                        name: 'company',
                    },
                    {
                        data: 'tax_number',
                        name: 'tax_number',
                    },
                    {
                        data: 'bank_number',
                        name: 'bank_number',
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'bills_data.count',
                        name: 'bills_data.count'
                    },
                    {
                        data: 'bills_data.total',
                        name: 'bills_data.total'
                    },
                    {
                        data: 'bills_data.paid',
                        name: 'bills_data.paid'
                    },
                    {
                        data: 'bills_data.dept',
                        name: 'bills_data.dept'
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

            $('.type_select2').change(function() {
                type = $(this).val();
                dataTable.ajax.reload();
            });
            $('.supplier_select2').change(function() {
                supplier_id = $(this).val();
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
        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Suppliers') }}</h5>
            <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ _t('CSV only support english characters use Excel for arabic') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="flatpickr-range">{{ _t('Date Range') }}</label>
                    <input type="text" class="form-control datepicker" value="{{ old('date') }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="name">{{ _t('Name') }}</label>
                    <input type="text" id="name" name="name" class="mt-2 name form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="phone">{{ _t('Phone') }}</label>
                    <input type="text" id="phone" name="phone" class="mt-2 phone form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="email">{{ _t('Email') }}</label>
                    <input type="text" id="email" name="email" class="mt-2 email form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="type_id">{{ _t('Type') }}</label>
                    <select id="type_id" name="type" class="type_select2 form-select" data-allow-clear="true">
                        <option value="" selected>{{ _t('All Types') }}</option>
                        @foreach (App\Enums\SupplierTypeEnum::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="supplier_id">{{ _t('Main Supplier') }}</label>
                    <select id="supplier_id" name="supplier_id" class="supplier_select2 form-select"
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
                        <th>{{ _t('Main Supplier') }}</th>
                        <th>{{ _t('Email') }}</th>
                        <th>{{ _t('Phone') }}</th>
                        <th>{{ _t('City') }}</th>
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Company') }}</th>
                        <th>{{ _t('Tax Number') }}</th>
                        <th>{{ _t('Bank Number') }}</th>
                        <th>{{ _t('Address') }}</th>
                        <th>{{ _t('Bill Counts') }}</th>
                        <th>{{ _t('Total') }}</th>
                        <th>{{ _t('Paid') }}</th>
                        <th>{{ _t('Dept') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--  -->

@endsection
