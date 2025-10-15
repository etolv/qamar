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
    {{-- <div class="container">
        <h2>تقرير الدخل</h2>

        <h4>إيرادات المبيعات</h4>
        <p>صافي المبيعات: {{ number_format($netSales, 2) }}</p>

        <h4>تكلفة البضاعة المباعة</h4>
        <p>COGS: {{ number_format($cogs, 2) }}</p>

        <h4>الربح الإجمالي</h4>
        <p>{{ number_format($grossProfit, 2) }}</p>

        <h4>المصاريف التشغيلية</h4>
        <ul>
            @foreach ($expenses as $label => $amount)
                <li>{{ $label }}: {{ number_format($amount, 2) }}</li>
            @endforeach
        </ul>
        <p>إجمالي المصاريف التشغيلية: {{ number_format($totalOperatingExpenses, 2) }}</p>

        <h4>الدخل التشغيلي</h4>
        <p>{{ number_format($operatingIncome, 2) }}</p>

        <h4>العناصر غير التشغيلية</h4>
        <p>إيرادات أخرى: {{ number_format($otherIncome, 2) }}</p>
        <p>فوائد: {{ number_format($interestExpense, 2) }}</p>

        <h4>الدخل قبل الضرائب</h4>
        <p>{{ number_format($incomeBeforeTax, 2) }}</p>

        <h4>الضرائب</h4>
        <p>{{ number_format($taxExpense, 2) }}</p>

        <h4>صافي الدخل</h4>
        <p><strong>{{ number_format($netIncome, 2) }}</strong></p>
    </div> --}}
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📊 تقرير الدخل</h4>
                    </div>
                    <div class="card-body">
                        {{-- Section: الإيرادات --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">إيرادات المبيعات</h5>
                            <div class="d-flex justify-content-between">
                                <span>صافي المبيعات</span>
                                <span class="badge bg-success fs-6">{{ number_format($netSales, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: COGS --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">تكلفة البضاعة المباعة (COGS)</h5>
                            <div class="d-flex justify-content-between">
                                <span>COGS</span>
                                <span class="badge bg-danger fs-6">{{ number_format($cogs, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Gross Profit --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">الربح الإجمالي</h5>
                            <div class="d-flex justify-content-between">
                                <span>الربح الإجمالي</span>
                                <span class="badge bg-info fs-6">{{ number_format($grossProfit, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Expenses --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">المصاريف التشغيلية</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>البند</th>
                                            <th class="text-end">القيمة (ر.س)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($expenses as $label => $amount)
                                            <tr>
                                                <td>{{ $label }}</td>
                                                <td class="text-end">{{ number_format($amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary fw-bold">
                                        <tr>
                                            <td>إجمالي المصاريف التشغيلية</td>
                                            <td class="text-end">{{ number_format($totalOperatingExpenses, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Section: Operating Income --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">الدخل التشغيلي</h5>
                            <div class="d-flex justify-content-between">
                                <span>الدخل التشغيلي</span>
                                <span
                                    class="badge bg-warning text-dark fs-6">{{ number_format($operatingIncome, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Non-operating --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">العناصر غير التشغيلية</h5>
                            <div class="d-flex justify-content-between">
                                <span>إيرادات أخرى</span>
                                <span>{{ number_format($otherIncome, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>فوائد</span>
                                <span>{{ number_format($interestExpense, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Income before tax --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">الدخل قبل الضرائب</h5>
                            <div class="d-flex justify-content-between">
                                <span>الدخل قبل الضرائب</span>
                                <span class="badge bg-secondary fs-6">{{ number_format($incomeBeforeTax, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Tax --}}
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary">الضرائب</h5>
                            <div class="d-flex justify-content-between">
                                <span>الضرائب</span>
                                <span>{{ number_format($taxExpense, 2) }}</span>
                            </div>
                        </div>

                        {{-- Section: Net Income --}}
                        <div class="mb-4">
                            <h4 class="fw-bold text-success text-center border-top pt-3">صافي الدخل</h4>
                            <h3 class="text-center fw-bold text-success">{{ number_format($netIncome, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
