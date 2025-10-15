@extends('layouts/layoutMaster')

@section('title', 'booking edit requests List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            window.deleteOptions('booking_edit_request');
            // $('.sectionFilter').select2({
            //   placeholder: '{{ _t('locale.select_value') }}',
            //   ajax: {
            //     url: route('sections.search'),
            //     headers: {
            //       'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
            //     },
            //     dataType: 'json',
            //     delay: 250,
            //     processResults: function(data) {
            //       console.log('data');
            //       console.log(data.data.data)
            //       return {
            //         results: data.data.data.map(function(item) {
            //           return {
            //             id: item.id,
            //             text: item.name
            //           };
            //         })
            //       };
            //     },
            //     cache: true
            //   }
            // });
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
                    url: "{{ route('booking-edit-request.fetch', ['type' => 2]) }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
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
                        data: 'booking.id',
                        name: 'booking.id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            var baseUrl = route('booking.show', full.booking_id),
                                name = "{{ _t('Booking') }}" + ' #' + full.booking_id;
                            var row_output =
                                '<div class="d-flex justify-content-start align-items-center">' +
                                '<div class="d-flex flex-column">' +
                                '<a href="' +
                                baseUrl +
                                '" class="text-body text-truncate"><span class="fw-semibold">' +
                                name +
                                '</span></a>' +
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
                            var name = full.booking.customer.user.name,
                                image = full.customer_image ||
                                "{{ asset('assets/img/illustrations/NoImage.png') }}",
                                number = full.booking.customer.user.phone,
                                baseUrl = route('customer.edit', full.booking.customer.id);
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
                    // {
                    //   data: null,
                    //   orderable: false,
                    //   searchable: false,
                    //   render: function(data, type, full, meta) {
                    //     if (full.employee) {
                    //       var name = full.booking.status,
                    //         number = full.booking.date,
                    //         baseUrl = route('booking.edit', full.employee.id);
                    //       var row_output =
                    //         '<div class="d-flex justify-content-start align-items-center">' +
                    //         '<div class="d-flex flex-column">' +
                    //         '<a href="' +
                    //         baseUrl +
                    //         '" class="text-body text-truncate"><span class="fw-semibold">' +
                    //         name +
                    //         '</span></a>' +
                    //         '<small class="text-truncate text-muted">' +
                    //         number +
                    //         '</small>' +
                    //         '</div>' +
                    //         '</div>';
                    //     } else {
                    //       return '{{ _t('Not assigned') }}'
                    //     }
                    //     return row_output;
                    //   }
                    // },
                    // {
                    //   data: null,
                    //   orderable: false,
                    //   searchable: false,
                    //   render: function(data, type, full, meta) {
                    //     if (full.driver) {
                    //       var name = full.driver.user.name,
                    //         image = full.driver_image || "{{ asset('assets/img/illustrations/NoImage.png') }}",
                    //         number = full.driver.user.phone,
                    //         baseUrl = route('driver.edit', full.driver.id);
                    //       var row_output =
                    //         '<div class="d-flex justify-content-start align-items-center">' +
                    //         '<div class="avatar-wrapper">' +
                    //         '<div class="avatar avatar-sm me-2">' +
                    //         '<img src="' + image + '" alt="Avatar" class="rounded-circle">' +
                    //         '</div>' +
                    //         '</div>' +
                    //         '<div class="d-flex flex-column">' +
                    //         '<a href="' +
                    //         baseUrl +
                    //         '" class="text-body text-truncate"><span class="fw-semibold">' +
                    //         name +
                    //         '</span></a>' +
                    //         '<small class="text-truncate text-muted">' +
                    //         number +
                    //         '</small>' +
                    //         '</div>' +
                    //         '</div>';
                    //     } else {
                    //       return '{{ _t('Not assigned') }}'
                    //     }
                    //     return row_output;
                    //   }
                    // },
                    {
                        orderable: false,
                        searchable: false,
                        data: 'status',
                        name: 'status'
                    },
                    {
                        orderable: true,
                        searchable: true,
                        data: 'description',
                        name: 'description'
                    },
                    {
                        orderable: true,
                        searchable: false,
                        data: 'booking.date',
                        name: 'booking.date'
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
                            var deleteButton =
                                `<a href="javascript:void(0);" data-with-trashed="1" class="text-body item-delete" data-object-id=${data.id}><i class="ti ti-trash ti-sm mx-2"></i></a>`;
                            return (
                                '<div class="d-flex align-items-center">' +
                                '<a href="' + route('booking-edit-request.edit', data.id) +
                                '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                                deleteButton +
                                '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                '<a href="' + route('booking.edit', data.booking_id) +
                                '" class="dropdown-item">{{ _t('Edit Booking') }}</a>' +
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
            <h5 class="card-title mb-3">{{ _t('Bookings Edit Requests') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    <!-- <a href="{{ route('booking.create') }}" class="btn btn-primary mb-3" id="newUserButton">{{ _t('New Booking') }}</a> -->
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
                        <th>{{ _t('Booking ID') }}</th>
                        <th>{{ _t('Customer') }}</th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Description') }}</th>
                        <th>{{ _t('Booking Date') }}</th>
                        <th>{{ _t('Request Date') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
