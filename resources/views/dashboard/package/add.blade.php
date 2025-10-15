@extends('layouts/layoutMaster')

@section('title', 'New package')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
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
                                        text: item.product.name + " - " + item.barcode + " - " +
                                            item.unit?.name + " - " + item.price +
                                            " {{ _t('SAR') }}",
                                        price: item.price
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            initializeProductSelect2('.product_select2_1');

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
                                        text: item.name + " - " + item.price +
                                            " {{ _t('SAR') }}",
                                        price: item.price
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            initializeServiceSelect2('.service_select2_1');

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
                            <div class="mb-3 col-6">
                                <label class="form-label" for="product-repeater-${currentCount+1}-1">{{ 'Products' }}</label>
                                <select id="product-repeater-${currentCount+1}-2" name="stocks[${currentCount}][id]" class="product_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="stocks[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('Price') }}</label>
                                <input type="number" name="stocks[${currentCount}][price]" id="price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Price') }}" required />
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
                            <div class="mb-3 col-6">
                                <label class="form-label" for="service-repeater-${currentCount+1}-1">{{ 'Services' }}</label>
                                <select id="service-repeater-${currentCount+1}-2" name="services[${currentCount}][id]" class="service_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="form-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" name="services[${currentCount}][quantity]" id="form-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="service-price-repeater-${currentCount+1}-2">{{ _t('Price') }}</label>
                                <input type="number" name="services[${currentCount}][price]" id="service-price-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Price') }}" required />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-service-items').append(newRepeaterItem);
                initializeServiceSelect2(`.service_select2_${currentCount+1}`);
            });


            $('.repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                // var repeaterItems = $(this).closest('.repeater-items');
                var item = $(this).closest('.repeater-item');
                // if (repeaterItems.children('.repeater-item').length > 1) {
                item.remove();
                updateBillPreview();
                // // } else {
                // Swal.fire({
                // icon: 'error',
                // title: 'Oops..',
                // text: "At least one product is required",
                // });
                // }
            });
            $('.repeater-service-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var repeaterItems = $(this).closest('.repeater-service-items');
                var item = $(this).closest('.repeater-service-item');
                item.remove();
                updateBillPreview();
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
                        let price = $(this).closest('.repeater-item').find(
                            'input[id^="price-repeater-"]').val() || 1;
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
                        let service_products = await fetchServiceProducts(service.id);

                        let quantity = serviceElement.closest('.repeater-service-item').find(
                            'input[id^="form-repeater-"]').val() || 1;
                        let price = serviceElement.closest('.repeater-service-item').find(
                            'input[id^="service-price-repeater-"]').val() || 0;
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
                previewHtml += '</tbody></table>';

                // Render total amount, discount, and final total
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Total') }}: {{ _t('SAR') }} ${(totalAmount).toFixed(2)}</h6>`;
                // previewHtml += `<h6 class="mt-3">{{ _t('Extra Fees') }}: {{ _t('SAR') }} 0.00 </h6>`;
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Discount') }}: {{ _t('SAR') }} ${(discount).toFixed(2)}</h6>`;
                previewHtml +=
                    `<h6 class="mt-3" id="total-amount">{{ _t('Total Amount') }}: {{ _t('SAR') }} ${(totalAmount - discount).toFixed(2)}</h6>`;
                previewHtml +=
                    `<input type="hidden" name="total" id="total-amount-input" value="${(totalAmount - discount).toFixed(2)}" />`;
                previewHtml += '</div></div>';

                // Update HTML content
                $('#bill-preview').html(previewHtml);
                //update left total amount
                let total = await getPaid();
                $('#left-total').text(((totalAmount - discount).toFixed(2) - total).toFixed(2));
            }
            $(document).on('keyup',
                '[id^="form-repeater-"], [id^="quantity-repeater-"], [id^="price-repeater-"], [id^="service-price-repeater-"]',
                function() {
                    updateBillPreview();
                });

            $(document).on('select2:select', '[class^="product_select2_"], [class^="service_select2_"]',
                function() {
                    updateBillPreview();
                });
            $(document).on('select2:select', '.coupon_select2', function() {
                updateBillPreview();
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
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('package.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Image') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-image"></i></span>
                                    <input type="file" name="image" value="{{ old('image') }}"
                                        value="{{ old('image') }}" id="basic-icon-default-email" accept="image/*"
                                        class="form-control" aria-describedby="basic-icon-default-email2" />
                                </div>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="flatpickr-datetime">{{ _t('Name') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                        id="basic-icon-default-date" placeholder="{{ _t('Name') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('name')
                                    <span class=" text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="flatpickr-datetime">{{ _t('Start Date') }}</label>
                            <div class="col-sm-10">
                                @php
                                    // Get the current date and time
                                    $now = \Carbon\Carbon::now()->format('Y-m-d');
                                @endphp
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="date" name="start_date" min="{{ $now }}"
                                        value="{{ old('start_date') }}" class="form-control" id="basic-icon-default-date"
                                        placeholder="{{ _t('Start Date') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                    <!-- <input type="text" class="form-control" name="date" min="{{ $now }}" value="{{ old('date') }}" placeholder="YYYY-MM-DD HH:MM" id="flatpickr-datetime" /> -->
                                </div>
                                @error('start_date')
                                    <span class=" text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="flatpickr-datetime">{{ _t('End Date') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="date" name="end_date" min="{{ $now }}"
                                        value="{{ old('end_date') }}" class="form-control" id="basic-icon-default-date"
                                        placeholder="{{ _t('End Date') }}" aria-describedby="basic-icon-default-fullname2"
                                        required />
                                    <!-- <input type="text" class="form-control" name="date" min="{{ $now }}" value="{{ old('date') }}" placeholder="YYYY-MM-DD HH:MM" id="flatpickr-datetime" /> -->
                                </div>
                                @error('end_date')
                                    <span class=" text-danger">{{ $message }}</span>
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
                                    <textarea name="description" class="form-control" id="basic-icon-default-fullsku"
                                        placeholder="{{ _t('Description') }}" aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Services') }}</h5>
                            </div>
                            @error('services')
                                <span class=" text-danger">{{ $message }}</span>
                            @enderror
                            <div class="card-body repeater-service-wrapper">
                                <div class="repeater-service-items">
                                    <div class="repeater-service-item">
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label"
                                                    for="service-repeater-1-1">{{ 'Services' }}</label>
                                                <select id="service-repeater-1-1" name="services[0][id]"
                                                    class="service_select2_1 form-select" data-allow-clear="true"
                                                    required>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-2">
                                                <label class="form-label"
                                                    for="form-repeater-1-2">{{ _t('Quantity') }}</label>
                                                <input type="number" name="services[0][quantity]" id="form-repeater-1-2"
                                                    class="form-control" min="1"
                                                    placeholder="{{ _t('Quantity') }}" required />
                                            </div>
                                            <div class="mb-3 col-2">
                                                <label class="form-label"
                                                    for="service-price-repeater-1-2">{{ _t('Price') }}</label>
                                                <input type="number" name="services[0][price]"
                                                    id="service-price-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Price') }}" min="0" required />
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-danger repeater-remove"
                                                    title="{{ _t('Remove') }}"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-service-add"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Products') }}</h5>
                            </div>
                            @error('products')
                                <span class=" text-danger">{{ $message }}</span>
                            @enderror
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    <div class="repeater-item">
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label"
                                                    for="product-repeater-1-1">{{ 'Products' }}</label>
                                                <select name="stocks[0][id]" id="product-repeater-1-1"
                                                    class="product_select2_1 form-select" data-allow-clear="true"
                                                    required>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-2">
                                                <label class="form-label"
                                                    for="quantity-repeater-1-2">{{ _t('Quantity') }}</label>
                                                <input type="number" min="1" name="stocks[0][quantity]"
                                                    id="quantity-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Quantity') }}" required />
                                            </div>
                                            <div class="mb-3 col-2">
                                                <label class="form-label"
                                                    for="price-repeater-1-2">{{ _t('Price') }}</label>
                                                <input type="number" min="0" name="stocks[0][price]"
                                                    id="price-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Price') }}" required />
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-danger repeater-remove"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
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
                        <!-- <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Products') }}</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                                            <div class="col-sm-10">
                                                                <select id="products" name="stocks[]" class="product_select2 form-select" data-allow-clear="true" multiple>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @error('products')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Services') }}</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group input-group-merge">
                                                            <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                                            <div class="col-sm-10">
                                                                <select id="services" name="services[]" class="service_select2 form-select" data-allow-clear="true" required multiple>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @error('services')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                                                    </div>
                                                </div> -->
                        <div id="bill-preview"></div>
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
