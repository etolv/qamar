@extends('layouts/layoutMaster')

@section('title', 'Financial')

@section('vendor-style')

    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')

    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            let date = null;
            // $('.type_select2').select2({
            //     placeholder: '{{ _t('Filter Expenses') }}',
            //     ajax: {
            //         url: route('bill.type.search'),
            //         headers: {
            //             'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
            //         },
            //         dataType: 'json',
            //         delay: 250,
            //         processResults: function(data) {
            //             return {
            //                 results: data.data.map(function(item) {
            //                     return {
            //                         id: item.id,
            //                         text: item.name
            //                     };
            //                 })
            //             };
            //         },
            //         cache: false
            //     }
            // });

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var department = getUrlParameter('date');

            $('.datepicker').change(function() {
                date = $(this).val();
                if (date.includes('to')) {
                    var currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('date', date);
                    window.location.href = currentUrl.toString();
                }
            });
        });
    </script>

@endsection

@section('content')
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Financial') }}</h5>
            <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                {{-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ _t('CSV only support english characters use Excel for arabic') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> --}}
                <div class="col-md-8">
                    {{-- <a href="{{ route('bill.create', ['department' => request()->department]) }}"
                        class="btn btn-primary mb-3" id="newProductButton">{{ _t('New Bill') }}</a> --}}
                    <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                        <label class="form-label" for="flatpickr-range">{{ _t('Date Range') }}</label>
                        <input type="text" class="form-control datepicker" style="width: auto!important"
                            value="{{ request()->date ?? '' }}" name="date" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                            id="flatpickr-range" />
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-center">
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Department') }}</th>
                        <th>{{ _t('Count') }}</th>
                        <th>{{ _t('Amount') }}</th>
                        <th>{{ _t('Tax') }}</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0 text-center">
                    {{-- Expenses --}}
                    <tr>
                        <td colspan="5" class="bg-light">{{ _t('Expenses') }}</td>
                    </tr>
                    @php $total = $total_tax = 0 @endphp
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->name . " ({$expense->term})" }}</td>
                            <td>{{ _t($expense->department->name) }} </td>
                            <td>{{ $expense->count }}</td>
                            <td>{{ $expense->total }}</td>
                            <td>{{ $expense->tax }}</td>
                            @php
                                $total += $expense->total;
                                $total_tax += $expense->tax;
                            @endphp
                        </tr>
                    @endforeach
                    @foreach ($withdrawals as $withdrawal)
                        <tr>
                            <td>{{ _t($withdrawal->type?->name) }}</td>
                            <td>{{ _t($withdrawal->department?->name) }} </td>
                            <td>{{ $withdrawal->count_type }}</td>
                            <td>{{ $withdrawal->total }}</td>
                            <td>{{ $withdrawal->tax }}</td>
                            @php
                                $total += $withdrawal->total;
                                $total_tax += $withdrawal->tax;
                            @endphp
                        </tr>
                    @endforeach
                    @foreach ($cash_flows as $cash_flow)
                        <tr>
                            <td>{{ _t($cash_flow->type?->name) . ' (' . _t('Cash Flow') . ')' }}</td>
                            <td>{{ _t('BOTH') }} </td>
                            <td>{{ $cash_flow->count }}</td>
                            <td>{{ $cash_flow->total }}</td>
                            <td>0.00</td>
                            @php
                                $total += $cash_flow->total;
                            @endphp
                        </tr>
                    @endforeach
                    @foreach ($salaries as $salary)
                        <tr>
                            <td>{{ _t($salary->month?->name) . ' ' . _t('Salaraies') }}</td>
                            <td>{{ _t('BOTH') }} </td>
                            <td>{{ $salary->count }}</td>
                            <td>{{ $salary->total }}</td>
                            <td>0.00</td>
                            @php
                                $total += $salary->total;
                            @endphp
                        </tr>
                    @endforeach
                    <tr class="text-center">
                        <td colspan="5">{{ _t('Total') . ": {$total} | " . _t('Total Tax') . " : {$total_tax}" }}</td>
                    </tr>
                    {{-- Bills --}}
                    <tr>
                        <td colspan="5" class="bg-light">{{ _t('Purshases') }}</td>
                    </tr>
                    @php $total = $total_tax = 0 @endphp
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>{{ _t('Bill') }}</td>
                            <td>{{ _t($purchase->department->name) }} </td>
                            <td>{{ $purchase->count }}</td>
                            <td>{{ $purchase->total }}</td>
                            <td>{{ $purchase->tax }}</td>
                            @php
                                $total += $purchase->total;
                                $total_tax += $purchase->tax;
                            @endphp
                        </tr>
                    @endforeach
                    <tr class="text-center">
                        <td colspan="5">{{ _t('Total') . ": {$total} | " . _t('Total Tax') . " : {$total_tax}" }}</td>
                    </tr>
                    {{-- Orders --}}
                    <tr>
                        <td colspan="5" class="bg-light">{{ _t('Orders') }}</td>
                    </tr>
                    @php $total = $total_tax = 0 @endphp
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ _t('Salon Order') }}</td>
                            <td>{{ _t($order->department?->name) }} </td>
                            <td>{{ $order->count }}</td>
                            <td>{{ $order->total }}</td>
                            <td>{{ $order->tax }}</td>
                            @php
                                $total += $order->total;
                                $total_tax += $order->tax;
                            @endphp
                        </tr>
                    @endforeach
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>{{ _t('Booking Order') }}</td>
                            <td>{{ _t('BOOKING') }} </td>
                            <td>{{ $booking->count }}</td>
                            <td>{{ $booking->total }}</td>
                            <td>{{ $booking->tax }}</td>
                            @php
                                $total += $booking->total;
                                $total_tax += $booking->tax;
                            @endphp
                        </tr>
                    @endforeach
                    @foreach ($cafeteria_orders as $order)
                        <tr>
                            <td>{{ _t($order->type->name) }}</td>
                            <td>{{ _t('CAFETERIA') }} </td>
                            <td>{{ $order->count }}</td>
                            <td>{{ $order->total }}</td>
                            <td>{{ $order->tax }}</td>
                            @php
                                $total += $order->total;
                                $total_tax += $order->tax;
                            @endphp
                        </tr>
                    @endforeach
                    <tr class="text-center">
                        <td colspan="5">{{ _t('Total') . ": {$total} | " . _t('Total Tax') . " : {$total_tax}" }}</td>
                    </tr>
                    {{-- Returns --}}
                    @if ($order_return_services)
                        <tr>
                            <td colspan="5" class="bg-light">{{ _t('Returns') }}</td>
                        </tr>
                        @php $total = $total_tax = 0 @endphp
                        <tr>
                            <td>{{ _t('Order') }}</td>
                            <td>{{ _t('SALON') }} </td>
                            <td>{{ $order_return_services->count }}</td>
                            <td>{{ $order_return_services->total }}</td>
                            <td>{{ 0 }}</td>
                            @php
                                $total += $order_return_services->total;
                                $total_tax += $order_return_services->tax;
                            @endphp
                        </tr>
                        <tr class="text-center">
                            <td colspan="5">{{ _t('Total') . ": {$total} | " . _t('Total Tax') . " : {$total_tax}" }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!--  -->

    @endsection
