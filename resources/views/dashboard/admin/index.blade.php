@extends('layouts/layoutMaster')

@section('title', 'Admin List')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
        $(document).ready(function() {
            window.deleteOptions('User');

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            let role_id = getUrlParameter('role_id');
            $('.role_select2').select2({
                placeholder: '{{ _t('Filter Role') }}',
                ajax: {
                    url: route('role.search'),
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            toastr.error("You do not have permission! Please contanct admin.");
                        } else if (jqXHR.status === 501) {
                            toastr.error("Unauthorized! Please login.");
                        } else if (jqXHR.status === 500) {
                            toastr.error('Server error! Please contact support.');
                        }
                    },
                    cache: false
                }
            });
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
                    url: "{{ route('admin.fetch') }}", // Adjust the URL based on your Laravel route
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function(d) {
                        d.role_id = role_id;
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false
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
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: "status",
                        data: function(data, type, full, meta) {
                            if (data.phone == '0992990128' || data.phone == '54700800' || data
                                .phone == '9660992990128' || data.phone == '96654700800') {
                                return ``;
                            } else {
                                @can('delete_admin')
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
                                @else
                                    return '';
                                @endcan
                            }
                        },
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            let editAction = '';
                            if (data.phone != '0992990128' && data.phone != '54700800' && data
                                .phone != '9660992990128' && data.phone != '96654700800') {
                                editAction = '<a href="' + route('admin.edit', data.id) +
                                    '" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>';
                            }
                            return (
                                '<div class="d-flex align-items-center">' +
                                @can('update_admin')
                                    editAction +
                                @endcan
                                // '<a href="javascript:;" class="text-body delete-record"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                                // '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
                                // '<div class="dropdown-menu dropdown-menu-end m-0">' +
                                // '<a href="javascript:;" class="dropdown-item">View</a>' +
                                // '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                                // '</div>' +
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
                        },
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

            $('.role_select2').change(function() {
                role_id = $(this).val();
                dataTable.ajax.reload();
            });

        });
    </script>

@endsection

@section('content')
    {{--
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span>مسؤول</span>
            <div class="d-flex align-items-center my-2">
              <h3 class="mb-0 me-2">{{$statistics['admin']}}</h3>
<!-- <p class="text-success mb-0">(+29%)</p> -->
</div>
<p class="mb-0">اجمالي المستخدمين</p>
</div>
<div class="avatar">
  <span class="avatar-initial rounded bg-label-primary">
    <i class="ti ti-user ti-sm"></i>
  </span>
</div>
</div>
</div>
</div>
</div>
<div class="col-sm-6 col-xl-3">
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <span>مستشارين</span>
          <div class="d-flex align-items-center my-2">
            <h3 class="mb-0 me-2">{{$statistics['consultants']}}</h3>
            <!-- <p class="text-success mb-0">(+18%)</p> -->
          </div>
          <p class="mb-0">اجمالي المستخدمين</p>
        </div>
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-danger">
            <i class="ti ti-user-plus ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-sm-6 col-xl-3">
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <span>الأستجابة الفنية</span>
          <div class="d-flex align-items-center my-2">
            <h3 class="mb-0 me-2">{{$statistics['technical_response']}}</h3>
            <!-- <p class="text-danger mb-0">(-14%)</p> -->
          </div>
          <p class="mb-0">اجمالي المستخدمين</p>
        </div>
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-success">
            <i class="ti ti-user-check ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-sm-6 col-xl-3">
  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <span>مركز الاعمال</span>
          <div class="d-flex align-items-center my-2">
            <h3 class="mb-0 me-2">{{$statistics['business_center']}}</h3>
            <!-- <p class="text-success mb-0">(+42%)</p> -->
          </div>
          <p class="mb-0">اجمالي المستخدمين</p>
        </div>
        <div class="avatar">
          <span class="avatar-initial rounded bg-label-warning">
            <i class="ti ti-user-exclamation ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
--}}
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Admins') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    @can('create_admin')
                        <a href="{{ route('admin.create') }}" class="btn btn-primary mb-3"
                            id="newUserButton">{{ _t('New admin') }}</a>
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
                        <th>{{ _t('Name') }}</th>
                        <th>{{ _t('Phone') }}</th>
                        <th>
                            {{ _t('Role') }}
                            <select id="roleFilter" name="role_id" class="role_select2 form-select" data-allow-clear="true">
                            </select>
                        </th>
                        <th>{{ _t('Status') }}</th>
                        <th>{{ _t('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection
