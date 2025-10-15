@extends('layouts/layoutMaster')

@section('title', 'New order')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
            let tax_percentage = "{{ $tax_percentage }}";
            // add customer
            $('#newCustomerForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'), // The form's action attribute value
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the success response here
                        toastr.success("{{ _t('Customer has been added') }}");
                        $('#newCustomerModal').modal('hide'); // Hide the modal
                    },
                    error: function(xhr) {
                        // Handle errors here
                        var errors = xhr.responseJSON.errors;
                        $('.text-danger').remove(); // Remove any existing error messages

                        $.each(errors, function(key, value) {
                            var input = $('[name="' + key + '"]');
                            input.after('<span class="text-danger">' + value[0] +
                                '</span>');
                        });
                    }
                });
            });
            // end add customer
            $('#submit-button').prop('disabled', true);
            // country code
            $('.country_code_select2').select2({});
            $('.payment_select2_1').select2({});
            $('.order_type_select2').select2({});
            $('.orderable_select2').select2({});
            $('.city_select2').select2({
                placeholder: '{{ _t('Select City') }}',
                ajax: {
                    url: route('city.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        console.log('data');
                        console.log(data.data.data)
                        return {
                            results: data.data.map(function(item) {
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
                                    text: item.name + " - " + item.phone,
                                    points: item.type.points
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
                            type: 'orders_hairdresser' // your custom parameter
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

            function branch_select2(element) {
                $(element).select2({
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
            }
            branch_select2('.branch_select2');
            branch_select2('.customer_branch_select2');


            function initializeProductSelect2(selector) {
                $(selector).select2({
                    placeholder: '{{ _t('Select Product') }}',
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
                                department: 2, // Cafeteria department ID
                                consumption_types: [1, 3], // sale and all
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.product.name + " - " + item.barcode + " - " +
                                            item.unit?.name,
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
                    placeholder: '{{ _t('Select Service') }}',
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
                                department: 2, // Cafeteria department ID
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
                            <div class="mb-3 col-8">
                                <label class="form-label" for="product-repeater-${currentCount+1}-1">{{ _t('Products') }}</label>
                                <select id="product-repeater-${currentCount+1}-2" name="stocks[${currentCount}][id]" class="product_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" value="1" name="stocks[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
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
            });

            $('.service-repeater-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.service-repeater-wrapper');
                var currentCount = repeaterWrapper.find('.service-repeater-item').length;
                var newRepeaterItem = `
                    <div class="service-repeater-item">
                        <div class="row">
                            <div class="mb-3 col-8">
                                <label class="form-label" for="service-repeater-${currentCount+1}-1">{{ _t('Service') }}</label>
                                <select id="service-repeater-${currentCount+1}-2" name="services[${currentCount}][id]" class="service_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="service-quantity-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" value="1" name="services[${currentCount}][quantity]" id="quantity-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger service-repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;

                repeaterWrapper.find('.service-repeater-items').append(newRepeaterItem);
                initializeServiceSelect2(`.service_select2_${currentCount+1}`);
            });
            $('.service-repeater-wrapper').on('click', '.service-repeater-remove', function(e) {
                e.preventDefault();
                var item = $(this).closest('.service-repeater-item');
                item.remove();
                updateSubmitButton();
            });

            $('.payment-repeater-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.payment-repeater-wrapper');
                // var maxAdvantages = 5; // Maximum allowed advantages
                var currentCount = repeaterWrapper.find('.payment-repeater-item').length;

                // if (currentCount < maxAdvantages) {
                // var template = repeaterWrapper.find('.repeater-item').html();
                // var item = $('<div class="repeater-item">' + template + '</div>');
                // item.find('.form-control').val(''); // Clear input values
                var newRepeaterItem = `
            <div class="payment-repeater-item">
                <div class="row">
                    <div class="mb-3 col-4">
                        <label class="form-label" for="product-repeater-1-1">{{ _t('Type') }}</label>
                        <select id="payment_type_${currentCount}" name="payments[${currentCount}][type]" class="payment_select2_${currentCount+1} form-select" data-allow-clear="false" required>
                            @foreach (\App\Enums\PaymentTypeEnum::cases() as $value)
                            <option value="{{ $value->value }}">{{ _t($value->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-3">
                        <label class="form-label" for="total-repeater-${currentCount}-2">{{ _t('Amount') }}</label>
                        <input type="number" name="payments[${currentCount}][amount]" value="0" id="total-repeater-${currentCount}-2" class="form-control total-repeater" placeholder="{{ _t('Amount') }}" required />
                    </div>
                    <div class="mb-3 col-3">
                        <label class="form-label" for="quantity-repeater-1-2">{{ _t('Paid') }}</label>
                        <div class="col-sm-10">
                            <label class="switch switch-primary me-0">
                                <input type="checkbox" class="switch-input" name="payments[${currentCount}][paid]" id="refundable_${currentCount}" name="refundable" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" checked />
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label"></span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3 col-1">
                        <label class="form-label" for="remover">{{ _t('') }}</label>
                        <button class="btn btn-danger repeater-remove" title="{{ _t('Remove') }}"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            </div>
            `;
                repeaterWrapper.find('.payment-repeater-items').append(newRepeaterItem);
                $(`.payment_select2_${currentCount + 1}`).select2({});
            });


            // function listenRepeater() {
            $(document).on('keyup', '[id^="total-repeater-"]', function() {
                updateSubmitButton();
            });
            // }
            $('.payment-repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var item = $(this).closest('.payment-repeater-item');
                item.remove();
                updateSubmitButton();
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


            async function updateSubmitButton() {
                let total = await getPaid();
                let total_amount = $('#total-amount-input')?.val() || 0;
                $('#left-total').text((total_amount - total).toFixed(2));
                $('#left').val((total_amount - total).toFixed(2));
                console.log("amounts", total_amount, total);
                if (total_amount - total > 0) {
                    $('#left-total').addClass('text-danger');
                } else {
                    $('#left-total').removeClass('text-danger');
                }
                $('#submit-button').prop('disabled', total_amount - total > 0);
            }

            async function getPaid() {
                let total = 0;
                $('[id^="total-repeater-"]').each(function() {
                    total += parseInt($(this).val() || 0);
                });
                return parseInt(total);
            }

            async function updateBillPreview() {
                let products = [];
                let services = [];

                // Function to fetch service products asynchronously
                // Iterate through product select2 elements
                $('[class^="product_select2_"]').each(function() {
                    let product = $(this).select2('data')[0];
                    if (product) {
                        let quantity = $(this).closest('.repeater-item').find(
                            'input[id^="quantity-repeater-"]').val() || 1;
                        products.push({
                            name: product.text,
                            quantity: quantity,
                            price: product.price,
                            total: product.price * quantity,
                            type: "{{ _t('Selection') }}"
                        });
                    }
                });
                $('[class^="service_select2_"]').each(function() {
                    let service = $(this).select2('data')[0];
                    if (service) {
                        let quantity = $(this).closest('.service-repeater-item').find(
                            'input[id^="service-quantity-repeater-"]').val() || 1;
                        services.push({
                            name: service.text,
                            quantity: quantity,
                            price: service.price,
                            total: service.price * quantity,
                            type: "{{ _t('Selection') }}"
                        });
                    }
                });


                // After fetching all data, render the bill preview
                await renderBillPreview(products, services);
            }

            async function renderBillPreview(products, services) {
                let totalAmount = 0;
                let grandTotal = 0;
                let tax = 0;
                let adminGiftAmount = 0;
                let tax_included = $('#tax_included').is(":checked");
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
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Quantity') }}</th>
                        <th>{{ _t('Price') }}</th>
                        <th>{{ _t('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                `;
                products.forEach(product => {
                    previewHtml += `
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.type}</td>
                        <td>${product.quantity}</td>
                        <td>{{ _t('SAR') }} ${product.price}</td>
                        <td>{{ _t('SAR') }} ${product.total}</td>
                    </tr>
                    `;
                    totalAmount += product.total;
                    tax += product.total * (tax_percentage / 100);
                });
                previewHtml += '</tbody></table>';
                // Render services table
                previewHtml += '<div class="card"><div class="card-body">';
                previewHtml += '<h5>{{ _t('Services') }}</h5>';
                previewHtml += '<table class="table">';
                previewHtml += `
                <thead>
                    <tr>
                        <th>{{ _t('Name') }}</th>
                        <th>{{ _t('Type') }}</th>
                        <th>{{ _t('Quantity') }}</th>
                        <th>{{ _t('Price') }}</th>
                        <th>{{ _t('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                `;
                services.forEach(service => {
                    previewHtml += `
                    <tr>
                        <td>${service.name}</td>
                        <td>${service.type}</td>
                        <td>${service.quantity}</td>
                        <td>{{ _t('SAR') }} ${service.price}</td>
                        <td>{{ _t('SAR') }} ${service.total}</td>
                    </tr>
                    `;
                    totalAmount += service.total;
                    tax += service.total * (tax_percentage / 100);
                });
                previewHtml += '</tbody></table>';
                grandTotal = totalAmount - discount;
                if (!tax_included) {
                    grandTotal += tax;
                } else {
                    totalAmount -= tax;
                }

                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Total') }}: {{ _t('SAR') }} ${(totalAmount).toFixed(2)}</h6>`;
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Discount') }}: {{ _t('SAR') }} ${discount.toFixed(2)}</h6>`;
                previewHtml +=
                    `<h6 class="mt-3">{{ _t('Tax') }}: {{ _t('SAR') }} ${tax.toFixed(2)}</h6>`;
                previewHtml +=
                    `<h6 class="mt-3" id="total-amount">{{ _t('Total Amount') }}: {{ _t('SAR') }} ${grandTotal.toFixed(2)}</h6>`;
                previewHtml +=
                    `<input type="hidden" name="total" id="total-amount-input" value="${grandTotal.toFixed(2)}" />`;
                previewHtml += '</div></div>';

                // Update HTML content
                $('#bill-preview').html(previewHtml);
                //update left total amount
                let total = await getPaid();
                $('#left-total').text((grandTotal.toFixed(2) - total).toFixed(2));
                if (grandTotal - total > 0) {
                    $('#left-total').addClass('text-danger');
                } else {
                    $('#left-total').removeClass('text-danger');
                }
                $('#submit-button').prop('disabled', total != (grandTotal));
            }
            $(document).on('keyup',
                '[id^="form-repeater-"], [id^="quantity-repeater-"], [id^="service-quantity-repeater-"]',
                function() {
                    updateBillPreview();
                });
            $('#tax_included').change(function() {
                updateBillPreview();
            });

            $(document).on('select2:select',
                '[class^="product_select2_"], [class^="service_select2_"]',
                function() {
                    updateBillPreview();
                });
            $(document).on('select2:select', '.coupon_select2', function() {
                updateBillPreview();
            });
            $('.orderable_select2').on('select2:select', function() {
                let orderable = $(this).val();
                let isCustomer = orderable == 2;
                let isEmployee = orderable == 3;

                $('.customer-div').toggleClass('d-none', !isCustomer);
                $('.employee-div').toggleClass('d-none', !isEmployee);

                $('.customer_select2').attr('required', isCustomer);
                $('.employee_select2').attr('required', isEmployee);
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
                    <h5 class="mb-0">{{ _t('Create Order') }}</h5> <small
                        class="text-muted float-end">{{ now() }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('cafeteria-order.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 orderable-div">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Orderable') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="orderable" name="type" class="orderable_select2 form-select"
                                            data-allow-clear="false">
                                            @foreach (App\Enums\OrderableTypeEnum::cases() as $value)
                                                <option value="{{ $value->value }}">{{ _t($value->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('orderable')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 customer-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Customer') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-9">
                                        <select id="customer_id" name="customer_id" class="customer_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                    <div class="col-sm-2" style="margin-left: 10px;">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#newCustomerModal">
                                            <!-- <i class="fa-solid fa-circle-plus"></i> -->
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('customer_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="customer_points_div row mb-3 d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email"></label>
                            <div class="col-sm-10">
                                <span id="customer_points" class="text-success"></span>
                            </div>
                        </div>
                        <div class="customer_history_div row mb-3 d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email"></label>
                            <div class="col-sm-10">
                                <a href="#" class="text-body text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#customerHistoryModal">
                                    <span id="customer_history" class="badge bg-label-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-custom-class="tooltip-primary"
                                        title="{{ _t('Click to view customer purchase history') }}">
                                        {{ _t('Customer History') }}
                                    </span>
                                </a>
                            </div>
                        </div> --}}
                        <div class="row mb-3 employee-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Employee') }}
                                *</label>
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
                        <div class="row mb-3 {{ $user_branch ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Branch') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="branch_id" name="branch_id" class="branch_select2 form-select"
                                            data-allow-clear="true" required>
                                            @if ($user_branch)
                                                <option value="{{ $user_branch->id }}">{{ _t($user_branch->name) }}
                                                </option>
                                            @endif
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
                                        aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Coupon') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="coupon_id" name="coupon_id" class="coupon_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                </div>
                                @error('coupon_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row mb-3 d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Tax Included') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input" name="tax_included" checked
                                            id="tax_included" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('tax_included')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Services') }}</h5>
                            </div>
                            <div class="card-body service-repeater-wrapper">
                                <div class="service-repeater-items">
                                    <div class="service-repeater-item">
                                        <div class="row">
                                            <div class="mb-3 col-8">
                                                <label class="form-label"
                                                    for="service-repeater-1-1">{{ 'Service' }}</label>
                                                <select name="services[0][id]" id="service-repeater-1-1"
                                                    class="service_select2_1 form-select" data-allow-clear="true">
                                                </select>
                                            </div>
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="service-quantity-repeater-1-2">{{ _t('Quantity') }}</label>
                                                <input type="number" name="services[0][quantity]"
                                                    id="service-quantity-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Quantity') }}" value="1" required />
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-danger service-repeater-remove"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary service-repeater-add"><i
                                                class="fa-solid fa-plus"></i></button>
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
                                    {{-- <div class="repeater-item">
                                        <div class="row">
                                            <div class="mb-3 col-8">
                                                <label class="form-label"
                                                    for="product-repeater-1-1">{{ 'Products' }}</label>
                                                <select name="stocks[0][id]" id="product-repeater-1-1"
                                                    class="product_select2_1 form-select" data-allow-clear="true">
                                                </select>
                                            </div>
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="quantity-repeater-1-2">{{ _t('Quantity') }}</label>
                                                <input type="number" name="stocks[0][quantity]"
                                                    id="quantity-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Quantity') }}" required />
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-danger repeater-remove"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-add"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Payments') }}</h5>
                            </div>
                            <div class="card-body payment-repeater-wrapper">
                                <div class="payment-repeater-items">
                                    <div class="payment-repeater-item replace-points-div d-none">
                                        <div class="row">
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="quantity-repeater-1-2">{{ _t('Replace Points') }}</label>
                                            </div>
                                            <div class="mb-3 col-3">
                                                <span class="points-to-replace"></span>
                                            </div>
                                            <div class="mb-3 col-3">
                                                <div class="col-sm-10">
                                                    <label class="switch switch-primary me-0">
                                                        <input type="checkbox" class="switch-input" name="points"
                                                            id="points" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseExample" aria-expanded="false"
                                                            aria-controls="collapseExample" />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"></span>
                                                            <span class="switch-off"></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="payment-repeater-item">
                                        <div class="row">
                                            <div class="mb-3 col-4">
                                                <label class="form-label"
                                                    for="product-repeater-1-1">{{ _t('Type') }}</label>
                                                <select id="payment_type" name="payments[0][type]"
                                                    class="payment_select2_1 form-select" data-allow-clear="false"
                                                    required>
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
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="total-repeater-1-2">{{ _t('Amount') }}</label>
                                                <input type="number" name="payments[0][amount]" id="total-repeater-1-2"
                                                    value="0" class="form-control total-repeater-1"
                                                    placeholder="{{ _t('Amount') }}" required />
                                            </div>
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="quantity-repeater-1-2">{{ _t('Paid') }}</label>
                                                <div class="col-sm-10">
                                                    <label class="switch switch-primary me-0">
                                                        <input type="checkbox" class="switch-input"
                                                            name="payments[0][paid]" id="refundable" name="refundable"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                                            aria-expanded="false" aria-controls="collapseExample"
                                                            checked />
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"></span>
                                                            <span class="switch-off"></span>
                                                        </span>
                                                        <span class="switch-label"></span>
                                                    </label>
                                                </div>
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
                                <input type="hidden" id="left" name="left" value="0">
                                <h6 class="text-start">{{ _t('Remaining') }}: <span id="left-total"
                                        class="text">0.00</span></h6>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary payment-repeater-add"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bill-preview"></div>
                        <div class="row justify-content-start">
                            <div class="col-sm-10">
                                <button id="submit-button" class="btn btn-primary"
                                    type="submit">{{ _t('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-onboarding modal fade animate__animated" id="newCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('New Customer') }}</h4>
                        <form id="newCustomerForm" method="POST" action="{{ route('customer.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @include('models.newCustomer')
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-onboarding modal fade animate__animated" id="customerHistoryModal" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('Customer History') }}</h4>
                        <div class="history-div"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->
@endsection
