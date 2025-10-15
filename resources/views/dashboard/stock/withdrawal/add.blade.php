@extends('layouts/layoutMaster')

@section('title', 'New Stock Withdrawal')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            var excluded_ids = [];
            let department = "{{ request()->department ?? 1 }}";
            let tax_percentage = "{{ $tax_percentage }}";
            $('.type_select2').select2({});
            $('.stock_select2').select2({
                placeholder: '{{ _t('Select Stock') }}',
                ajax: {
                    url: route('stock.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            min_quantity: '1', // your custom parameter
                            department: department, // Cafeteria department ID
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.product.name + " - " + item.barcode + " - " +
                                        item.unit?.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employee') }}',
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
                                    text: item.name + " - " + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });

            function initializeSelect2(id) {
                $(`.stock_select2_${id}`).select2({
                    placeholder: '{{ _t('Select Stocks') }}',
                    ajax: {
                        url: route('stock.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                excluded_ids: excluded_ids, // your custom parameter
                                department: department,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    let purchase_bill = null;
                                    if (item.transfers_to.length)
                                        purchase_bill = item.transfers_to[0]?.from;
                                    return {
                                        id: item.id,
                                        text: item.product.name + " - " + item.barcode + " - " +
                                            item.unit?.name,
                                        quantity: item.quantity,
                                        price: purchase_bill?.exchange_price,
                                        tax: purchase_bill?.tax,
                                        convert: purchase_bill?.convert,
                                        unit: item.unit?.name
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            initializeSelect2('1');

            $('.repeater-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.repeater-wrapper');
                // var maxAdvantages = 5; // Maximum allowed advantages
                var currentCount = repeaterWrapper.find('.repeater-item').length;

                // if (currentCount < maxAdvantages) {
                // var template = repeaterWrapper.find('.repeater-item').html();
                // var item = $('<div class="repeater-item">' + template + '</div>');
                // item.find('.form-control').val(''); // Clear input values
                var newRepeaterItem = `
                    <div class="repeater-item">
                        <div class="row">
                            <div class="mb-3 col-3">
                                <label class="form-label" for="stock-repeater-${currentCount+1}-1">{{ _t('Stocks') }}</label>
                                <select id="stock-repeater-${currentCount+1}-2" name="stocks[${currentCount}][stock_id]" class="stock_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="unit-repeater-${currentCount+1}-2">{{ _t('Unit') }}</label>
                                <input type="text" name="stocks[${currentCount}][unit]" id="unit-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Return Unit') }}" readonly />
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('Piece Price') }}</label>
                                <input type="number" step="any" name="stocks[${currentCount}][price]" id="price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Piece Price') }}" readonly />
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="stocks[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-items').append(newRepeaterItem);
                initializeSelect2(currentCount + 1);
            });
            $('.repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var repeaterItems = $(this).closest('.repeater-items');
                var item = $(this).closest('.repeater-item');
                // if (repeaterItems.children('.repeater-item').length > 1) {
                item.remove();
                updateTotalPreview();
                // } else {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Oops..',
                //         text: "At least one product is required",
                //     });
                // }
            });
            $(document).on('select change', '[class^="stock_select2_"]', function() {
                var quantity = $(this).select2('data')[0].quantity;
                var price = $(this).select2('data')[0].price;
                var tax = $(this).select2('data')[0].tax;
                var convert = $(this).select2('data')[0].convert;
                var unit = $(this).select2('data')[0].unit;
                quantity_input = $(this).closest('.repeater-item').find('input[id^="quantity-repeater-"]');
                price_input = $(this).closest('.repeater-item').find('input[id^="price-repeater-"]');
                unit_input = $(this).closest('.repeater-item').find('input[id^="unit-repeater-"]');
                quantity_input.val(quantity);
                quantity_input.attr('max', quantity);
                unit_input.val(unit);
                price_input.val(price);
                updateTotalPreview();
            });
            $(document).on('keyup change',
                '[id^="price-repeater-"], [id^="quantity-repeater-"], [id^="convert-repeater-"]',
                function() {
                    updateTotalPreview();
                });
            $('.type_select2').change(function() {
                let type = $(this).val();
                $('.employee-div').addClass('d-none');
                $('.reason-div').addClass('d-none');
                if (type) {
                    if (type == 3) {
                        $('.employee-div').removeClass('d-none');
                    } else if (type == 2) {
                        $('.reason-div').removeClass('d-none');
                    }
                }
            });
            $('#purchaseRadioIcon').click(function() {
                if ($(this).is(':checked')) {
                    $('#total').attr('readonly', true);
                    $('.profit-div').removeClass('d-none');
                    $('.type-div').addClass('d-none');
                    $('.products-card').removeClass('d-none');
                } else {
                    $('#total').attr('readonly', false);
                    $('.profit-div').addClass('d-none');
                    $('.type-div').removeClass('d-none');
                    $('.products-card').addClass('d-none');
                }
            });
            $('#expenseRadioIcon').click(function() {
                if ($(this).is(':checked')) {
                    $('#total').attr('readonly', false);
                    $('.profit-div').addClass('d-none');
                    $('.type-div').removeClass('d-none');
                    $('.products-card').addClass('d-none');
                } else {
                    $('#total').attr('readonly', true);
                    $('.profit-div').removeClass('d-none');
                    $('.type-div').addClass('d-none');
                    $('.products-card').removeClass('d-none');
                }
            });

            async function updateTotalPreview() {
                let total = 0;
                let tax = 0;
                console.log('total', total);
                excluded_ids = [];
                $('[id^="price-repeater-"]').each(function() {
                    let stock_id = $(this).closest('.repeater-item').find(
                        'select[class^="stock_select2_"]').val();
                    excluded_ids.push(stock_id);
                    let price = $(this).val();
                    if (price) {
                        let quantity = $(this).closest('.repeater-item').find(
                            'input[id^="quantity-repeater-"]').val() || 1;
                        let convert = $(this).closest('.repeater-item').find(
                            'input[id^="convert-repeater-"]').val() || 1;
                        $(this).closest('.repeater-item').find('input[id^="exchange-price-repeater-"]')
                            .val((price / convert).toFixed(2));
                        total += price * quantity;
                        tax += (price * quantity) * (tax_percentage / 100);

                    }
                });
                $('#total').val(total.toFixed(2) - tax.toFixed(2));
                $('#tax').val(tax.toFixed(2));
                $('#grand_total').val((total).toFixed(2));
            }
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small class="text-muted float-end" id="bill_id"></small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('stock-withdrawal.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="department" value="{{ request()->department ?? 1 }}" />
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type" name="type" class="type_select2 form-select"
                                            data-allow-clear="false">
                                            @foreach (App\Enums\StockWithdrawalTypeEnum::cases() as $type)
                                                <option value="{{ $type->value }}">{{ _t($type->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 employee-div d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Employee') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employee_id" name="employee_id" class="employee_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                </div>
                                @error('employee_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 reason-div d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Reason') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <textarea rows="4" class="form-control" name="reason" placeholder="{{ _t('Reason') }}"></textarea>
                                    </div>
                                </div>
                                @error('bill_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 type-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type_id" name="bill_type_id" class="type_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                </div>
                                @error('type_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4 products-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Stocks') }}</h5>
                            </div>
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    <div class="repeater-item">
                                        <!-- <div class="row">                                                                                   </div> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-add"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Total') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-dollar-sign"></i></span>
                                        <input type="number" min="1" name="total" value="0.00" required
                                            class="form-control" id="total" placeholder="{{ _t('total') }}"
                                            aria-describedby="basic-icon-default-fullquantity2" readonly />
                                    </div>
                                    @error('total')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-4 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Tax') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-dollar-sign"></i></span>
                                        <input type="number" min="0" name="tax" value="0.00" required
                                            class="form-control" id="tax" placeholder="{{ _t('Tax') }}"
                                            aria-describedby="basic-icon-default-fullquantity2" readonly />
                                    </div>
                                    @error('tax')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-4 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Grand Total') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-dollar-sign"></i></span>
                                        <input type="number" min="0" name="grand_total" value="0.00" required
                                            class="form-control" id="grand_total" placeholder="{{ _t('Grand Total') }}"
                                            aria-describedby="basic-icon-default-fullquantity2" readonly />
                                    </div>
                                    @error('grand_total')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
