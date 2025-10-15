@extends('layouts/layoutMaster')

@section('title', 'New Session')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-style')
    <style>
        .qr-code {
            max-width: 200px;
            margin: 10px;
        }

        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            function initEmployeeSelect2(identifier) {
                $(identifier).select2({
                    placeholder: '{{ _t('Select Employees') }}',
                    ajax: {
                        url: route('employee.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                // type: 'orders_hairdresser' // your custom parameter
                            };
                        },
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
            }
            initEmployeeSelect2('.employee_select2_2');
            initEmployeeSelect2('.employee_select2');
            $('[class^="session_select2"]').select2({});
            $('[class^="status_select2"]').select2({});
            $('[class^="status_select2"]').on('change', function() {
                // if (allowChangeEvent) {
                var selectedStatus = $(this).val();
                var id = $(this).data('id');
                console.log('id', id);
                console.log('selectedStatus', selectedStatus);
                sendApiRequest(selectedStatus, id);
                // }
            });

            function sendApiRequest(selectedValue, id) {
                var formData = new FormData();
                var formData = {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'status': selectedValue,
                };
                $.ajax({
                    url: route('order-service-session.update', id), // Replace with your actual API endpoint
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        toastr.success("{{ _t('Status changed') }}");
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                        console.error(error.responseJSON.message);
                    }
                });
            }
            $('#editEmployeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var session_id = button.data('id');
                var modal = $(this);
                modal.find('#session_id').val(session_id);
            });
        });
    </script>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

            <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{ _t('Order') }}
                    #{{ $order_service->order_id }}</h5>
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{ _t('Service') }} #{{ $order_service->id }}
                </h5>
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">
                    @php
                        $color = 'info';
                        $order = $order_service->order;
                        if (in_array($order->status->name, ['CANCELLED', 'REJECTED'])) {
                            $color = 'danger';
                        } elseif (in_array($order->status->name, ['COMPLETED'])) {
                            $color = 'success';
                        }
                        $payment_color = 'info';
                        if ($order->payment_status->name == 'PAID') {
                            $payment_color = 'success';
                        } elseif ($order->payment_status->name == 'UNPAID') {
                            $payment_color = 'danger';
                        }
                    @endphp

                    <span>{{ _t('Order Status') }}:</span> <span
                        class="badge bg-label-{{ $color }}">{{ $order->status?->name }}</span>
                </h5>
                <p class="text-body">{{ $order->created_at }}</p>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-2">
                <p class="text-body"><a target="_blank" class="btn btn-primary"
                        href="{{ route('order-service-session.pdf', $order_service->id) }}" download><i
                            class="fa-solid fa-file-pdf"></i></a>
                </p>
                <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col">
                            <h5 class="card-title m-0">{{ _t('Service detail') }}</h5>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table class="datatables-order-details table border-top dataTable no-footer dtr-column"
                                id="DataTables_Table_0">
                                <thead>
                                    <tr>
                                        <th class="control sorting_disabled dtr-hidden" rowspan="1" colspan="1"
                                            style="width: 0px; display: none;" aria-label=""></th>
                                        <!-- <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 18px;" data-col="1" aria-label=""><input type="checkbox" class="form-check-input"></th> -->
                                        <th class="w-40 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 239px;" aria-label="products">{{ _t('Service') }}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;"
                                            aria-label="price">price</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;"
                                            aria-label="qty">qty</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 53px;"
                                            aria-label="total">total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd">
                                        <td class="  control" tabindex="0" style="display: none;"></td>
                                        <!-- <td class="  dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input"></td> -->
                                        <td class="sorting_1">
                                            <div class="d-flex justify-content-start align-items-center text-nowrap">
                                                <div class="avatar-wrapper">
                                                    <div class="avatar me-2"><img
                                                            src="{{ $order_service->service->getFirstMedia('image') ? $order_service->service->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png') }}"
                                                            alt="" class="rounded-2"></div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="text-body mb-0 text-wrap">
                                                        {{ Illuminate\Support\Str::limit($order_service->service->name, 30, '...') }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span>{{ 'SAR ' . $order_service->price }}</span></td>
                                        <td><span class="text-body">{{ $order_service->quantity }}</span></td>
                                        <td>
                                            <h6 class="mb-0 text-nowrap">
                                                {{ 'SAR ' . $order_service->price * $order_service->quantity }}</h6>
                                        </td>
                                    </tr>
                                </tbody>
                                <thead>
                                    <tr>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;"
                                            aria-label="qty">{{ _t('Session Count') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;"
                                            aria-label="qty">{{ _t('Left Sessions') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 53px;"
                                            aria-label="total">{{ _t('Due Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd">
                                        <td><span class="text-body">{{ $order_service->session_count }}</span></td>
                                        <td><span
                                                class="text-body">{{ $order_service->session_count - count($order_service->sessions) }}</span>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 text-nowrap">{{ $order_service->due_date }}</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col">
                            <h5 class="card-title m-0">{{ _t('Sessions') }}</h5>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table class="datatables-order-details table border-top dataTable no-footer dtr-column"
                                id="DataTables_Table_0">
                                <thead>
                                    <tr>
                                        <th class="w-20 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 5px;" aria-label="#">#</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 94px;" aria-label="date">{{ _t('Date') }}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 94px;" aria-label="employee">{{ _t('Employee') }}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 85px;" aria-label="status">{{ _t('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order_service->sessions as $index => $session)
                                        <tr class="odd">
                                            <td style="width: 20px;">{{ $index + 1 }}</td>
                                            <td><span
                                                    class="text-body">{{ Carbon\Carbon::parse($session->date)->format('Y-m-d') }}</span>
                                            </td>
                                            <td>
                                                @if ($session->employee)
                                                    <div class="d-flex justify-content-start align-items-center mb-4">
                                                        <div class="avatar me-2">
                                                            <img src="{{ $session->employee->user->getFirstMedia('profile') ? $session->employee->user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png') }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <a href="#" class="text-body text-nowrap"
                                                                data-bs-toggle="modal" data-id="{{ $session->id }}"
                                                                data-bs-target="#editEmployeeModal">
                                                                <h6 class="mb-0" id="basic-icon-default-profit"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-custom-class="tooltip-primary"
                                                                    title="{{ _t('Click to change session employee') }}">
                                                                    {{ $session->employee->user->name }}
                                                                </h6>
                                                            </a>
                                                            <small
                                                                class="text-muted">{{ $session->employee?->user?->phone }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="#" class="text-body text-nowrap"
                                                        data-bs-toggle="modal" data-id="{{ $session->id }}"
                                                        data-bs-target="#editEmployeeModal">
                                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-custom-class="tooltip-primary"
                                                            title="{{ _t('Click to add session employee') }}">{{ _t('Not Assigned') }}</span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <select id="status" name="status" data-id="{{ $session->id }}"
                                                    class="status_select2_{{ $index }} form-select"
                                                    data-allow-clear="false" required>
                                                    @foreach (App\Enums\SessionStatusEnum::cases() as $session_status)
                                                        <option value="{{ $session_status->value }}"
                                                            {{ $session->status->value == $session_status->value ? 'selected' : '' }}>
                                                            {{ _t($session_status->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="odd">
                                        <td colspan="4">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#newSessionModal">
                                                {{ _t('New Session') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{ _t('Customer details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="{{ $order->customer->user->getFirstMedia('profile') ? $order->customer->user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png') }}"
                                    alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="d-flex flex-column">
                                <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/user/view/account"
                                    class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ $order->customer->user->name }}</h6>
                                </a>
                                <small class="text-muted">{{ _t('Customer ID') }}: #{{ $order->customer_id }}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <span
                                class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i
                                    class="ti ti-shopping-cart ti-sm"></i></span>
                            <h6 class="text-body text-nowrap mb-0">{{ $order->customer->orders->count() }}
                                {{ _t('Orders') }}</h6>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <span
                                class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i
                                    class="ti ti-package ti-sm"></i></span>
                            <h6 class="text-body text-nowrap mb-0">{{ $order->customer->bookings->count() }}
                                {{ _t('Bookings') }}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>{{ _t('Contact info') }}</h6>
                            <!-- <h6><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editUser">Edit</a></h6> -->
                        </div>
                        <p class=" mb-1">{{ _t('City') }}: {{ $order->customer->city->name }}</p>
                        <p class=" mb-0">{{ _t('Mobile') }}: {{ $order->customer->user->phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-onboarding modal fade animate__animated" id="newSessionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('New Session') }}</h4>
                        <form method="POST" action="{{ route('order-service-session.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $order_service->id }}" name="order_service_id" />
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Status') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-color-swatch"></i></span>
                                        <div class="col-sm-10">
                                            <select id="status" name="status" class="session_select2 form-select"
                                                data-allow-clear="false" required>
                                                @foreach (App\Enums\SessionStatusEnum::cases() as $session_status)
                                                    <option value="{{ $session_status->value }}">
                                                        {{ _t($session_status->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class=" row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Employee') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                                        <div class="col-sm-10">
                                            <select id="employee_id" name="employee_id"
                                                class="employee_select2 form-select" data-allow-clear="false">
                                            </select>
                                        </div>
                                    </div>
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Date') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-image"></i></span>
                                        <input type="date" name="date"
                                            value="{{ old('date') ?? Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="basic-icon-default-email" class="form-control"
                                            aria-describedby="basic-icon-default-email2" />
                                    </div>
                                    @error('date')
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

    <!-- Edit Employee Modal -->
    <div class="modal-onboarding modal fade animate__animated" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Edit Session Employee') }}</h4>
                        <form method="POST" action="{{ route('order-service-session.update-employee') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="" name="session_id" id="session_id" />
                            <div class=" row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Employee') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                                        <div class="col-sm-10">
                                            <select id="employee_id_2" name="employee_id"
                                                class="employee_select2_2 form-select" data-allow-clear="false" required>
                                            </select>
                                        </div>
                                    </div>
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
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

    <!--/ Edit Employee Modal -->
@endsection
