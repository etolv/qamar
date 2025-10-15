@extends('layouts/layoutMaster')

@section('title', 'New bill')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            var department = getUrlParameter('department') ?? 1;
            $('.payment_select2').select2({});
            $('.consumption_select2').select2({});
            $('.product_select2').select2({
                placeholder: '{{ _t('Select Product') }}',
                ajax: {
                    url: route('product.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            department: department,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name + " - " + item.sku,
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.card_select2').select2({
                placeholder: '{{ _t('Select Card') }}',
                ajax: {
                    url: route('card.fetch'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name + " - " + item.number,
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.type_select2').select2({
                placeholder: '{{ _t('Select Type') }}',
                ajax: {
                    url: route('bill.type.search'),
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
                                    text: item.name,
                                    static: item.static,
                                    price: item.price
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.branch_select2').select2({
                placeholder: '{{ _t('Select Branch') }}',
                ajax: {
                    url: route('branch.search'),
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

            function initSupplierSelect2(className, id) {
                $(className).select2({
                    placeholder: '{{ _t('Select Supplier') }}',
                    ajax: {
                        url: route('supplier.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                department: department,
                                supplier_id: id
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name || item.company
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            initSupplierSelect2('.main_supplier_select2', null);
            $(`.tax_type_select2_0`).select2({
                placeholder: "{{ _t('Select Tax Type') }}",
                data: [
                    @foreach ($tax_types as $tax)
                        {
                            id: "{{ $tax->value }}",
                            text: '{{ _t($tax->name) }}'
                        },
                    @endforeach
                ]
            });

            function initializeSelect2(id) {
                $(`.tax_type_select2_${id}`).select2({
                    placeholder: "{{ _t('Select Tax Type') }}",
                    data: [
                        @foreach ($tax_types as $tax)
                            {
                                id: "{{ $tax->value }}",
                                text: '{{ _t($tax->name) }}'
                            },
                        @endforeach
                    ]
                });
                $(`.product_select2_${id}`).select2({
                    placeholder: '{{ _t('Select Products') }}',
                    ajax: {
                        url: route('product.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                department: department,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + " - " + item.sku
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
                $(`.purchase_unit_select2_${id}`).select2({
                    placeholder: '{{ _t('Select Unit') }}',
                    ajax: {
                        url: route('unit.search'),
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
                $(`.sell_unit_select2_${id}`).select2({
                    placeholder: '{{ _t('Select Unit') }}',
                    ajax: {
                        url: route('unit.search'),
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
                                <label class="form-label" for="product-repeater-${currentCount+1}-1">{{ _t('Products') }}</label>
                                <select id="product-repeater-${currentCount+1}-2" name="products[${currentCount}][product_id]" class="product_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="purchase-repeater-${currentCount+1}-1">{{ 'Purchase Unit' }}</label>
                                <select id="purchase-repeater-${currentCount+1}-1" name="products[${currentCount}][purchase_unit_id]" class="purchase_unit_select2_${currentCount+1} form-select" data-allow-clear="true" required>
                                </select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="sell-repeater-${currentCount+1}-1">{{ 'Sell Unit' }}</label>
                                <select id="sell-repeater-${currentCount+1}-1" name="products[${currentCount}][retail_unit_id]" class="sell_unit_select2_${currentCount+1} form-select" data-allow-clear="true" required>
                                </select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="convert-repeater-${currentCount+1}-2">{{ _t('Purchase To Sell') }}</label>
                                <input type="number" name="products[${currentCount}][convert]" id="convert-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Convert') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('Purchase Price') }}</label>
                                <input type="number" name="products[${currentCount}][purchase_price]" id="price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Purchase Price') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="products[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label text-muted" for="exchange-price-repeater-${currentCount+1}-2">{{ _t('Exchange Price') }}</label>
                                <input type="number" name="products[${currentCount}][exchange_price]" id="exchange-price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Exchange Price') }}" readonly />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('Sell Price') }}</label>
                                <input type="number" name="products[${currentCount}][sell_price]" id="sell-price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Sell Price') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="date-repeater-${currentCount+1}-2">{{ _t('Expiration Date') }}</label>
                                <input type="date" name="products[${currentCount}][expiration_date]" id="date-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Expiration date') }}" />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="tax-type-repeater-${currentCount+1}-1">{{ _t('Tax Type') }}</label>
                                <select id="tax-type-repeater-${currentCount+1}-2" name="products[${currentCount}][tax_type]" class="tax_type_select2_${currentCount+1} form-select" required></select>
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <hr>
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
            $(document).on('keyup change',
                '[id^="price-repeater-"], [id^="quantity-repeater-"], [id^="convert-repeater-"]',
                function() {
                    updateTotalPreview();
                });
            $('.supplier_select2').change(function() {
                let suppler_id = $(this).val();
                if (suppler_id) {
                    $('#identifier').val(
                        `B{{ $bill_id }}${suppler_id}{{ Carbon\Carbon::now()->format('ymd') }}`);
                }
            });
            $('.main_supplier_select2').change(function() {
                let suppler_id = $(this).val();
                if (suppler_id) {
                    initSupplierSelect2('.supplier_select2', suppler_id);
                    $('#identifier').val(
                        `B{{ $bill_id }}${suppler_id}{{ Carbon\Carbon::now()->format('ymd') }}`);
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
                console.log('total', total);
                $('[id^="price-repeater-"]').each(function() {
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

            $('.type_select2').change(function() {
                let type = $(this).select2('data')[0];
                let static = type.static;
                if (static) {
                    $('#total').val(type.price);
                }
            });
            $('.payment_select2').change(function() {
                let type = $(this).val();
                if (type == 1) {
                    $('.card-div').addClass('d-none');
                    $('.card_select2').attr('required', false);
                } else {
                    $('.card-div').removeClass('d-none');
                    $('.card_select2').attr('required', true);
                }
            });
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
                    <form method="post" action="{{ route('bill.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ request()->department ?? 1 }}" name="department">
                        <div class="row mb-3">
                            <div class="col-md mb-md-0 mb-2">
                                <div class="form-check custom-option custom-option-icon">
                                    <label class="form-check-label custom-option-content" for="purchaseRadioIcon">
                                        <span class="custom-option-body">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                            <span class="custom-option-title">{{ _t('Purchase Bill') }}</span>
                                            <small>{{ _t('Purchase products from your suppliers') }}</small>
                                        </span>
                                        <input name="type" value="purchase" class="form-check-input" type="radio"
                                            value="" id="purchaseRadioIcon" checked />
                                    </label>
                                </div>
                            </div>
                            <div class="col-md mb-md-0 mb-2">
                                <div class="form-check custom-option custom-option-icon">
                                    <label class="form-check-label custom-option-content" for="expenseRadioIcon">
                                        <span class="custom-option-body">
                                            <i class="fa-solid fa-share-from-square"></i>
                                            <span class="custom-option-title"> {{ _t('Expenses') }} </span>
                                            <small>{{ _t('Register Exchange bill in your system, electricity, water, etc') }}</small>
                                        </span>
                                        <input name="type" value="expense" class="form-check-input" type="radio"
                                            value="" id="expenseRadioIcon" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 profit-div">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Received') }}
                                *</label>
                            <div class="col-sm-10">
                                <label class="switch switch-primary me-0">
                                    <input type="checkbox" class="switch-input" id="received" name="received"
                                        data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false"
                                        aria-controls="collapseExample" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label"></span>
                                </label>
                            </div>
                            @error('received')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <!-- <label class="col-sm-2 col-form-label" for="basic-icon-default-profit">{{ _t('Profit Percentage') }}</label>
                                                                                                                                                                                                                                                                                                        <div class="col-sm-4">
                                                                                                                                                                                                                                                                                                            <span class="mt-2 badge bg-label-primary" id="basic-icon-default-profit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ _t('You can change this value from setting page') }}">
                                                                                                                                                                                                                                                                                                                {{ $profit_percentage }} <i class="fa-solid fa-percentage"></i>
                                                                                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                                                                                        </div> -->
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Main Supplier') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="main_supplier_id" name="main_supplier_id"
                                            class="main_supplier_select2 form-select" data-allow-clear="true" required>
                                        </select>
                                    </div>
                                </div>
                                @error('main_supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Supplier') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="supplier_id" name="supplier_id" class="supplier_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                </div>
                                @error('supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('BIll Id') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <input type="text" id="identifier" name="identifier"
                                            placeholder="{{ _t('BIll Id') }}" class="form-control"
                                            data-allow-clear="true" />
                                    </div>
                                </div>
                                @error('identifier')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="type-div d-none">
                            <div class="row mb-3">
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
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Term') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                        <div class="col-sm-10">
                                            <input type="text" value="{{ old('term') }}" id="term"
                                                name="term" placeholder="{{ _t('Term') }}" class="form-control" />
                                        </div>
                                    </div>
                                    @error('type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Tax Type') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                        <div class="col-sm-10">
                                            <select id="tax-type" name="tax_type"
                                                class="tax_type_select2_0 form-select"></select>
                                        </div>
                                    </div>
                                    @error('type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Payment Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="payment_type" name="payment_type" class="payment_select2 form-select"
                                            data-allow-clear="false" required>
                                            @php
                                                $paymentTypes = \App\Enums\PaymentTypeEnum::cases();
                                                $filteredPaymentTypes = array_filter(
                                                    $paymentTypes,
                                                    fn($value) => $value !== \App\Enums\PaymentTypeEnum::POINT,
                                                );
                                            @endphp
                                            @foreach ($filteredPaymentTypes as $value)
                                                <option value="{{ $value->value }}">{{ _t($value->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('type_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 card-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Card') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="card_id" name="card_id" class="card_select2 form-select"
                                            data-allow-clear="false">
                                        </select>
                                    </div>
                                </div>
                                @error('card_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('File') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-file"></i></span>
                                    <div class="col-sm-10">
                                        <input type="file" name="file" id="file" class="form-control" />
                                    </div>
                                </div>
                                @error('file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4 products-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Products') }}</h5>
                            </div>
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    <div class="repeater-item">
                                        {{-- repeater item --}}
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
                            <div class="col-sm-6">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Paid') }} *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-dollar-sign"></i></span>
                                        <input type="number" min="0" name="paid"
                                            value="{{ old('paid') ?? 0 }}" required class="form-control"
                                            id="basic-icon-default-fullpaid" placeholder="{{ _t('paid') }}"
                                            aria-label="{{ _t('paid') }}"
                                            aria-describedby="basic-icon-default-fullpaid2" required />
                                    </div>
                                    @error('paid')
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
