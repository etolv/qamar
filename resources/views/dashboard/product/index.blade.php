@extends('layouts/layoutMaster')

@section('title', 'Products')

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
            let consumption_type = getUrlParameter('consumption_type');
            let department = getUrlParameter('department');
            window.deleteOptions('Product');
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
                    url: "{{ route('product.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.consumption_type = consumption_type;
                        d.department = department;
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var image = full.image ||
                                "{{ asset('assets/img/illustrations/default.png') }}"
                            return '<img src="' + image +
                                '" alt="Profile Image" class="rounded-circle" style="width: 30px; height: 30px;">';
                        }
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'consumption_type',
                        name: 'consumption_type'
                    },
                    {
                        data: 'min_quantity',
                        name: 'min_quantity'
                    },
                    {
                        data: 'category.name',
                        name: 'category.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'brand.name',
                        name: 'brand.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.refundable) {
                                return "{{ _t('Yes') }}";
                            } else {
                                return "{{ _t('No') }}";
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        name: "status",
                        orderable: false,
                        searchable: false,
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
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('product.show', data.id) +
                                '" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                @can('update_product')
                                    '<a href="' + route('product.edit', data.id) +
                                        '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                @endcan
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                @can('read_stock')
                                    '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                    '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                    '<a href="' + route("stock.index", {
                                            product_id: data.id
                                        }) +
                                        '" class="dropdown-item">{{ _t('Stock') }} <i class="fa-solid fa-warehouse"></i></a>' +
                                @endcan
                                '' +
                                '<a target="_blank" href="' + route("product.barcode", data
                                    .id) +
                                '" class="dropdown-item">{{ _t('Barcode') }} <i class="fa-solid fa-barcode"></i></a>' +
                                // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                                '</div>' +
                                '</div>'
                            );
                        }
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

            // Add index column
            // dataTable.on('order.dt search.dt', function() {
            // dataTable.column(0, {
            //     search: 'applied',
            //     order: 'applied'
            // }).nodes().each(function(cell, i) {
            //     cell.innerHTML = i + 1;
            // });
            // }).draw();

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
            <h5 class="card-title mb-3">{{ _t('Products') }}</h5>
            <div class="d-flex justify-content-between align-items-start row pb-2 gap-3 gap-md-0">
                <div class="col-md-4">
                    @can('create_product')
                        <a href="javascript:;" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#importDataModal">{{ _t('Import') }}</a>
                    @endcan
                </div>
                <div class="col-md-4">
                    @can('create_product')
                        <a href="{{ route('product.create', ['department' => request()->department]) }}"
                            class="btn btn-primary mb-3" id="newProductButton">{{ _t('New product') }}</a>
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
                        <th>{{ _t('Image') }}</th>
                        <th>{{ _t('Barcode') }}</th>
                        <th>{{ _t('Name') }}</th>
                        <th>
                            {{ _t('Consumption Type') }}
                            <select id="consumptionTypFilter" name="consumption_type"
                                class="consumption_type_select2 form-select" data-allow-clear="true" required>
                                <option value="">{{ _t('All Options') }}</option>
                                @foreach (App\Enums\ConsumptionTypeEnum::cases() as $type)
                                    <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>{{ _t('Min Quantity') }}</th>
                        <th>{{ _t('Category') }}</th>
                        <th>{{ _t('Brand') }}</th>
                        <th>{{ _t('Refundable') }}</th>
                        <th>{{ _t('Created At') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Actions') }}</th>
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
                        <h4 class="onboarding-title text-body">{{ _t('Products Import') }}</h4>
                        <form method="POST" action="{{ route('product.import') }}" enctype="multipart/form-data">
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
