@extends('layouts/layoutMaster')

@section('title', 'Bills Dept')

@section('vendor-style')

    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')

    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/fixedColumns.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
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
            let bill_type_id = null;
            let bill_type = null;
            let supplier_id = null;
            $('.bill_type_select2').select2({});
            $('.type_select2').select2({
                placeholder: '{{ _t('Select Type') }}',
                ajax: {
                    url: route('bill.type.search'),
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
            $('.supplier_select2').select2({
                placeholder: '{{ _t('Select Supplier') }}',
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
                                    text: item.name
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
            let supplierId = getUrlParameter('supplier_id');
            let department = getUrlParameter('department');
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
                    url: "{{ route('bill.debt.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.supplier_id = supplierId;
                        d.type = bill_type;
                        d.bill_type_id = bill_type_id;
                        d.department = department;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'identifier',
                        name: 'identifier',
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
                        data: 'bill_type.name',
                        name: 'bill_type.name',
                        orderable: false,
                        searchable: false
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
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'paid',
                        name: 'paid'
                    },
                    {
                        data: 'left',
                        name: 'left',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var color = data.received ? 'success' : 'warning';
                            var received_text = data.received ? "{{ _t('Received') }}" :
                                "{{ _t('Not Received') }}";
                            return (`<span class="badge bg-label-${color}">` +
                                received_text + '</span>');
                        }
                    },
                    {
                        data: 'receiving_date',
                        name: 'receiving_date'
                    },
                    {
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
                                '<a href="' + route('bill.show', data.id) +
                                '" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                //   '<a href="' + route('order.edit', data.id) + '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                //   '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                //   '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                                //   '</div>' +
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
                        {
                            extend: 'csv',
                            text: 'Excel',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'dropdown-item',
                        },
                        {
                            extend: 'excel',
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
            $('.bill_type_select2').change(function() {
                bill_type = $(this).val();
                dataTable.ajax.reload();
            });
            $('.type_select2').change(function() {
                bill_type_id = $(this).val();
                dataTable.ajax.reload();
            });
            $('.supplier_select2').change(function() {
                supplierId = $(this).val();
                dataTable.ajax.reload();
            });

        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Bills Debt') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    <!-- <a href="{{ route('bill.create') }}" class="btn btn-primary mb-3" id="newProductButton">{{ _t('New Bill') }}</a> -->
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
                        <th>{{ _t('ID') }}</th>
                        <th>
                            {{ _t('Type') }}
                            <select id="billTypeFilter" name="type" class="bill_type_select2 form-select"
                                data-allow-clear="true" required>
                                <option value="">{{ _t('All Options') }}</option>
                                @foreach (App\Enums\BillTypeEnum::cases() as $type)
                                    <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            {{ _t('Expense') }}
                            <select id="typeFilter" name="bill_type_id" class="type_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
                        <th>
                            {{ _t('Supplier') }}
                            <select id="supplierFilter" name="supplier_id" class="supplier_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
                        <th>{{ _t('Total') }}</th>
                        <th>{{ _t('Paid') }}</th>
                        <th>{{ _t('Left') }}</th>
                        <th>{{ _t('Receiving Status') }}</th>
                        <th>{{ _t('Receiving Date') }}</th>
                        <th>{{ _t('Date') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--  -->

@endsection
