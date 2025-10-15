@extends('layouts/layoutMaster')

@section('title', 'Cash List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            window.deleteOptions('cash_flow');
            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }
            $('.type_select2').select2({});
            $('.employee_select2').select2({
                placeholder: '{{ _t('Filter Employee') }}',
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

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var employeeId = getUrlParameter('employee_id');
            var type = getUrlParameter('type');
            var date = getUrlParameter('date');
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
                    url: "{{ route('cash_flow.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.employee_id = employeeId;
                        d.date = date;
                        d.type = type;
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
                            if (full.flowable.user) {
                                var name = full.flowable.user.name,
                                    image = full.flowable_image ||
                                    "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                    number = full.flowable.user.phone,
                                    baseUrl = route('employee.show', full.flowable_id);
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
                        data: 'type_name',
                        name: 'type_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'status_name',
                        name: 'status_name',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var completeAction = '';
                            if (data.due_date >= moment().format('YYYY-MM-DD')) {
                                completeAction =
                                    `<a href="javascript:;" class="text-body postponed" data-bs-toggle="modal" data-id="${data.id}" data-date="${data.due_date}" data-bs-target="#editCashModal"><i class="fa-solid fa-clock-rotate-left ti-sm mx-2" title="{{ _t('Postponed due date') }}"></i></a>`;
                            }
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="javascript:;" data-object-id="' + data.id +
                                '" data-with-trashed="false" class="text-body delete-record item-delete"><i class="fa-solid fa-trash-can ti-sm mx-2"></i></a>' +
                                // '<a href="' + route('order.show', data.id) + '" class="text-body"><i class="ti ti-eye ti-sm me-2"></i></a>' +
                                // '<a href="' + route('salary.close', data.id) + '" class="text-body"><i class="fa-solid fa-rectangle-xmark"></i></a>' +
                                completeAction + '</div>'
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
                }],
                // order: [9, 'asc']
            });

            $('.employee_select2').change(function() {
                employeeId = $(this).val();
                dataTable.ajax.reload();
            });
            $('.type_select2').change(function() {
                type = $(this).val();
                dataTable.ajax.reload();
            });
            $('.datepicker').change(function() {
                date = $(this).val();
                dataTable.ajax.reload();
            });

            $('#editCashModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var due_date = button.data('date');
                var url = route('cash-flow.update', button.data('id'));
                var modal = $(this);
                modal.find('#form-edit').attr('action', url);
                modal.find('#due_date').val(due_date);
            });



        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Cash Withdrawals') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    <a href="{{ route('cash-flow.create') }}" class="btn btn-primary mb-3"
                        id="newUserButton">{{ _t('New Cash Withdrawal') }}</a>
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
                        <th>
                            {{ _t('Employee') }}
                            <select id="employeeFilter" name="employee_id" class="employee_select2 form-select"
                                data-allow-clear="true" required>
                            </select>
                        </th>
                        <th>
                            {{ _t('Type') }}
                            <select id="type" name="type" class="type_select2 form-select" data-allow-clear="false">
                                <option value="">{{ _t('All Types') }}</option>
                                @foreach (App\Enums\CashFlowTypeEnum::cases() as $type)
                                    <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Amount') }}</th>
                        <th>{{ _t('Reason') }}</th>
                        <th>
                            {{ _t('Due Date') }}
                            <input type="text" class="form-control datepicker" value="{{ old('date') }}"
                                name="date" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                        </th>
                        <th>{{ _t('Created At') }}</th>
                        <th>{{ _t('Action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <div class="modal-onboarding modal fade animate__animated" id="editCashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Edit Service Employee') }}</h4>
                        <form method="POST" action="#" id="form-edit" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class=" row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Due Date') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <div class="col-sm-10">
                                            <input type="date" class="form-control" name="due_date"
                                                value="{{ old('due_date') }}" id="due_date"
                                                placeholder="{{ _t('Due Date') }}" />
                                        </div>
                                    </div>
                                    @error('due_date')
                                        <span class=" text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">{{ _t('Update') }}</button>
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
