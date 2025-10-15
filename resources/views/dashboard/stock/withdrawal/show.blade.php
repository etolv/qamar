@extends('layouts/layoutMaster')

@section('title', 'Order')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/leaflet/leaflet.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/toastr/toastr.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/toastr/toastr.js',
'resources/assets/vendor/libs/leaflet/leaflet.js',
])
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
        $('.payment_select2').select2({});
        $('.print-button').click(function(event) {
            event.preventDefault();
            window.print();
        })
    });
</script>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

        <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
            <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{$bill->identifier}}</h5>
            <p class="text-body">{{Carbon\Carbon::parse($bill->created_at)->format('Y-m-d H:i')}}</p>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-2">
            <!-- <p class="text-body"><a target="_blank" class="btn btn-primary" title="{{_t('Download PDF')}}" href="{{route('order.pdf', $bill->id)}}"><i class="fa-solid fa-file-pdf"></i></a></p> -->
            <p class="text-body"><a target="_blank" class="btn btn-primary print-button" title="{{_t('Print')}}" href="#"><i class="fa-solid fa-print"></i></a></p>
            <!-- <p class="text-body"><a target="_blank" class="btn btn-warning" title="{{_t('Rate')}}" href="{{route('order.rate', $bill->id)}}"><i class="fa-regular fa-star"></i> </a></p> -->
            <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
        </div>
    </div>

    <!-- Order Details Table -->

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h5 class="card-title m-0">{{_t('Bill details')}}</h5>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="datatables-order-details table border-top dataTable no-footer dtr-column" id="DataTables_Table_0">
                            <thead>
                                <tr>
                                    <th class="control sorting_disabled dtr-hidden" rowspan="1" colspan="1" style="width: 0px; display: none;" aria-label=""></th>
                                    <!-- <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 18px;" data-col="1" aria-label=""><input type="checkbox" class="form-check-input"></th> -->
                                    <th class="w-40 sorting_disabled" rowspan="1" colspan="1" style="width: 239px;" aria-label="products">{{_t('product')}}</th>
                                    <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;" aria-label="price">{{_t('Purchase Unit')}}</th>
                                    <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;" aria-label="price">{{_t('Purchase Unit')}}</th>
                                    <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;" aria-label="price">{{_t('Purchase Price')}}</th>
                                    <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;" aria-label="price">{{_t('Exchange Price')}}</th>
                                    <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 94px;" aria-label="price">{{_t('Sell Price')}}</th>
                                    <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 85px;" aria-label="qty">{{_t('qty')}}</th>
                                    <th class="w-15 sorting_disabled" rowspan="1" colspan="1" style="width: 53px;" aria-label="total">{{_t('total')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($bill->billProducts as $billProduct)

                                <tr class="odd">
                                    <td class="  control" tabindex="0" style="display: none;"></td>
                                    <!-- <td class="  dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input"></td> -->
                                    <td class="sorting_1">
                                        <div class="d-flex justify-content-start align-items-center text-nowrap">
                                            <div class="avatar-wrapper">
                                                <div class="avatar me-2"><img src="{{$billProduct->product->getFirstMedia('image') ? $billProduct->product->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png')}}" alt="" class="rounded-2"></div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <h6 class="text-body mb-0 text-wrap">{{ Illuminate\Support\Str::limit($billProduct->product->name, 30, '...') }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span>{{ $billProduct->unit_purchase->name }}</span></td>
                                    <td><span>{{ $billProduct->unit_retail->name }}</span></td>
                                    <td><span>{{ 'SAR ' . $billProduct->purchase_price }}</span></td>
                                    <td><span>{{ 'SAR ' . $billProduct->exchange_price }}</span></td>
                                    <td><span>{{ 'SAR ' . $billProduct->sell_price }}</span></td>
                                    <td><span class="text-body">{{ $billProduct->quantity }}</span></td>
                                    <td>
                                        <h6 class="mb-0 text-nowrap">{{ 'SAR ' . ($billProduct->purchase_price * $billProduct->quantity) }}</h6>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end align-items-center m-3 mb-2 p-1">
                        <div class="order-calculations">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="w-px-100 text-heading">{{_t('Total')}}:</span>
                                <h6 class="mb-0">SAR {{$bill->total}}</h6>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="w-px-100 text-heading">{{_t('Initial Payment')}}:</span>
                                <h6 class="mb-0">SAR {{$bill->paid}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($bill->payments))
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h5 class="card-title m-0">{{_t('Bill Payments')}}</h5>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="datatables-order-details table border-top dataTable no-footer dtr-column" id="DataTables_Table_0">
                            <thead>
                                <tr>
                                    <th class="control sorting_disabled" rowspan="1" colspan="1" aria-label="">{{_t('Type')}}</th>
                                    <th class="control sorting_disabled" rowspan="1" colspan="1" aria-label="">{{_t('Status')}}</th>
                                    <th class="control sorting_disabled" rowspan="1" colspan="1" aria-label="">{{_t('Amount')}}</th>
                                    <th class="control sorting_disabled" rowspan="1" colspan="1" aria-label="">{{_t('Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->payments as $payment)
                                <tr class="odd">
                                    <td class="control" tabindex="0">{{_t($payment->type->name)}}</td>
                                    <td class="control" tabindex="0">{{_t($payment->status->name)}}</td>
                                    <td class="control" tabindex="0">{{$payment->amount}}</td>
                                    <td class="control" tabindex="0">{{Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end align-items-center m-3 mb-2 p-1">
                        @php
                        $paid_amount = $bill->payments->sum('amount');
                        $left_dept = $bill->total - $paid_amount;
                        @endphp
                        <div class="order-calculations">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="w-px-100 text-heading">{{_t('Total')}}:</span>
                                <h6 class="mb-0">SAR {{$paid_amount}}</h6>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="w-px-100 text-heading">{{_t('Left dept')}}:</span>
                                <h6 class="mb-0">SAR {{$left_dept}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title m-0">{{_t('Supplier details')}}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start align-items-center mb-4">
                        <div class="avatar me-2">
                            <img src="{{$bill->supplier->getFirstMedia('profile') ? $order->supplier->getFirstMediaUrl('profile') :asset('assets/img/illustrations/NoImage.png')   }}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{route('supplier.edit', $bill->supplier_id)}}" class="text-body text-nowrap">
                                <h6 class="mb-0">{{$bill->supplier->name}}</h6>
                            </a>
                            <small class="text-muted">{{_t('Supplier ID')}}: #{{$bill->supplier_id}}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center mb-4">
                        <span class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i class="ti ti-shopping-cart ti-sm"></i></span>
                        <h6 class="text-body text-nowrap mb-0">{{$bill->supplier->bills->count()}} {{_t('Bills')}}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6>{{_t('Contact info')}}</h6>
                        <!-- <h6><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editUser">Edit</a></h6> -->
                    </div>
                    <p class=" mb-1">{{_t('Address')}}: {{$bill->supplier->address}}</p>
                    <p class=" mb-1">{{_t('Phone')}}: {{$bill->supplier->phone}}</p>
                    <p class=" mb-1">{{_t('Email')}}: {{$bill->supplier->email}}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h6 class="card-title m-0">{{_t('QR')}}</h6>
                    <!-- <h6 class="m-0"><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addNewAddress">Edit</a></h6> -->
                </div>
                <div class="card-body">
                    <img src="https://www.qr-genereren.nl/qrcode.png?text={{route('bill.show', $bill->id)}}&fore…0&backgroundColor=ffffff&moduleSize=16&padding=0&" class="img-thumbnail img-responsive" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-onboarding modal fade animate__animated" id="newPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{_t('New Payment')}}</h4>
                        <form id="newPaymentForm" method="POST" action="{{route('bill.payment.store', $bill->id)}}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Type')}} *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select id="payment_type" name="type" class="payment_select2 form-select" data-allow-clear="false" required>
                                            @php
                                            $paymentTypes = \App\Enums\PaymentTypeEnum::cases();
                                            $filteredPaymentTypes = array_filter($paymentTypes, fn($value) => $value !== \App\Enums\PaymentTypeEnum::POINT);
                                            @endphp
                                            @foreach($filteredPaymentTypes as $value)
                                            <option value="{{$value->value}}">{{_t($value->name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('type')
                                    <span class="text-danger">{{$message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Amount')}} *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="fa-solid fa-usdollar-sign"></i></span>
                                        <input type="number" min="1" max="{{$left_dept}}" name="amount" id="total-repeater-1-2" value="0" class="form-control total-repeater-1" placeholder="{{_t('Amount')}}" required />
                                    </div>
                                    @error('phone')
                                    <span class="text-danger">{{$message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">{{_t('Create')}}</button>
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



</div>
@endsection