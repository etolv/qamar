@extends('layouts/layoutMaster')

@section('title', 'Customer List')

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
    {{-- <script src="{{ asset('assets/js/app-user-list.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            const flatpickrRange = document.querySelector('#flatpickr-range');
            const flatpickrVisitsRange = document.querySelector('#flatpickr-visits-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }
            if (typeof flatpickrVisitsRange != undefined) {
                flatpickrVisitsRange.flatpickr({
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
            let service_id = getUrlParameter('service_id');
            let branch_id = getUrlParameter('branch_id');
            let last_visit = getUrlParameter('last_visit');
            let visit_count = getUrlParameter('visit_count') ?? 0;
            let visit_range = getUrlParameter('visit_range');
            window.deleteOptions('User');
            $('.last_visit_select2').select2();
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
            $('.service_select2').select2({
                placeholder: '{{ _t('Bought Service') }}',
                ajax: {
                    url: route('service.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        console.log('data');
                        console.log(data.data.data)
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
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
                    url: "{{ route('customer.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.date = date;
                        d.service_id = service_id;
                        d.branch_id = branch_id;
                        d.last_visit = last_visit;
                        d.visit_count = visit_count;
                        d.visit_range = visit_range;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: true,
                        searchable: true
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
                        data: 'type.city.name',
                        name: 'type.city.translations.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type.points',
                        name: 'type.points',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'last_visit',
                        name: 'last_visit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'visit_count',
                        name: 'visit_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: "status",
                        data: function(data, type, full, meta) {
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
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('customer.edit', data.type_id) +
                                '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                // '<a href="' + route('customer.edit', data.type_id) + '" class="text-body">{{ _t('Bills') }}</a>' +
                                '<a href="' + route('booking.index', {
                                    'customer_id': data.type_id
                                }) +
                                '" class="dropdown-item">{{ _t('Bookings') }}</a>' +
                                '<a href="' + route('order.index', {
                                    'customer_id': data.type_id
                                }) +
                                '" class="dropdown-item">{{ _t('Orders') }}</a>' +
                                '<a href="' + route('rate.index', {
                                    customer_id: data.type_id
                                }) +
                                '" class="dropdown-item">{{ _t('Rates') }}</a>' +
                                '<a href="' + route('customer.edit', data.type_id) +
                                '" class="dropdown-item">{{ _t('Bills') }}</a>' +
                                '<a href="' + route('customer.edit', data.type_id) +
                                '" class="dropdown-item">{{ _t('Points') }}</a>' +
                                '<a href="' + route('listed-order.index', {
                                    customer_id: data.type_id
                                }) +
                                '" class="dropdown-item">{{ _t('Listed Orders') }}</a>' +
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
                            $(node).closest('.dt-buttons').removeClass(
                                    'btn-group')
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
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });
            $('.visits-datepicker').change(function() {
                visit_range = $(this).val();
                dataTable.ajax.reload();
            });
            $('.service_select2').change(function() {
                service_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.branch_select2').change(function() {
                branch_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.last_visit_select2').change(function() {
                last_visit = $(this).val();
                dataTable.ajax.reload();
            });
            $('#visit_count').change(function() {
                visit_count = $(this).val();
                dataTable.ajax.reload();
            });

        });
    </script>

@endsection

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Customers') }}</h5>
            <div class="d-flex justify-content-between align-items-start row pb-2 gap-3 gap-md-0">
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="flatpickr-range">{{ _t('Date Range') }}</label>
                    <input type="text" class="form-control datepicker" value="{{ old('date') }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="flatpickr-visits-range">{{ _t('Visits Range') }}</label>
                    <input type="text" class="form-control visits-datepicker" value="{{ old('date') }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-visits-range" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="service_id">{{ _t('Service Bought') }}</label>
                    <select id="service_id" name="service_id" class="service_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="last_visit">{{ _t('Last Visit') }}</label>
                    <select id="last_visit" name="last_visit" class="last_visit_select2 form-select"
                        data-allow-clear="true">
                        <option value="">{{ _t('All') }}</option>
                        <option value="7">{{ _t('Since a week') }}</option>
                        <option value="14">{{ _t('Since 2 weeks') }}</option>
                        <option value="30">{{ _t('Since 1 month') }}</option>
                        <option value="90">{{ _t('Since 3 months') }}</option>
                        <option value="180">{{ _t('Since 6 months') }}</option>
                        <option value="365">{{ _t('Since 1 year') }}</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="visit_count">{{ _t('Visit Count') }} ({{ _t('More than') }})</label>
                    <input type="number" value="0" step="1" id="visit_count" name="visit_count"
                        class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="branch_id">{{ _t('Branch') }}</label>
                    <select id="branch_id" name="branch_id" class="branch_select2 form-select" data-allow-clear="true">
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('customer.create') }}" class="btn btn-primary mb-3"
                        id="newUserButton">{{ _t('New Customer') }}</a>
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
                        <th>{{ _t('City') }}</th>
                        <th>{{ _t('Points') }}</th>
                        <th>{{ _t('Date') }}</th>
                        <th>{{ _t('Last Visit') }}</th>
                        <th>{{ _t('Visit Count') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
