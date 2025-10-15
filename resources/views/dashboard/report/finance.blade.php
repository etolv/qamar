@extends('layouts/layoutMaster')

@section('title', 'Finance')

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
        @if ($summary)
            <div class="card-body mt-4">
                <h5 class="card-title">{{ _t('Payment Summary by Card') }}</h5>

                @foreach (['cafeteria', 'orders', 'bookings', 'purchases', 'expenses'] as $section)
                    @php
                        $records = $summary[$section] ?? collect();
                        $sectionTotal = $records->sum('total_amount');
                    @endphp

                    @if ($records->count())
                        <div class="mt-4">
                            <h6 class="mb-2">{{ _t(ucfirst($section)) }} ({{ number_format($sectionTotal, 2) }})</h6>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ _t('Card') }}</th>
                                            <th>{{ _t('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $row)
                                            <tr>
                                                <td>{{ $row->card_name ?? _t('Cash') }}</td>
                                                <td>{{ number_format($row->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold table-active">
                                            <td>{{ _t('Total') }}</td>
                                            <td>{{ number_format($sectionTotal, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="table-responsive text-nowrap d-none">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>{{ _t('Description') }}</th>
                        <th>{{ _t('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ _t('Order Sales') }}</td>
                        <td class="text-success">{{ $finance['orderSales'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Booking Sales') }}</td>
                        <td class="text-success">{{ $finance['bookingSales'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Total Sales') }}</td>
                        <td class="text-success">{{ $finance['totalSales'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Order Expenses') }}</td>
                        <td class="text-success">{{ $finance['orderExpenses'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Booking Expenses') }}</td>
                        <td class="text-success">{{ $finance['bookingExpenses'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Total Expenses') }}</td>
                        <td class="text-success">{{ $finance['totalExpenses'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ _t('Total Purchases') }}</td>
                        <td class="text-success">{{ $finance['totalPurchases'] }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="row text-center mt-4 bg-light p-3 rounded">
                <div class="col-md-4">
                    <p class="fw-bold">{{ _t('Total Sales') }}</p>
                    <p class="fs-4 text-primary">{{ $finance['totalSales'] }}</p>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold">{{ _t('Total Expenses') }}</p>
                    <p class="fs-4 text-primary">{{ $finance['totalExpenses'] + $finance['totalPurchases'] }}</p>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold">{{ _t('Total Profit') }}</p>
                    <p class="fs-4 {{ $finance['profit'] > 0 ? 'text-success' : 'text-danger' }}">{{ $finance['profit'] }}
                    </p>
                </div>
            </div>
        </div>
        <!--  -->

    @endsection
