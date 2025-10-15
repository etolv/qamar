@extends('layouts/layoutMaster')

@section('title', 'New order')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.consumption_select2').select2({});
            $('.customer_select2').select2({
                placeholder: '{{ _t('Select Customer') }}',
                ajax: {
                    url: route('customer.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
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
                            type: 'bookings_hairdresser' // your custom parameter
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
            $('.branch_select2').select2({
                placeholder: '{{ _t('Select Branch') }}',
                ajax: {
                    url: route('branch.search'),
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
                                    text: item.name + " - " + item.city.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.coupon_select2').select2({
                placeholder: '{{ _t('Select Coupon') }}',
                ajax: {
                    url: route('coupon.search'),
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
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });

            function initializeProductSelect2(selector) {
                $(selector).select2({
                    placeholder: '{{ _t('Select Products') }}',
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
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.product.name + " - " + item.barcode,
                                        price: item.price
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            var stocks_count = "{{ count($order->orderStocks) }}";
            for (let i = 1; i <= stocks_count; i++) {
                initializeProductSelect2(`.product_select2_${i}`);
            }

            function initializeServiceSelect2(selector) {
                $(selector).select2({
                    placeholder: '{{ _t('Select Services') }}',
                    ajax: {
                        url: route('service.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                department: 1, // Salon department ID
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name,
                                        price: item.price
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            var services_count = "{{ count($order->orderServices) }}";
            for (let i = 1; i <= services_count; i++) {
                initializeServiceSelect2(`.service_select2_${i}`);
            }

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
                            <div class="mb-3 col-8">
                                <label class="form-label" for="product-repeater-${currentCount+1}-1">{{ 'Products' }}</label>
                                <select id="product-repeater-${currentCount+1}-2" name="stocks[${currentCount}][id]" class="product_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="stocks[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control stocks-quantity-${currentCount+1}" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-items').append(newRepeaterItem);
                initializeProductSelect2(`.product_select2_${currentCount+1}`);
                // initializeProductSelect2(currentCount + 1);
                // } else {
                //     // Show error message using SweetAlert
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Oops..',
                //         text: 'Maximum allowed advantages is ' + maxAdvantages,
                //     });
                // }
            });

            $('.repeater-service-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.repeater-service-wrapper');
                // var maxAdvantages = 5; // Maximum allowed advantages
                var currentCount = repeaterWrapper.find('.repeater-service-item').length;

                // if (currentCount < maxAdvantages) {
                // var template = repeaterWrapper.find('.repeater-item').html();
                // var item = $('<div class="repeater-item">' + template + '</div>');
                // item.find('.form-control').val(''); // Clear input values
                var newRepeaterItem = `
                    <div class="repeater-service-item">
                        <div class="row">
                            <div class="mb-3 col-8">
                                <label class="form-label" for="service-repeater-${currentCount+1}-1">{{ 'Services' }}</label>
                                <select id="service-repeater-${currentCount+1}-2" name="services[${currentCount}][id]" class="service_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="form-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="services[${currentCount}][quantity]" id="form-repeater-${currentCount+1}-2" class="form-control service-quantity-${currentCount+1}" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-info repeater-session" title="{{ _t('Session') }}"><i class="fa-solid fa-calendar-days"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-service-items').append(newRepeaterItem);
                initializeServiceSelect2(`.service_select2_${currentCount+1}`);
            });

            $('.repeater-service-wrapper').on('click', '.repeater-session', function(e) {
                e.preventDefault();
                var currentItem = $(this).closest('.repeater-service-item');
                var repeaterItems = $(this).closest('.repeater-service-items');
                var itemIndex = repeaterItems.find('.repeater-service-item').index(currentItem);
                var sessionFields = currentItem.find('.session-fields');

                if (sessionFields.length > 0) {
                    sessionFields.remove();
                    $(this).removeClass('btn-danger').addClass('btn-info');
                } else {
                    var newSessionFields = `
                    <div class="row session-fields">
                        <div class="row session-fields">
                            <div class="mb-3 col-4">
                                <label class="form-label" for="session_count-${itemIndex}">{{ _t('Sessions Count') }}</label>
                                <input type="number" min="1" name="services[${itemIndex}][session_count]" id="session_count-${itemIndex}" class="form-control" placeholder="{{ _t('Sessions Count') }}" required />
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="due_date-${itemIndex}">{{ _t('Due Date') }}</label>
                                <input type="date" name="services[${itemIndex}][due_date]" id="due_date-${itemIndex}" class="form-control" placeholder="{{ _t('Due Date') }}" required />
                            </div>
                        </div>      
                    </div>
                `;
                    currentItem.append(newSessionFields);
                    $(this).removeClass('btn-info').addClass('btn-danger');
                }
            });


            $('.repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var repeaterItems = $(this).closest('.repeater-items');
                var item = $(this).closest('.repeater-item');
                if (repeaterItems.children('.repeater-item').length > 1) {
                    item.remove();
                    updateBillPreview();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops..',
                        text: "At least one product is required",
                    });
                }
            });
            $('.repeater-service-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var repeaterItems = $(this).closest('.repeater-service-items');
                var item = $(this).closest('.repeater-service-item');
                if (repeaterItems.children('.repeater-service-item').length > 1) {
                    item.remove();
                    updateBillPreview();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops..',
                        text: "At least one service is required",
                    });
                }
            });

            async function updateBillPreview() {
                let products = [];
                let services = [];

                // Function to fetch service products asynchronously
                async function fetchServiceProducts(serviceId) {
                    try {
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        const response = await $.ajax({
                            url: route('service.products', serviceId),
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + csrfToken
                            }
                        });
                        return response.data
                            .product_services; // Assuming products is an array of product objects
                    } catch (error) {
                        console.error('Error fetching service products:', error);
                        return []; // Return empty array or handle error as needed
                    }
                }

                // Iterate through product select2 elements
                $('[class^="product_select2_"]').each(function() {
                    let product = $(this).select2('data')[0];
                    if (product) {
                        let quantity = $(this).closest('.repeater-item').find(
                            'input[id^="quantity-repeater-"]').val() || 1;
                        let price = product.price || $(this).find('option:selected').data('price');
                        products.push({
                            name: product.text,
                            quantity: quantity,
                            price: price,
                            total: price * quantity
                        });
                    }
                });

                // Iterate through service select2 elements
                for (let i = 0; i < $('[class^="service_select2"]').length; i++) {
                    let serviceElement = $('[class^="service_select2"]').eq(i);
                    let service = serviceElement.select2('data')[0];
                    if (service) {
                        // console.log($(service.element).data('price'));
                        let service_products = await fetchServiceProducts(service.id);

                        let quantity = serviceElement.closest('.repeater-service-item').find(
                            'input[id^="form-repeater-"]').val() || 1;
                        let price = service.price || $(service.element).data('price');
                        services.push({
                            name: service.text,
                            quantity: quantity,
                            price: price,
                            total: price * quantity,
                            products: service_products
                        });
                    }
                }

                // After fetching all data, render the bill preview
                await renderBillPreview(products, services);
            }

            async function renderBillPreview(products, services) {
                let totalAmount = 0;
                let discount = 0;
                let previewHtml = '<h4>{{ _t('Bill Preview') }}</h4>';

                // Render products table
                previewHtml += '<div class="card"><div class="card-body">';
                previewHtml += '<h5>{{ _t('Products') }}</h5>';
                previewHtml += '<table class="table">';
                previewHtml += `
        <thead>
            <tr>
                <th>{{ _t('Name') }}</th>
                <th>{{ _t('Quantity') }}</th>
                <th>{{ _t('Price') }}</th>
                <th>{{ _t('Total') }}</th>
            </tr>
        </thead>
        <tbody>`;
                products.forEach(product => {
                    previewHtml += `
            <tr>
                <td>${product.name}</td>
                <td>${product.quantity}</td>
                <td>{{ _t('SAR') }} ${product.price}</td>
                <td>{{ _t('SAR') }} ${product.total}</td>
            </tr>`;
                    totalAmount += product.total;
                });
                previewHtml += '</tbody></table>';

                // Render services table
                previewHtml += '<h5 class="mt-3">{{ _t('Services') }}</h5>';
                previewHtml += '<table class="table">';
                previewHtml += `
        <thead>
            <tr>
                <th>{{ _t('Name') }}</th>
                <th>{{ _t('Quantity') }}</th>
                <th>{{ _t('Price') }}</th>
                <th>{{ _t('Total') }}</th>
            </tr>
        </thead>
        <tbody>`;
                services.forEach(service => {
                    previewHtml += `
            <tr>
                <td>${service.name}</td>
                <td>${service.quantity}</td>
                <td>{{ _t('SAR') }} ${service.price}</td>
                <td>{{ _t('SAR') }} ${service.total}</td>
            </tr>`;

                    // Check if there are products associated with the service
                    if (service.products.length > 0) {
                        previewHtml +=
                            '<tr><td></td><td></td><td colspan="4"><h6><strong>{{ _t('Related Products') }}:</strong></h6></td></tr>';
                        service.products.forEach(product => {
                            if (product.required) {
                                previewHtml += `
                    <tr>
                    <td></td>
                    <td></td>
                        <td><strong>{{ _t('Name') }}:</strong> ${product.product.name}</td>
                        <td><strong>{{ _t('SKU') }}:</strong> ${product.product.sku}</td>
                    </tr>`;
                            }
                        });
                    }

                    totalAmount += service.total;
                });
                let selectedCoupon = $('.coupon_select2').select2('data')[0];
                if (selectedCoupon) {
                    console.log('selectedCoupon');
                    console.log(selectedCoupon);
                    let coupon_discount = selectedCoupon.discount || (selectedCoupon).data('discount');
                    let is_percentage = selectedCoupon.is_percentage || $(selectedCoupon).data('is_percentage')
                    if (is_percentage) {
                        discount = totalAmount * coupon_discount / 100;
                    } else {
                        discount = coupon_discount;
                    }
                }
                previewHtml += '</tbody></table>';

                // Render total amount, discount, and final total
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Total') }}: {{ _t('SAR') }} ${(totalAmount).toFixed(2)}</h6>`;
                // previewHtml += `<h6 class="mt-3">{{ _t('Extra Fees') }}: {{ _t('SAR') }} 0.00 </h6>`;
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Discount') }}: {{ _t('SAR') }} ${(discount).toFixed(2)}</h6>`;
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Total Amount') }}: {{ _t('SAR') }} ${(totalAmount - discount).toFixed(2)}</h6>`;
                previewHtml += '</div></div>';

                // Update HTML content
                $('#bill-preview').html(previewHtml);
            }

            $(document).on('change', '[id^="form-repeater-"], [id^="quantity-repeater-"]', function() {
                updateBillPreview();
            });

            $(document).on('select2:select', '[class^="product_select2_"], [class^="service_select2_"]',
                function() {
                    updateBillPreview();
                });
            $(document).on('select2:select', '.coupon_select2', function() {
                updateBillPreview();
            });
            updateBillPreview();
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Update Order') }}</h5> <small
                        class="text-muted float-end">{{ $order->created_at }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('order.update', $order->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Customer') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="customer_id" name="customer_id" class="customer_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="{{ $order->customer_id }}" selected>
                                                {{ $order->customer->user->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('customer_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Employee') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employee_id" name="employee_id" class="employee_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="{{ $order->employee_id }}" selected>
                                                {{ $order->employee->user->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('employee_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 {{ $user_branch ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Branch') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="branch_id" name="branch_id" class="branch_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="{{ $order->branch_id }}" selected>{{ $order->branch->name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Description') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <textarea name="description" class="form-control" id="basic-icon-default-fullsku" placeholder="{{ _t('Description') }}"
                                        aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('description') ?? $order->description }}</textarea>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Coupon') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="coupon_id" name="coupon_id" class="coupon_select2 form-select"
                                            data-allow-clear="true">
                                            @if ($order->coupon)
                                                <option value="{{ $order->coupon->id }}"
                                                    data-discount="{{ $order->coupon->discount }}"
                                                    data-is_percentage="{{ $order->coupon->is_percentage }}" selected>
                                                    {{ $order->coupon->name . ' - ' . $order->coupon->code }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('coupon_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Payment Type') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="payment_type" name="payment_type" class="payment_select2 form-select"
                                            data-allow-clear="true" required>
                                            @foreach (\App\Enums\PaymentTypeEnum::cases() as $value)
                                                <option value="{{ $value->value }}"
                                                    {{ $order->payment_type->value == $value->value ? 'selected' : '' }}>
                                                    {{ _t($value->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('payment_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Services') }}</h5>
                            </div>
                            <div class="card-body repeater-service-wrapper">
                                <div class="repeater-service-items">
                                    @foreach ($order->orderServices as $index => $orderService)
                                        <div class="repeater-service-item">
                                            <div class="row">
                                                <div class="mb-3 col-8">
                                                    <label class="form-label"
                                                        for="service-repeater-{{ $index + 1 }}-1">{{ 'Services' }}</label>
                                                    <select id="service-repeater-{{ $index + 1 }}-1"
                                                        name="services[{{ $index }}][id]"
                                                        class="service_select2_{{ $index + 1 }} form-select"
                                                        data-allow-clear="true" required>
                                                        <option value="{{ $orderService->service_id }}"
                                                            data-price="{{ $orderService->price }}">
                                                            {{ $orderService->service->name }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-2">
                                                    <label class="form-label"
                                                        for="form-repeater-{{ $index + 1 }}-2">{{ _t('Quantity') }}</label>
                                                    <input type="number" value="{{ $orderService->quantity }}"
                                                        name="services[{{ $index }}][quantity]"
                                                        id="form-repeater-{{ $index + 1 }}-2"
                                                        class="form-control service-quantity-{{ $index + 1 }}"
                                                        placeholder="{{ _t('Quantity') }}" required />
                                                </div>
                                                <div class="mb-3 col-1">
                                                    <label class="form-label" for="remover">{{ _t('') }}</label>
                                                    <button class="btn btn-danger repeater-remove"><i
                                                            class="fa-solid fa-trash"></i></button>
                                                </div>
                                                <div class="mb-3 col-1">
                                                    <label class="form-label" for="remover">{{ _t('') }}</label>
                                                    <button
                                                        class="btn {{ $orderService->session_count > 1 ? 'btn-danger' : 'btn-info' }} repeater-session"
                                                        title="{{ _t('Session') }}"><i
                                                            class="fa-solid fa-calendar-days"></i></button>
                                                </div>
                                                @if ($orderService->session_count > 1)
                                                    <div class="row session-fields">
                                                        <div class="mb-3 col-4">
                                                            <label class="form-label"
                                                                for="session_count-${itemIndex}">{{ _t('Sessions Count') }}</label>
                                                            <input type="number" min="1"
                                                                name="services[$index][session_count]"
                                                                value="{{ $orderService->session_count }}"
                                                                id="session_count-${itemIndex}" class="form-control"
                                                                placeholder="{{ _t('Sessions Count') }}" required />
                                                        </div>
                                                        <div class="mb-3 col-4">
                                                            <label class="form-label"
                                                                for="due_date-$index">{{ _t('Due Date') }}</label>
                                                            <input type="date" name="services[$index][due_date]"
                                                                value="{{ $orderService->due_date }}"
                                                                id="due_date-$index" class="form-control"
                                                                placeholder="{{ _t('Due Date') }}" required />
                                                        </div>
                                                        <!-- <div class="mb-3 col-4">
                                                            <label class="form-label" for="extra_price-$index">{{ _t('Extra Fees') }}</label>
                                                            <input type="number" name="services[$index][extra_price]" id="extra_price-$index" value="{{ $orderService->extra_price }}" class="form-control" placeholder="{{ _t('Extra Fees') }}" />
                                                        </div> -->
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button
                                            class="btn btn-primary repeater-service-add">{{ _t('Add service') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Products') }}</h5>
                            </div>
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    @foreach ($order->orderStocks as $index => $orderStock)
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="mb-3 col-8">
                                                    <label class="form-label"
                                                        for="product-repeater-{{ $index + 1 }}-1">{{ 'Product' }}</label>
                                                    <select name="stocks[{{ $index }}][id]"
                                                        id="product-repeater-{{ $index + 1 }}-1"
                                                        class="product_select2_{{ $index + 1 }} form-select"
                                                        data-allow-clear="true">
                                                        <option value="{{ $orderStock->stock_id }}"
                                                            data-price="{{ $orderStock->price }}">
                                                            {{ $orderStock->stock->product->name }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-3">
                                                    <label class="form-label"
                                                        for="quantity-repeater-{{ $index + 1 }}-2">{{ _t('Quantity') }}</label>
                                                    <input type="number" value="{{ $orderStock->quantity }}"
                                                        name="stocks[{{ $index }}][quantity]"
                                                        id="quantity-repeater-{{ $index + 1 }}-2"
                                                        class="form-control stocks-quantity-{{ $index + 1 }}"
                                                        placeholder="{{ _t('Quantity') }}" required />
                                                </div>
                                                <div class="mb-3 col-1">
                                                    <label class="form-label" for="remover">{{ _t('') }}</label>
                                                    <button class="btn btn-danger repeater-remove"><i
                                                            class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-add">{{ _t('Add product') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bill-preview"></div>
                        <div class="row justify-content-start">
                            <div class="col-sm-10">
                                <button class="btn btn-primary" type="submit">{{ _t('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
