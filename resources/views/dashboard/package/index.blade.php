@extends('layouts/layoutMaster')

@section('title', 'Package List')

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
        // Echo.private('new-order')
        //   .listen('NewOrderEvent', (event) => {
        //     toastr.success("{{ _t('New booking placed') }} #" + event.data.id);
        //     $('.datatables-users-test').DataTable().ajax.reload();
        //   });
        $(document).ready(function() {
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var customerId = getUrlParameter('customer_id');
            var date = "{{ $date ?? null }}";
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
                    url: "{{ route('package.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.customer_id = customerId;
                        d.date = date;
                    }
                    // data: function(d) {
                    //   d.date_from = $('#dateFilterFrom').val();
                    //   d.date_to = $('#dateFilterTo').val();
                    // }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        orderable: false,
                        searchable: false,
                        data: 'services_count',
                        name: 'services_count'
                    },
                    {
                        orderable: false,
                        searchable: false,
                        data: 'products_count',
                        name: 'products_count'
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'order_count',
                        name: 'order_count'
                    },
                    {
                        orderable: true,
                        searchable: true,
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        orderable: true,
                        searchable: true,
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('package.show', data.id) +
                                '" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                @can('update_package')
                                    '<a href="' + route('package.edit', data.id) +
                                        '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                @endcan
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                @can('read_rate')
                                    '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                    '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                    '<a href="' + route('rate.index', {
                                            booking_id: data.id
                                        }) + '" class="dropdown-item">{{ _t('rates') }}</a>' +
                                @endcan
                                // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
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
                // order: [9, 'asc']
            });
            $('#dateFilterFrom, #dateFilterTo').on('change', function() {
                dataTable.ajax.reload();
            });


        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Packages') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    @can('create_package')
                        <a href="{{ route('package.create') }}" class="btn btn-primary mb-3"
                            id="newUserButton">{{ _t('New Package') }}</a>
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
                        <th>{{ _t('Name') }}</th>
                        <th>{{ _t('Description') }}</th>
                        <th>{{ _t('Total') }}</th>
                        <th>{{ _t('Services') }}</th>
                        <th>{{ _t('Products') }}</th>
                        <th>{{ _t('Orders') }}</th>
                        <th>{{ _t('Start date') }}</th>
                        <th>{{ _t('End date') }}</th>
                        <th>{{ _t('Created Date') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
