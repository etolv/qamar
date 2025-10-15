@extends('layouts/layoutMaster')

@section('title', 'Stocks')

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

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.consumption_type_select2').select2({});

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var productId = getUrlParameter('product_id');
            var consumption_type = getUrlParameter('consumption_type');
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
                ajax: {
                    url: "{{ route('stock.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.product_id = productId;
                        d.department = department;
                        d.consumption_type = consumption_type;
                        //   d.status = booking_status;
                        //   d.date = date;
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
                        name: 'product.name',
                        data: null,
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
                    //     {
                    //         name: "status",
                    //         orderable: false,
                    //         searchable: false,
                    //         data: function(data, type, full, meta) {
                    //             return `
                // <label class="switch switch-primary">
                //   <input type="checkbox" title="Activate" class="soft-delete activate-object switch-input" data-object-id="${data.id}" ${data.deleted_at != null ? "" : "checked"} />
                //   <span class="switch-toggle-slider">
                //     <span class="switch-on">
                //       <i class="ti ti-check"></i>
                //     </span>
                //     <span class="switch-off">
                //       <i class="ti ti-x"></i>
                //     </span>
                //   </span>
                // </label>`;
                    //         },
                    //     },
                    // {
                    //     data: null,
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function(data, type, full, meta) {
                    //         return (
                    //             '<div class="d-flex align-items-center">' +
                    //             '<a href="' + route('stock.edit', data.id) + '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                    //             // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                    //             // '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                    //             // '<div class="dropdown-menu dropdown-menu-end m-0">' +
                    //             // '<a href="javascript:;" class="dropdown-item">View</a>' +
                    //             // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                    //             // '</div>' +
                    //             '</div>'
                    //         );
                    //     }
                    // }
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

            // Add index column
            dataTable.on('order.dt search.dt', function() {
                dataTable.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
            $('.consumption_type_select2').change(function() {
                consumption_type = $(this).val();
                dataTable.ajax.reload();
            });


        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Stocks') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 mb-3">
                    <select id="consumptionTypFilter" name="consumption_type" class="consumption_type_select2 form-select"
                        data-allow-clear="true" required>
                        <option value="">{{ _t('All Options') }}</option>
                        @foreach (App\Enums\ConsumptionTypeEnum::cases() as $type)
                            <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    @can('create_stock')
                        <a href="{{ route('stock-withdrawal.create', ['department' => request()->department]) }}"
                            class="btn btn-primary mb-3" id="newProductButton">{{ _t('Withdraw') }}</a>
                    @endcan
                </div>
                <div class="col-md-4">
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
                        <!-- <th>{{ _t('Status') }}</th> -->
                        <!-- <th>{{ _t('Actions') }}</th> -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--  -->
    <div class="modal-onboarding modal fade animate__animated" id="importDataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Stock Import') }}</h4>
                        <form method="POST" action="{{ route('stock.import') }}" enctype="multipart/form-data">
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
