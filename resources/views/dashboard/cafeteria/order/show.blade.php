@extends('layouts/layoutMaster')

@section('title', 'Order')

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

        .rating-color {
            color: #fbc634 !important;
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
            $('.employee_select2').select2({
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
            $('.print-button').click(function(event) {
                event.preventDefault();
                window.print();
            });
            $('#editEmployeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var service_id = button.data('id');
                var modal = $(this);
                modal.find('#order_service_id').val(service_id);
            });
            // let finalURL =
            //     'https://chart.googleapis.com/chart?cht=qr&chl=' +
            //     "{{ $order->id }}" +
            //     '&chs=160x160&chld=L|0'
            // $('.qr-code').attr('src', finalURL);
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

                    <span>{{ _t('Status') }}:</span> <span
                        class="badge bg-label-{{ $color }}">{{ $order->status?->name }}</span>
                    <span>{{ _t('Payment status') }}:</span> <span
                        class="badge bg-label-{{ $payment_color }}">{{ $order->payment_status?->name }}</span>
                </h5>
                <p class="text-body">{{ $order->date }}</p>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-2">
                <p class="text-body"><a target="_blank" class="btn btn-primary" title="{{ _t('Download PDF') }}"
                        href="{{ route('order.pdf', $order->id) }}" download><i class="fa-solid fa-file-pdf"></i></a></p>
                {{-- <p class="text-body"><a target="_blank" class="btn btn-warning" title="{{ _t('Rate') }}"
                        href="{{ route('order.rate', $order->id) }}"><i class="fa-regular fa-star"></i> </a></p> --}}
                <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col">
                            <h5 class="card-title m-0">{{ _t('Order details') }}</h5>
                        </div>
                        <!-- <div class="col">
                                                                                                                                                <div class="mb-1 mt-1">
                                                                                                                                                    <div class="col-sm-10">
                                                                                                                                                        <label class="col-sm-2 col-form-label text-nowrap" for="status">{{ _t('Status') }}</label>
                                                                                                                                                        <select id="status" name="status" class="status_select2 form-select" data-allow-clear="false">
                                                                                                                                                            @foreach ([] as $status)
    <option value="{{ $status->value }}" {{ $order->status == $status ? 'selected' : '' }}>{{ $status->name }}</option>
    @endforeach
                                                                                                                                                        </select>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div> -->
                        <!-- <div class="col"> -->
                        <!-- <div class="mb-1 mt-1"> -->
                        <!-- <div class="col-sm-10"> -->
                        <!-- <label class="col-sm-2 col-form-label text-nowrap" for="status">{{ _t('Delivery') }}</label> -->
                        <!-- <select id="delivery" name="delivery" class="delivery_select2 form-select" data-allow-clear="false"> -->
                        <!-- @foreach ([] as $status)
    -->
                        <!-- <option value="{{ $status->value }}" {{ $order->delivery_status == $status ? 'selected' : '' }}>{{ $status->name }}</option> -->
                        <!--
    @endforeach -->
                        <!-- </select> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- </div> -->
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
                                            style="width: 239px;" aria-label="products">{{ _t('product') }}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;"
                                            aria-label="price">{{ _t('price') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;"
                                            aria-label="qty">{{ _t('qty') }}</th>
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 53px;"
                                            aria-label="total">{{ _t('total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($order->cafeteriaOrderStocks as $orderStock)
                                        <tr class="odd">
                                            <td class="  control" tabindex="0" style="display: none;"></td>
                                            <!-- <td class="  dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input"></td> -->
                                            <td class="sorting_1">
                                                <div class="d-flex justify-content-start align-items-center text-nowrap">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar me-2"><img
                                                                src="{{ $orderStock->stock->product->getFirstMedia('image') ? $orderStock->stock->product->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png') }}"
                                                                alt="" class="rounded-2"></div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="text-body mb-0 text-wrap">
                                                            {{ Illuminate\Support\Str::limit($orderStock->stock->product->name, 30, '...') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span>{{ 'SAR ' . $orderStock->price }}</span></td>
                                            <td><span class="text-body">{{ $orderStock->quantity }}</span></td>
                                            <td>
                                                <h6 class="mb-0 text-nowrap">
                                                    {{ 'SAR ' . $orderStock->price * $orderStock->quantity }}</h6>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 94px;" aria-label="price">{{ _t('Quantity') }}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 94px;" aria-label="price">{{ _t('price') }}</th>
                                        {{-- <th class="w-15 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 85px;" aria-label="qty">qty</th> --}}
                                        <th class="w-15 sorting_disabled" rowspan="1" colspan="1"
                                            style="width: 53px;" aria-label="total">total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->cafeteriaOrderServices as $orderService)
                                        <tr class="odd">
                                            <td class="  control" tabindex="0" style="display: none;"></td>
                                            <!-- <td class="  dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input"></td> -->
                                            <td class="sorting_1">
                                                <div class="d-flex justify-content-start align-items-center text-nowrap">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar me-2"><img
                                                                src="{{ $orderService->service->getFirstMedia('image') ? $orderService->service->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png') }}"
                                                                alt="" class="rounded-2"></div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="text-body mb-0 text-wrap">
                                                            {{ Illuminate\Support\Str::limit($orderService->service->name, 30, '...') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-body">{{ $orderService->quantity }}</span></td>
                                            <td><span>{{ 'SAR ' . $orderService->price }}</span></td>
                                            <td>
                                                <h6 class="mb-0 text-nowrap">
                                                    {{ 'SAR ' . $orderService->price * $orderService->quantity }}</h6>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($order->coupon)
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                <table class="datatables-order-details table border-top dataTable no-footer dtr-column"
                                    id="DataTables_Table_0" style="width: 685px;">
                                    <thead>
                                        <tr>
                                            <th class="control sorting_disabled dtr-hidden" rowspan="1" colspan="1"
                                                style="width: 0px; display: none;" aria-label=""></th>
                                            <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 239px;" aria-label="products">{{ _t('Coupon') }}</th>
                                            <th class="w-25 sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 94px;" aria-label="price">{{ _t('Code') }}</th>
                                            <th class="w-15 sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 85px;" aria-label="qty">{{ _t('Type') }}</th>
                                            <th class="w-15 sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 53px;" aria-label="total">{{ _t('Value') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="odd">
                                            <td class="  control" tabindex="0" style="display: none;"></td>
                                            <td><span>{{ $order->coupon->name }}</span></td>
                                            <td><span class="text-body">{{ $order->coupon->code }}</span></td>
                                            <td>
                                                <h6 class="mb-0 text-nowrap">
                                                    {{ $order->coupon->is_percentage ? _t('Percentage') : _t('Fixed') }}
                                                </h6>
                                            </td>
                                            <td><span class="text-body">{{ $order->coupon->discount }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="d-flex justify-content-end align-items-center m-3 mb-2 p-1">
                            <div class="order-calculations">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="w-px-100 text-heading">{{ _t('Subtotal') }}:</span>
                                    <h6 class="mb-0">SAR {{ $order->total }}</h6>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <small>({{ $order->tax_included ? _t('Tax Included') : _t('Tax Not Included') }})</small>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="w-px-100 text-heading">{{ _t('Tax') }}:</span>
                                    <h6 class="mb-0">SAR {{ $order->tax }}</h6>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="w-px-100 text-heading">{{ _t('Discount') }}:</span>
                                    <h6 class="mb-0">SAR {{ $order->discount }}</h6>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6 class="w-px-100 mb-0">{{ _t('Grand Total') }}:</h6>
                                    <h6 class="mb-0">SAR {{ $order->grand_total }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (count($order->payments))
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col">
                                <h5 class="card-title m-0">{{ _t('Order Payments') }}</h5>
                            </div>
                        </div>
                        <div class="card-datatable table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                <table class="datatables-order-details table border-top dataTable no-footer dtr-column"
                                    id="DataTables_Table_0">
                                    <thead>
                                        <tr>
                                            <th class="control sorting_disabled" rowspan="1" colspan="1"
                                                aria-label="">{{ _t('Type') }}</th>
                                            <th class="control sorting_disabled" rowspan="1" colspan="1"
                                                aria-label="">{{ _t('Status') }}</th>
                                            <th class="control sorting_disabled" rowspan="1" colspan="1"
                                                aria-label="">{{ _t('Amount') }}</th>
                                            <th class="control sorting_disabled" rowspan="1" colspan="1"
                                                aria-label="">{{ _t('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->payments as $payment)
                                            <tr class="odd">
                                                <td class="control" tabindex="0">{{ _t($payment->type->name) }}</td>
                                                <td class="control" tabindex="0">{{ _t($payment->status->name) }}</td>
                                                <td class="control" tabindex="0">{{ $payment->amount }}</td>
                                                <td class="control" tabindex="0">
                                                    {{ Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title m-0">{{ _t('Order statuses') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="timeline pb-0 mb-0">
                            @if (!in_array($order->status->value, [5, 6]))
                                <li
                                    class="timeline-item timeline-item-transparent {{ $order->status->value >= 1 ? 'border-primary' : 'border-transparent pb-0' }}">
                                    <span
                                        class="timeline-point {{ $order->status->value >= 1 ? 'timeline-point-primary' : 'timeline-point-secondary pb-0' }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t('Pending') }}</h6>
                                            <span
                                                class="text-muted">{{ $order->status->value >= 1 ? _t('DONE') : _t('PENDING') }}</span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value >= 1 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                                <li
                                    class="timeline-item timeline-item-transparent {{ $order->status->value >= 2 ? 'border-primary' : 'border-transparent pb-0' }}">
                                    <span
                                        class="timeline-point {{ $order->status->value >= 2 ? 'timeline-point-primary' : 'timeline-point-secondary pb-0' }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t('Accepted') }}</h6>
                                            <span
                                                class="text-muted">{{ $order->status->value >= 2 ? _t('DONE') : _t('PENDING') }}</span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value >= 2 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                                <li
                                    class="timeline-item timeline-item-transparent {{ $order->status->value >= 3 ? 'border-primary' : 'border-transparent pb-0' }}">
                                    <span
                                        class="timeline-point {{ $order->status->value >= 3 ? 'timeline-point-primary' : 'timeline-point-secondary pb-0' }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t('Confirmed') }}</h6>
                                            <span
                                                class="text-muted">{{ $order->status->value >= 3 ? _t('DONE') : _t('PENDING') }}</span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value >= 3 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                                <li
                                    class="timeline-item timeline-item-transparent {{ $order->status->value >= 4 ? 'border-primary' : 'border-transparent pb-0' }}">
                                    <span
                                        class="timeline-point {{ $order->status->value >= 4 ? 'timeline-point-primary' : 'timeline-point-secondary pb-0' }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t('Started') }}</h6>
                                            <span
                                                class="text-muted">{{ $order->status->value >= 4 ? _t('DONE') : _t('PENDING') }}</span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value >= 4 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                                <li
                                    class="timeline-item timeline-item-transparent {{ $order->status->value == 7 ? 'border-primary' : 'border-transparent pb-0' }}">
                                    <span
                                        class="timeline-point {{ $order->status->value == 7 ? 'timeline-point-primary' : 'timeline-point-secondary pb-0' }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t('Completed') }}</h6>
                                            <span
                                                class="text-muted">{{ $order->status->value == 7 ? _t('DONE') : _t('PENDING') }}</span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value == 7 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                            @else
                                <li class="timeline-item timeline-item-transparent border-danger">
                                    <span class="timeline-point timeline-point-danger"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ _t($order->status->name) }}</h6>
                                            <span class="text-muted"></span>
                                        </div>
                                        <!-- <p class="mt-2">{{ $order->status->value == 7 ? _t('DONE') : _t('PENDING') }}</p> -->
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div> --}}
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{ _t('Customer details') }}</h6>
                    </div>
                    @if ($order->type == App\Enums\OrderableTypeEnum::CUSTOMER)
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
                                    <small class="text-muted">{{ _t('Customer ID') }}:
                                        #{{ $order->customer_id }}</small>
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
                            <p class=" mb-1">{{ _t('State') }}: {{ $order->customer->city->state->name }}</p>
                            <p class=" mb-1">{{ _t('City') }}: {{ $order->customer->city->name }}</p>
                            <p class=" mb-0">{{ _t('Mobile') }}: {{ $order->customer->user->phone }}</p>
                        </div>
                    @else
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5>{{ _t($order->type->name) }}</h5>
                                <!-- <h6><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editUser">Edit</a></h6> -->
                            </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
