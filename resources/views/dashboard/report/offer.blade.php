@extends('layouts/layoutMaster')

@section('title', 'Offer List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            let date = getUrlParameter('date');
            let status = getUrlParameter('status');
            let customer_id = getUrlParameter('customer_id');
            let employee_id = getUrlParameter('employee_id');
            let package_id = getUrlParameter('package_id');
            let is_gift = getUrlParameter('is_gift');
            let branch_id = getUrlParameter('branch_id');
            $('.order_type_select2').select2({});
            $('.status_select2').select2({});
            $('.package_select2').select2({
                placeholder: '{{ _t('Select Package') }}',
                ajax: {
                    url: route('package.search'),
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
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.customer_select2').select2({
                placeholder: '{{ _t('Select Customer') }}',
                ajax: {
                    url: route('customer.search'),
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
                                    text: item.name + " - " + item.phone,
                                    points: item.type.points
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employee') }}',
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
                            // type: 'orders_hairdresser' // your custom parameter
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.type_id,
                                    text: item.name + " - " + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.branch_select2').select2({
                placeholder: '{{ _t('Select Branch') }}',
                ajax: {
                    url: route('branch.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name + " - " + item.city.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var dataTable = $('.datatables-users-test').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/{{ session()->get('locale') ?? app()->getLocale() }}.json" // Arabic language file
                },
                processing: true,
                serverSide: true,
                paging: false, // pagination
                searching: false, // search box
                ajax: {
                    url: "{{ route('report.offer.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.date = date;
                        d.status = status;
                        d.customer_id = customer_id;
                        d.employee_id = employee_id;
                        d.is_gift = is_gift;
                        d.branch_id = branch_id;
                        d.package_id = package_id;
                    },
                    dataSrc: function(response) {
                        $('.total_count').text(response.total_count);
                        $('.total_orders').text(response.total_orders);
                        $('.total_tax').text(response.total_tax);
                        $('.bill_count').text(response.bill_count);
                        $('.bill_total').text(response.bill_sum);
                        $('.bill_tax').text(response.bill_tax);
                        return response.data;
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
                            var name = full.customer.user.name,
                                image = full.customer_image ||
                                "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                number = full.customer.user.phone,
                                baseUrl = route('customer.edit', full.customer.id);
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
                                number +
                                '</small>' +
                                '</div>' +
                                '</div>';
                            return row_output;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.branch) {
                                var name = full.branch.name,
                                    number = full.branch.city.name,
                                    baseUrl = route('branch.edit', full.branch.id);
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
                            } else {
                                return '{{ _t('Not assigned') }}'
                            }
                            return row_output;
                        }
                    },
                    {
                        data: null,
                        name: 'status',
                        render: function(data, type, full, meta) {
                            var color = data.status == 1 ? 'warning' : data.status ==
                                6 ? 'danger' : 'success';
                            return (
                                `<span class="badge bg-label-${color}">` +
                                data.status_name + '</span>'
                            );
                        }
                    },
                    {
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.order_packages) {
                                let subTable = '<table class="table table-bordered">';
                                subTable +=
                                    '<tr><th>ID</th><th>Name</th><th>Price</th></tr>';
                                full.order_packages.forEach(function(pkg) {
                                    subTable += `
                                    <tr>
                                        <td>${pkg.id}</td>
                                        <td>${pkg.package.name}</td>
                                        <td>${pkg.total_price}</td>
                                    </tr>
                                `;
                                });
                                subTable += '</table>';
                                return (subTable);
                            } else {
                                return ('');
                            }
                            // orderPackages
                        }
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'created_at',
                        name: 'created_at'
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
                        //     text: 'CSV',
                        //     className: 'dropdown-item',
                        // },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            className: 'dropdown-item',
                        },
                        // {
                        //     extend: 'pdfHtml5',
                        //     text: 'PDF',
                        //     className: 'dropdown-item',
                        // },
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
            $('#dateFilterFrom, #dateFilterTo').on('change', function() {
                dataTable.ajax.reload();
            });

            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });
            $('.status_select2').change(function() {
                status = $(this).val();
                dataTable.ajax.reload();
            });
            $('.customer_select2').change(function() {
                customer_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.employee_select2').change(function() {
                employee_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.order_type_select2').change(function() {
                is_gift = $(this).val();
                dataTable.ajax.reload();
            });
            $('.branch_select2').change(function() {
                branch_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.package_select2').change(function() {
                package_id = $(this).val();
                dataTable.ajax.reload();
            });

        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Order Packages') }}</h5>
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
                    <label class="form-label" for="status">{{ _t('Status') }}</label>
                    <select id="status" name="status" class="status_select2 form-select" data-allow-clear="true">
                        <option>{{ _t('All') }}</option>
                        @foreach (App\Enums\StatusEnum::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="customer_id">{{ _t('Customer') }}</label>
                    <select id="customer_id" name="customer_id" class="customer_select2 form-select"
                        data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="employee_id">{{ _t('Employee') }}</label>
                    <select id="employee_id" name="employee_id" class="employee_select2 form-select"
                        data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="is_gift">{{ _t('Package') }}</label>
                    <select id="package_id" name="package_id" class="package_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="branch_id">{{ _t('Branch') }}</label>
                    <select id="branch_id" name="branch_id" class="branch_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-users-test table">{{-- add class 'dt-responsive' for responsive --}}
                <thead class="border-top">
                    <tr>
                        <!-- Filter -->
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>{{ _t('Customer') }}</th>
                        <th>{{ _t('Branch') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Packages') }}</th>
                        <th>{{ _t('Created Date') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
