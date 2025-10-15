@extends('layouts/layoutMaster')

@section('title', 'Return order')

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
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('[class^="payment_select2_"]').select2({});
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
        });
    </script>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

            <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{ _t('Order') }} #{{ $order->id }}</h5>
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">
                    @php
                        $color = 'info';
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
                <!-- <p class="text-body"><a target="_blank" class="btn btn-primary" href="{{ route('order.pdf', $order->id) }}"><i class="fa-solid fa-file-pdf"></i></a></p> -->
                <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col">
                            <h5 class="card-title m-0">{{ _t('Services detail') }}</h5>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table class="table border-top dataTable" id="DataTables_Table_0">
                                <form method="post" action="{{ route('order-service-return.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <thead>
                                        <tr>
                                            <th class="w-25">
                                                {{ _t('Return') }}</th>
                                            <th class="w-25">
                                                {{ _t('Quantity') }}</th>
                                            <th class="w-25">
                                                {{ _t('Payment') }}</th>
                                            <th class="w-40">
                                                {{ _t('Service') }}</th>
                                            <th class="w-25">
                                                {{ _t('Type') }}</th>
                                            <th class="w-25">
                                                {{ _t('Status') }}</th>
                                            <th class="w-25">
                                                {{ _t('price') }}</th>
                                            <th class="w-15">
                                                {{ _t('qty') }}</th>
                                            <th class="w-15">
                                                {{ _t('total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderServices as $order_service)
                                            <tr class="odd">
                                                <td class="control" tabindex="0">
                                                    <input class="form-check-input"
                                                        name="services[{{ $order_service->id }}][return]" type="checkbox"
                                                        id="services[{{ $order_service->id }}][postpone]"
                                                        {{ $order_service->return ? 'disabled' : '' }} />
                                                    <!-- <input name="services[{{ $order_service->id }}][id]" type="hidden" value="{{ $order_service->id }}" /> -->
                                                </td>
                                                <td>
                                                    <input class="form-control" style="width: 100px;"
                                                        value="{{ $order_service->quantity }}"
                                                        name="services[{{ $order_service->id }}][quantity]" min="1"
                                                        max="{{ $order_service->quantity }}" type="number"
                                                        id="services[{{ $order_service->id }}][quantity]"
                                                        {{ $order_service->return ? 'disabled' : '' }} />
                                                </td>
                                                <td class="control" tabindex="0">
                                                    <select id="payment_type_{{ $order_service->id }}"
                                                        name="services[{{ $order_service->id }}][payment_type]"
                                                        class="payment_select2_{{ $order_service->id }} form-select"
                                                        data-allow-clear="false">
                                                        @php
                                                            $paymentTypes = \App\Enums\PaymentTypeEnum::cases();
                                                            $filteredPaymentTypes = array_filter(
                                                                $paymentTypes,
                                                                fn($value) => $value !==
                                                                    \App\Enums\PaymentTypeEnum::POINT,
                                                            );
                                                        @endphp
                                                        @foreach ($filteredPaymentTypes as $value)
                                                            <option value="{{ $value->value }}">{{ _t($value->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <!-- <td class="  dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input"></td> -->
                                                <td class="sorting_1">
                                                    <div
                                                        class="d-flex justify-content-start align-items-center text-nowrap">
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
                                                <td><span>{{ _t($order_service->type->name) }}</span></td>
                                                <td><span>{{ _t($order_service->status->name) }}</span></td>
                                                <td><span>{{ 'SAR ' . $order_service->price }}</span></td>
                                                <td><span class="text-body">{{ $order_service->quantity }}</span></td>
                                                <td>
                                                    <h6 class="mb-0 text-nowrap">
                                                        {{ 'SAR ' . $order_service->price * $order_service->quantity }}
                                                    </h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="odd">
                                            <td colspan="4">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ _t('Return') }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    {{-- <thead>
                                    <tr>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;" aria-label="qty">{{ _t('Session Count') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;" aria-label="qty">{{ _t('Left Sessions') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 53px;" aria-label="total">{{ _t('Due Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd">
                                        <td><span class="text-body">{{ $order_service->session_count }}</span></td>
                                        <td><span class="text-body">{{ $order_service->session_count - count($order_service->sessions) }}</span></td>
                                        <td>
                                            <h6 class="mb-0 text-nowrap">{{ $order_service->due_date }}</h6>
                                        </td>
                                    </tr>
                                </tbody>  --}}
                                </form>
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
@endsection
