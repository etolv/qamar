@extends('layouts/layoutMaster')

@section('title', 'Order Service Return')

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
        //     toastr.success("{{ _t('New order placed') }} #" + event.data.id);
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
                    url: "{{ route('order-service-return.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.customer_id = customerId;
                        // d.date_to = $('#dateFilterTo').val();
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
                            var name = full.order_service.order.customer.user.name,
                                image = full.customer_image ||
                                "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                number = full.order_service.order.customer.user.phone,
                                baseUrl = route('customer.edit', full.order_service.order.customer
                                    .id);
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
                        data: 'order_service.service.name',
                        name: 'order_service.service.name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'order_service.price',
                        name: 'order_service.price'
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
                            var actions =
                                '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                '<a href="' + route('order.show', data.order_service.order_id) +
                                '" class="dropdown-item"><i class="fa-regular fa-eye"></i> {{ _t('View Order') }}</a>' +
                                '<a href="' + route('notification.user.create', data.order_service
                                    .order.customer.user_id) +
                                '" class="dropdown-item"><i class="fa-regular fa-comment"></i> {{ _t('Notification') }}</a>' +
                                '</div>';
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a target="_blank" href="https://wa.me/' + data.order_service
                                .order.customer.user.phone + '" title="' +
                                "{{ _t('Whatsapp') }}" +
                                '" class="text-body"><i class="fa-brands fa-whatsapp"></i></a>' +
                                // '<a href="' + route("order-service-session.show", data.id) + '" title="' + "{{ _t('Sessions') }}" + '" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                actions +
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
            <h5 class="card-title mb-3">{{ _t('Order Service Return') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    <!-- <a href="{{ route('order.create') }}" class="btn btn-primary mb-3" id="newUserButton">{{ _t('New Order') }}</a> -->
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
                        <th>{{ _t('Customer') }}</th>
                        <th>{{ _t('Service') }}</th>
                        <th>{{ _t('Quantity') }}</th>
                        <th>{{ _t('Service Price') }}</th>
                        <th>{{ _t('Date') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
