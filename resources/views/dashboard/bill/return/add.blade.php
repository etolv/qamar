@extends('layouts/layoutMaster')

@section('title', 'New bill return')

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
            let supplier_id = null;
            let bill_id = null;
            $('.consumption_select2').select2({});
            $('.stock_select2').select2({
                placeholder: '{{ _t('Select Stock') }}',
                ajax: {
                    url: route('stock.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
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
            $('.supplier_select2').select2({
                placeholder: '{{ _t('Select Supplier') }}',
                ajax: {
                    url: route('supplier.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.bill_select2').select2({
                placeholder: '{{ _t('Select Bill') }}',
                ajax: {
                    url: route('bill.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            supplier_id: supplier_id, // your custom parameter
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.identifier
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
                                bill_id: bill_id,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.product.name + " - " + item.barcode + " - " +
                                            item.unit?.name,
                                        quantity: item.quantity,
                                        price: item.price,
                                        exchange_price: item.exchange_price,
                                        unit: item.unit?.name
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
                // $(`.purchase_unit_select2_${id}`).select2({
                //     placeholder: '{{ _t('Select Unit') }}',
                //     ajax: {
                //         url: route('unit.search'),
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
                // $(`.sell_unit_select2_${id}`).select2({
                //     placeholder: '{{ _t('Select Unit') }}',
                //     ajax: {
                //         url: route('unit.search'),
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
                                <label class="form-label" for="unit-repeater-${currentCount+1}-2">{{ _t('Return Unit') }}</label>
                                <input type="text" name="stocks[${currentCount}][unit]" id="unit-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Return Unit') }}" required />
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('Piece Price') }}</label>
                                <input type="number" name="stocks[${currentCount}][return_price]" id="price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Piece Price') }}" readonly />
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
                var price = $(this).select2('data')[0].exchange_price;
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
            $('.supplier_select2').change(function() {
                let suppler_id = $(this).val();
                if (suppler_id) {
                    // $('#identifier').val(`B1${suppler_id}{{ Carbon\Carbon::now()->format('ymd') }}`);
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
            $('.supplier_select2').change(function() {
                supplier_id = $(this).val();
                $('.bill_select2').attr('disabled', false);
            });
            $('.bill_select2').change(function() {
                bill_id = $(this).val();
                $('.repeater-add').attr('disabled', false);
            });

            async function updateTotalPreview() {
                let total = 0;
                console.log('total', total);
                excluded_ids = [];
                $('[id^="price-repeater-"]').each(function() {
                    let stock_id = $(this).closest('.repeater-item').find(
                        'select[class^="stock_select2_"]').val();
                    console.log('stock_id');
                    console.log(stock_id);
                    excluded_ids.push(stock_id);
                    console.log('excluded_ids');
                    console.log(excluded_ids);
                    let price = $(this).val();
                    if (price) {
                        let quantity = $(this).closest('.repeater-item').find(
                            'input[id^="quantity-repeater-"]').val() || 1;
                        let convert = $(this).closest('.repeater-item').find(
                            'input[id^="convert-repeater-"]').val() || 1;
                        $(this).closest('.repeater-item').find('input[id^="exchange-price-repeater-"]')
                            .val((price / convert).toFixed(2));
                        total += price * quantity;
                    }
                });
                $('#total').val(total);
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
                    <form method="post" action="{{ route('bill-return.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Supplier') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="supplier_id" name="supplier_id" class="supplier_select2 form-select"
                                            data-allow-clear="true" required>
                                        </select>
                                    </div>
                                </div>
                                @error('supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Bill') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="bill_id" name="bill_id" class="bill_select2 form-select"
                                            data-allow-clear="true" disabled>
                                        </select>
                                    </div>
                                </div>
                                @error('bill_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Reason') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <textarea rows="4" class="form-control" name="reason" placeholder="{{ _t('Return Reason') }}"></textarea>
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
                                        <!-- <div class="row">
                                                                            <div class="mb-3 col-4">
                                                                                <label class="form-label" for="product-repeater-1-1">{{ 'Products' }}</label>
                                                                                <select name="products[0][product_id]" id="product-repeater-1-1" class="product_select2_1 form-select" data-allow-clear="true">
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3 col-3">
                                                                                <label class="form-label" for="purchase-repeater-1-1">{{ 'Purchase Unit' }}</label>
                                                                                <select id="purchase-repeater-1-1" name="products[0][purchase_unit_id]" class="purchase_unit_select2_1 form-select" data-allow-clear="true" required>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3 col-3">
                                                                                <label class="form-label" for="sell-repeater-1-1">{{ 'Sell Unit' }}</label>
                                                                                <select id="sell-repeater-1-1" name="products[0][retail_unit_id]" class="sell_unit_select2_1 form-select" data-allow-clear="true" required>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3 col-2">
                                                                                <label class="form-label" for="convert-repeater-1-2">{{ _t('Purchase To Sell') }}</label>
                                                                                <input type="number" name="products[0][convert]" id="convert-repeater-1-2" class="form-control" placeholder="{{ _t('Convert') }}" required />
                                                                            </div>
                                                                            <div class="mb-3 col-2">
                                                                                <label class="form-label" for="price-repeater-1-2">{{ _t('Purchase Price') }}</label>
                                                                                <input type="number" name="products[0][purchase_price]" id="price-repeater-1-2" class="form-control" placeholder="{{ _t('Purchase Price') }}" required />
                                                                            </div>
                                                                            <div class="mb-3 col-2">
                                                                                <label class="form-label" for="quantity-repeater-1-2">{{ _t('Purchase Quantity') }}</label>
                                                                                <input type="number" name="products[0][quantity]" id="quantity-repeater-1-2" class="form-control" placeholder="{{ _t('Purchase Quantity') }}" required />
                                                                            </div>
                                                                            <div class="mb-3 col-2">
                                                                                <label class="form-label" for="date-repeater-1-2">{{ _t('Expiration Date') }}</label>
                                                                                <input type="date" name="products[0][expiration_date]" id="date-repeater-1-2" class="form-control" placeholder="{{ _t('Expiration date') }}" />
                                                                            </div>
                                                                            <div class="mb-3 col-1">
                                                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                                                                            </div>
                                                                        </div> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-add" disabled><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6">
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
                            <!-- <div class="col-sm-6">
                                                                <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Paid') }} *</label>
                                                                <div class="col-sm-10">
                                                                    <div class="input-group input-group-merge">
                                                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
                                                                        <input type="number" min="1" name="paid" value="{{ old('paid') }}" required class="form-control" id="basic-icon-default-fullpaid" placeholder="{{ _t('paid') }}" aria-label="{{ _t('paid') }}" aria-describedby="basic-icon-default-fullpaid2" required />
                                                                    </div>
                                                                    @error('paid')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                                                                </div>
                                                            </div> -->
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
