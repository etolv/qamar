@extends('layouts/layoutMaster')

@section('title', 'Ledger')

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
            $('.account_select2').select2({});

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var account_id = getUrlParameter('account_id');
            let date = getUrlParameter('date');

            const flatpickrRange = document.querySelector('#flatpickr-range');
            if (typeof flatpickrRange != undefined) {
                flatpickrRange.flatpickr({
                    mode: 'range'
                });
            }

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
            <h5 class="card-title mb-3">دفتر الأستاذ (Ledger)</h5>
            <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-8">
                    <div class="d-flex justify-content-start align-items-center row pb-2 gap-3 gap-md-0">
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <select name="account_id" class="account_select2 form-select"
                                        onchange="this.form.submit()">
                                        <option value="">اختر الحساب</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $selectedAccount == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control datepicker" style="width: auto!important"
                                        value="{{ request()->date ?? '' }}" name="date"
                                        placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>التاريخ</th>
                            <th>الوصف</th>
                            <th class="text-end">مدين (Debit)</th>
                            <th class="text-end">دائن (Credit)</th>
                            <th class="text-end">الرصيد (Balance)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $balance = 0;
                        @endphp
                        @if ($selectedAccount)
                            @foreach ($transactions as $transaction)
                                @php
                                    // If the account is receiving money, it's a debit; otherwise, it's a credit
                                    $debit = $transaction->to_account_id == $selectedAccount ? $transaction->amount : 0;
                                    $credit =
                                        $transaction->from_account_id == $selectedAccount ? $transaction->amount : 0;
                                    $balance += $debit - $credit;
                                @endphp
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-end">{{ number_format($debit, 2) }}</td>
                                    <td class="text-end">{{ number_format($credit, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance, 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">{{ _t('Please select an account') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--  -->

@endsection
