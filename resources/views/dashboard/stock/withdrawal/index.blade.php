@extends('layouts/layoutMaster')

@section('title', 'Stock Withdrawals')

@section('vendor-style')

    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')

    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            let product_id = null;
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employees') }}',
                ajax: {
                    url: route('employee.search'),
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
                                    text: item.name + ' - ' + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.type_select2').select2({
                placeholder: '{{ _t('Filter Type') }}'
            });
            $('.product_select2').select2({
                placeholder: '{{ _t('Filter Product') }}',
                ajax: {
                    url: route('product.search'),
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
                                    text: item.name + " - " + item.sku
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
            var employee_id = getUrlParameter('employee_id');
            var date = getUrlParameter('date');
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
                    url: "{{ route('stock.withdrawal.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.type = type;
                        d.product_id = product_id;
                        d.employee_id = employee_id;
                        d.department = department;
                        d.date = date;
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
                    },
                    // {
                    //     data: null,
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function(data, type, full, meta) {
                    //         var received = '';
                    //         return (
                    //             `<div class="d-flex align-items-center">
                //         <a href="${route('bill.show', data.id)}" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>
                //         ` +
                    //             //   '<a href="' + route('order.edit', data.id) + '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                    //             received + '</div>'
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
            // var colvis = new $.fn.dataTable.ColVis(dataTable);
            // $(colvis.button()).insertAfter('div.info');
            // dataTable.on('column-visibility', function(e, settings, colIdx, visibility) {
            //     dataTable.tables(':gt(0)').column(colIdx).visible(visibility);
            // });

            $('.type_select2').change(function() {
                type = $(this).val();
                dataTable.ajax.reload();
            });
            $('.product_select2').change(function() {
                product_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.employee_select2').change(function() {
                employee_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });
        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Withdrawals') }}</h5>
            <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                <div class="mb-3 col-md-3">
                    <input type="text" class="form-control datepicker" value="{{ old('date') }}" name="date"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                </div>
                <div class="mb-3 col-md-3">
                    <select id="typeFilter" name="type" class="type_select2 form-select" data-allow-clear="true"
                        required>
                        @foreach (App\Enums\StockWithdrawalTypeEnum::cases() as $type)
                            <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 col-md-3">
                    <select id="productFilter" name="product_id" class="product_select2 form-select" data-allow-clear="true"
                        required>
                    </select>
                </div>
                <div class="mb-3 col-md-3">
                    <select id="employeeFilter" name="employee_id" class="employee_select2 form-select"
                        data-allow-clear="true" required>
                    </select>
                </div>
                <div class="mb-3 col-md-3">
                    <a href="{{ route('stock-withdrawal.create', ['department' => request()->department]) }}"
                        class="btn btn-primary mb-3" id="newProductButton">{{ _t('Withdraw') }}</a>
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
                        {{-- <th>{{ _t('Actions') }}</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--  -->

@endsection
