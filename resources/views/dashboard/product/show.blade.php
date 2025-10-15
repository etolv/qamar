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
            let product_id = "{{ $product->id }}";
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var dataTable = $('.datatables-stock-test').DataTable({
                // Customize DataTable options here
                // For example:
                language: {
                    url: `https://cdn.datatables.net/plug-ins/1.11.5/i18n/${current_system_locale}.json`
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stock.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.product_id = product_id;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var name = full.product.name,
                                image = full.product_image ||
                                "{{ asset('assets/img/illustrations/default.png') }}",
                                sku = full.product.sku,
                                baseUrl = route('product.edit', full.product_id);
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
                        data: 'consumption_type',
                        name: 'consumption_type',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'product.min_quantity',
                        name: 'product.min_quantity'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'exchange_price',
                        name: 'exchange_price'
                    },
                    {
                        data: 'unit.name',
                        name: 'unit.name'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var date = new Date(full.expiration_date);

                            // Check if the date is less than or equal to today
                            if (date <= new Date()) {
                                return '<span style="color: red;">' + full.expiration_date +
                                    '</span>';
                            } else {
                                return full
                                    .expiration_date; // If not less than or equal to today, render normally
                            }
                        }
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name',
                        orderable: false,
                        searchable: false,
                    },
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
            var dataTable_withdrawals = $('.datatables-withdrawal').DataTable({
                language: {
                    url: `https://cdn.datatables.net/plug-ins/1.11.5/i18n/${current_system_locale}.json`
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stock.withdrawal.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.product_id = product_id;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        searchable: false,
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
                                '<a href="#" class="text-body text-truncate"><span class="fw-semibold">' +
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
                        data: 'quantity',
                        name: 'quantity',
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                    {
                        data: 'tax',
                        name: 'tax',
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                `<span class="badge bg-light text-capitalize">${(data.price * data.quantity)}</span>`
                            );
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                `<span class="badge bg-light text-capitalize">${data.tax+(data.price * data.quantity)}</span>`
                            );
                        }
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                    },
                    {
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

        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('product.index') }}">{{ _t('Products') }}</a> /
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
                                src="{{ $product->getFirstMedia('profile') ? $product->getFirstMedia('profile')->getUrl() : asset('assets/img/illustrations/NoImage.png') }}"
                                height="100" width="100" alt="User avatar" />
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $product->name }}</h4>
                                <span class="badge bg-label-secondary mt-1">{{ $product->sku }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                        <div class="d-flex align-items-start me-4 mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-checkbox ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">1.23k</p>
                                <small>Tasks Done</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-briefcase ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">568</p>
                                <small>Projects Done</small>
                            </div>
                        </div>
                    </div> --}}
                    <p class="mt-4 small text-uppercase text-muted">Details</p>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-medium me-1">{{ _t('Consumption Type') }}:</span>
                                <span>{{ $product->consumption_type?->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Min Quantity') }}:</span>
                                <span>{{ $product->min_quantity }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Category') }}:</span>
                                <span>{{ $product->category->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Brand') }}:</span>
                                <span>{{ $product->brand->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Department') }}:</span>
                                <span>{{ $product->department?->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Created At') }}:</span>
                                <span>{{ $product->created_at }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Status') }}:</span>
                                <span
                                    class="badge bg-label-{{ $product->deleted_at ? 'danger' : 'success' }}">{{ $product->deleted_at ? _t('Archived') : _t('Active') }}</span>
                            </li>
                        </ul>
                        @can('update_product')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-start">
                                        <a href="{{ route('product.edit', $product->id) }}"
                                            class="btn btn-primary me-3">{{ _t('Edit') }}</a>
                                        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#roundVacationModal">
                                            {{ _t('Round Vacations') }}
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                        @endcan
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
                <h5 class="card-header">{{ _t('Stock') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-stock-test table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Barcode') }}</th>
                                <th>{{ _t('Product') }}</th>
                                <th>{{ _t('Consumption Type') }}</th>
                                <th>{{ _t('Qty') }}</th>
                                <th>{{ _t('Min Qty') }}</th>
                                <th>{{ _t('Retail Price') }}</th>
                                <th>{{ _t('Exchange Price') }}</th>
                                <th>{{ _t('Unit') }}</th>
                                <th>{{ _t('Expiration date') }}</th>
                                <th>{{ _t('Supplier') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Withdrawals') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-withdrawal table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <!-- Filter -->
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Type') }}</th>
                                <th>{{ _t('Product') }}</th>
                                <th>{{ _t('Employee') }}</th>
                                <th>{{ _t('Quantity') }}</th>
                                <th>{{ _t('Price') }}</th>
                                <th>{{ _t('Tax') }}</th>
                                <th>{{ _t('Total') }}</th>
                                <th>{{ _t('Grand Total') }}</th>
                                <th>{{ _t('Reason') }}</th>
                                <th>{{ _t('Date') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!--/ User Content -->
    </div>

    <!-- Modal -->

    <!-- end modal -->
@endsection
