@extends('layouts/layoutMaster')

@section('title', 'Rate order')

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
</style>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        $('[class^="reason_select2_"]').select2({
            placeholder: '{{_t("Select Reason")}}',
            ajax: {
                url: route('rate.reason.search'),
                headers: {
                    'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(function(item) {
                            console.log(item);
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

        <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
            <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">{{_t('Order')}} #{{$order->id}}</h5>
            <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">
                @php
                $color = "info";
                if(in_array($order->status->name, ['CANCELLED', 'REJECTED'])) {
                $color = "danger";
                } else if(in_array($order->status->name, ['COMPLETED'])) {
                $color = "success";
                }
                $payment_color = "info";
                if($order->payment_status->name == "PAID") {
                $payment_color = "success";
                } else if($order->payment_status->name == "UNPAID") {
                $payment_color = "danger";
                }
                @endphp

                <span>{{_t('Order Status')}}:</span> <span class="badge bg-label-{{$color}}">{{$order->status?->name}}</span>
            </h5>
            <p class="text-body">{{$order->created_at}}</p>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-2">
            <p class="text-body"><a class="btn btn-primary" title="{{_t('Show')}}" href="{{route('order.show', $order->id)}}"><i class="fa-regular fa-eye"></i></a></p>
            <!-- <button class="btn btn-label-danger delete-order waves-effect">Delete Order</button> -->
        </div>
    </div>

    <!-- Order Details Table -->

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h5 class="card-title m-0">{{_t('Rate detail')}}</h5>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="datatables-order-details table border-top dataTable no-footer dtr-column" id="DataTables_Table_0">
                            <form method="post" action="{{route('order.rate.submit', $order->id)}}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <thead>
                                    <tr>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 100px;" aria-label="">{{_t('Type')}}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 100px;" aria-label="">{{_t('Rate')}}</th>
                                        <th class="w-25 sorting_disabled" rowspan="1" colspan="1" style="width: 100px;" aria-label="">{{_t('Reason')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(App\Enums\RateTypeEnum::cases() as $index => $rate)
                                    @php
                                    $order_rate = $order->rates->where('type', $rate)->first();
                                    @endphp
                                    <tr class="odd">
                                        <td class="control" tabindex="0">
                                            <input type="hidden" name="rates[{{$rate->value}}][type]" value="{{$rate->value}}">
                                            {{_t($rate->name)}}
                                        </td>
                                        <td class="control" tabindex="0">
                                            <input type="number" min="1" max="5" class="form-control" name="rates[{{$rate->value}}][rate]" id="rates[{{$rate->value}}][rate]" placeholder="{{_t('Rate')}}" value="{{$order_rate?->rate ?? 0}}" />
                                        </td>
                                        <td class="control" tabindex="0">
                                            <select id="rates[{{$rate->value}}][rate_reason_id]" name="rates[{{$rate->value}}][rate_reason_id]" class="reason_select2_{{$index}} form-select" data-allow-clear="true">
                                                @if($order_rate?->rate_reason_id)
                                                <option value="{{$order_rate->rate_reason_id}}">{{$order_rate->reason?->name}}</option>
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr class="odd">
                                        <td colspan="4">
                                            <button type="submit" class="btn btn-primary">
                                                {{_t('Submit')}}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title m-0">{{_t('Customer details')}}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start align-items-center mb-4">
                        <div class="avatar me-2">
                            <img src="{{$order->customer->user->getFirstMedia('profile') ? $order->customer->user->getFirstMediaUrl('profile') :asset('assets/img/illustrations/NoImage.png')   }}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="d-flex flex-column">
                            <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/user/view/account" class="text-body text-nowrap">
                                <h6 class="mb-0">{{$order->customer->user->name}}</h6>
                            </a>
                            <small class="text-muted">{{_t('Customer ID')}}: #{{$order->customer_id}}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center mb-4">
                        <span class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i class="ti ti-shopping-cart ti-sm"></i></span>
                        <h6 class="text-body text-nowrap mb-0">{{$order->customer->orders->count()}} {{_t('Orders')}}</h6>
                    </div>
                    <div class="d-flex justify-content-start align-items-center mb-4">
                        <span class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i class="ti ti-package ti-sm"></i></span>
                        <h6 class="text-body text-nowrap mb-0">{{$order->customer->bookings->count()}} {{_t('Bookings')}}</h6>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h6>{{_t('Contact info')}}</h6>
                        <!-- <h6><a href=" javascript:void(0)" data-bs-toggle="modal" data-bs-target="#editUser">Edit</a></h6> -->
                    </div>
                    <p class=" mb-1">{{_t('State')}}: {{$order->customer->city->state->name}}</p>
                    <p class=" mb-1">{{_t('City')}}: {{$order->customer->city->name}}</p>
                    <p class=" mb-0">{{_t('Mobile')}}: {{$order->customer->user->phone}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection