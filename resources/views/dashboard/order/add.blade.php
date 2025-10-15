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
            // gift
            let point_to_cash = "{{ $point_to_cash }}";
            let admin_gift = false;
            let replace_point = false;
            let customer_points = 0;
            let cash_from_points = 0;
            let tax_percentage = "{{ $tax_percentage }}";
            $('#order_type').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'gift') {
                    $('#gifter_div').removeClass('d-none');
                    admin_gift = true;
                } else {
                    $('#gifter_div').addClass('d-none');
                    admin_gift = false;
                }
                updateBillPreview();
            });
            $('#gifter').change(function() {
                admin_gift = $(this).val() == 'admin';
                updateBillPreview();
            });
            $('#tax_included').change(function() {
                updateBillPreview();
            });
            $('#customer_id').change(async function() {
                $('.customer_history_div').removeClass('d-none');
                let customer_data = $(this).select2('data')[0];
                customer_points = customer_data.points || 0;
                if (customer_points > 0) {
                    if (point_to_cash)
                        cash_from_points = customer_points * point_to_cash;
                    $('.customer_points_div').removeClass('d-none');
                    $('.replace-points-div').removeClass('d-none');
                    $('#customer_points').text(
                        `{{ _t('Customer has') }} ${customer_points} {{ _t('point') }}`);
                    $('.points-to-replace').text(
                        `${customer_points} {{ _t('point') }}: ${cash_from_points} {{ _t('SAR') }}`
                    );
                } else {
                    $('.customer_points_div').addClass('d-none');
                    $('.replace-points-div').addClass('d-none');
                }
                let orders = await fetchCustomerOrders(customer_data.id);
                if (orders.length > 0) {
                    renderHistoryModal(orders);
                }
            });

            async function fetchCustomerOrders(customerId) {
                try {
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    const response = await $.ajax({
                        url: route('customer.orders', customerId),
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + csrfToken
                        }
                    });
                    return response.data.data;
                } catch (error) {
                    console.error('Error fetching service products:', error);
                    return [];
                }
            }

            function renderHistoryModal(orders) {
                console.log("orders");
                console.log(orders);
                let historyHtml = '<div class="card-body">';
                historyHtml += '<div class="card"><div class="card-body">';
                historyHtml += '<h5>{{ _t('Products') }}</h5>';
                historyHtml += '<table class="table">';
                historyHtml += `
        <thead>
            <tr>
                <th>{{ _t('Order') }}</th>
                <th>{{ _t('Name') }}</th>
                <th>{{ _t('Quantity') }}</th>
                <th>{{ _t('Price') }}</th>
                <th>{{ _t('Total') }}</th>
                <th>{{ _t('Date') }}</th>
            </tr>
        </thead>
        <tbody>`;
                orders.forEach(order => {
                    order.order_stocks.forEach(order_stock => {
                        historyHtml += `
            <tr>
                <td><a href="${route('order.show', order_stock.order_id)}" target="_blank">${order_stock.order_id}</a></td>
                <td>${order_stock.stock.product.name}</td>
                <td>${order_stock.quantity}</td>
                <td>{{ _t('SAR') }} ${order_stock.price}</td>
                <td>{{ _t('SAR') }} ${order_stock.price * order_stock.quantity}</td>
                <td>${order_stock.created_at}</td>
            </tr>`;
                    });
                });
                historyHtml += '</tbody></table>';

                // Render services table
                historyHtml += '<h5 class="mt-3">{{ _t('Services') }}</h5>';
                historyHtml += '<table class="table">';
                historyHtml += `
        <thead>
            <tr>
                <th>{{ _t('Order') }}</th>
                <th>{{ _t('Name') }}</th>
                <th>{{ _t('Quantity') }}</th>
                <th>{{ _t('Price') }}</th>
                <th>{{ _t('Total') }}</th>
                <th>{{ _t('Date') }}</th>
            </tr>
        </thead>
        <tbody>`;
                orders.forEach(order => {
                    order.order_services.forEach(order_service => {
                        historyHtml += `
            <tr>
                <td><a href="${route('order.show', order_service.order_id)}" target="_blank">${order_service.order_id}</a></td>
                <td>${order_service.service.name}</td>
                <td>${order_service.quantity}</td>
                <td>{{ _t('SAR') }} ${order_service.price}</td>
                <td>{{ _t('SAR') }} ${order_service.price * order_service.quantity}</td>
                <td>${order_service.created_at}</td>
            </tr>`;
                    });
                });
                historyHtml += '</tbody></table>';

                historyHtml += '</div></div></div>';

                // Update HTML content
                $('.history-div').html(historyHtml);
            }

            $('#points').change(function() {
                replace_point = $(this).is(":checked");
                updateBillPreview();
            });

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
            $('.gifter_select2').select2({});
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            toastr.error("You do not have permission! Please contanct admin.");
                        } else if (jqXHR.status === 501) {
                            toastr.error("Unauthorized! Please login.");
                        } else if (jqXHR.status === 500) {
                            toastr.error('Server error! Please contact support.');
                        }
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            toastr.error("You do not have permission! Please contanct admin.");
                        } else if (jqXHR.status === 501) {
                            toastr.error("Unauthorized! Please login.");
                        } else if (jqXHR.status === 500) {
                            toastr.error('Server error! Please contact support.');
                        }
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            toastr.error("You do not have permission! Please contanct admin.");
                        } else if (jqXHR.status === 501) {
                            toastr.error("Unauthorized! Please login.");
                        } else if (jqXHR.status === 500) {
                            toastr.error('Server error! Please contact support.');
                        }
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
                        error: function(jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status === 403) {
                                toastr.error("You do not have permission! Please contanct admin.");
                            } else if (jqXHR.status === 501) {
                                toastr.error("Unauthorized! Please login.");
                            } else if (jqXHR.status === 500) {
                                toastr.error('Server error! Please contact support.');
                            }
                        },
                        cache: false
                    }
                });
            }
            branch_select2('.branch_select2');
            branch_select2('.customer_branch_select2');
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
                                    text: item.name + " - " + item.code,
                                    discount: item.discount,
                                    is_percentage: item.is_percentage,
                                    services: item.services,
                                    products: item.products
                                };
                            })
                        };
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            toastr.error("You do not have permission! Please contanct admin.");
                        } else if (jqXHR.status === 501) {
                            toastr.error("Unauthorized! Please login.");
                        } else if (jqXHR.status === 500) {
                            toastr.error('Server error! Please contact support.');
                        }
                    },
                    cache: false
                }
            });

            function initPackageEmployeeSelect() {
                $('[class^="package_employee_select2_"]').select2({
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
            }

            function initializeCardSelect2(selector) {
                $(selector).select2({
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
            }

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
                                department: 1, // Salon department ID
                                consumption_types: [1, 3],
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.product.name + " - " + item.barcode + " - " +
                                            item.unit?.name,
                                        price: item.price,
                                        product_id: item.product_id
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
                                q: params.term,
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
            initializeServiceSelect2('.service_select2_1');
            initializeCardSelect2('.card_select2_1')

            function initializePackageSelect2(selector) {
                $(selector).select2({
                    placeholder: '{{ _t('Select Package') }}',
                    ajax: {
                        url: route('package.search'),
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                date: "{{ date('Y-m-d') }}"
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + " - " + item.total + " SAR",
                                        price: item.total
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }

            function initializeEmployeeSelect2(selector) {
                $(selector).select2({
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
            }
            initializeEmployeeSelect2('.employee_select2_1');

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

            $('.packages-repeater-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.package-repeater-wrapper');
                var currentCount = repeaterWrapper.find('.package-repeater-item').length;
                var newRepeaterItem = `
                    <div class="package-repeater-item">
                        <div class="row">
                            <div class="mb-3 col-8">
                                <label class="form-label" for="package-select-${currentCount+1}-1">{{ _t('package') }}</label>
                                <select name="packages[${currentCount}][id]" id="package-select-${currentCount+1}-1" class="package_select2_${currentCount+1} form-select">
                                </select>
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.package-repeater-items').append(newRepeaterItem);
                initializePackageSelect2(`.package_select2_${currentCount+1}`);
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
                    <div class="mb-3 col-3">
                        <label class="form-label" for="product-repeater-1-1">{{ _t('Type') }}</label>
                        <select data-id="${currentCount + 1}" id="payment_type_${currentCount}" name="payments[${currentCount}][type]" class="payment_select2_${currentCount+1} form-select" data-allow-clear="false" required>
                            @foreach (\App\Enums\PaymentTypeEnum::cases() as $value)
                            <option value="{{ $value->value }}">{{ _t($value->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-3 d-none card-div-${currentCount + 1}">
                        <label class="form-label" for="product-repeater-1-1">{{ _t('Card') }}</label>
                        <select id="card_${currentCount}" name="payments[${currentCount}][card_id]" class="card_select2_${currentCount+1} form-select" data-allow-clear="true">
                        </select>
                    </div>
                    <div class="mb-3 col-3">
                        <label class="form-label" for="total-repeater-${currentCount}-2">{{ _t('Amount') }}</label>
                        <input type="number" step="any" name="payments[${currentCount}][amount]" value="0" id="total-repeater-${currentCount}-2" class="form-control total-repeater" placeholder="{{ _t('Amount') }}" required />
                    </div>
                    <div class="mb-3 col-2">
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
                initializeCardSelect2(`.card_select2_${currentCount+1}`);
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

            //package repeater remover
            $('.package-repeater-wrapper').on('click', '.repeater-remove', function(e) {
                console.log("remover");
                e.preventDefault();
                var item = $(this).closest('.package-repeater-item');
                item.remove();
                updateBillPreview();
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
                            <div class="mb-3 col-4">
                                <label class="form-label" for="service-repeater-${currentCount+1}-1">{{ _t('Services') }}</label>
                                <select id="service-repeater-${currentCount+1}-2" name="services[${currentCount}][id]" class="service_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="employee-repeater-${currentCount+1}-1">{{ _t('Employee') }}</label>
                                <select id="employee-repeater-${currentCount+1}-1" name="services[${currentCount}][employee]" class="employee_select2_${currentCount+1} form-select" data-allow-clear="true">
                                </select>
                            </div>
                            <div class="mb-3 col-2">
                                <label class="form-label" for="form-repeater-${currentCount+1}-2">{{ _t('Quantity') }}</label>
                                <input type="number" value="1" name="services[${currentCount}][quantity]" id="form-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
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
                initializeEmployeeSelect2(`.employee_select2_${currentCount+1}`);
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
                        <div class="mb-3 col-4">
                            <label class="form-label" for="session_count-${itemIndex}">{{ _t('Sessions Count') }}</label>
                            <input type="number" min="1" name="services[${itemIndex}][session_count]" id="session_count-${itemIndex}" class="form-control" placeholder="{{ _t('Sessions Count') }}" required />
                        </div>
                        <div class="mb-3 col-4">
                            <label class="form-label" for="due_date-${itemIndex}">{{ _t('Due Date') }}</label>
                            <input type="date" name="services[${itemIndex}][due_date]" id="due_date-${itemIndex}" class="form-control" placeholder="{{ _t('Due Date') }}" required />
                        </div>
                    </div>
                `;
                    // <div class="mb-3 col-4">
                    //             <label class="form-label" for="session_price-${itemIndex}">{{ _t('Extra Fees') }}</label>
                    //             <input type="number" name="services[${itemIndex}][session_price]" id="session_price-${itemIndex}" class="form-control" placeholder="{{ _t('Extra Fees') }}" />
                    //         </div>
                    currentItem.append(newSessionFields);
                    $(this).removeClass('btn-info').addClass('btn-danger');
                }
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
                // if (repeaterItems.children('.repeater-service-item').length > 1) {
                item.remove();
                updateBillPreview();
                // } else {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Oops..',
                //         text: "At least one service is required",
                //     });
                // }
            });


            async function updateSubmitButton() {
                let total = await getPaid();
                console.log("total", total);
                let total_amount = $('#total-amount-input')?.val() || 0;
                $('#left-total').text((total_amount - total).toFixed(2));
                $('#left').val((total_amount - total).toFixed(2));
                if (total_amount - total > 0) {
                    $('#left-total').addClass('text-danger');
                } else {
                    $('#left-total').removeClass('text-danger');
                }
                $('#submit-button').prop('disabled', total < total_amount);
            }

            async function getPaid() {
                let total = 0;
                $('[id^="total-repeater-"]').each(function() {
                    total += parseFloat($(this).val() || 0);
                });
                return parseFloat(total);
            }

            async function updateBillPreview() {
                let products = [];
                let services = [];
                let packages = [];

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

                async function fetchPackageItems(packageId) {
                    try {
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        const response = await $.ajax({
                            url: route('package.items', packageId),
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + csrfToken
                            }
                        });
                        return response.data; // Assuming products is an array of product objects
                    } catch (error) {
                        console.error('Error fetching package items:', error);
                        return []; // Return empty array or handle error as needed
                    }
                }

                for (let i = 0; i < $('[class^="package_select2"]').length; i++) {
                    let serviceElement = $('[class^="package_select2"]').eq(i);
                    let package = serviceElement.select2('data')[0];
                    var currentItemRepeater = serviceElement.closest('.package-repeater-item');
                    let items = await fetchPackageItems(package.id);
                    console.log("items");
                    console.log(items);
                    let package_services_html = '<div class="package-service-items">';
                    items.forEach(item => {
                        if (item.item_type == 'App\\Models\\Service') {
                            services.push({
                                name: item.item.name,
                                quantity: item.quantity,
                                price: item.price,
                                total: item.price * item.quantity,
                                products: item.item.product_services,
                                type: "{{ _t('Package') }}"
                            });
                            package_services_html += `
                        <div class="row">
                            <div class="mb-3 col-4">
                                <label class="form-label" for="package-service-${item.id}-1">{{ _t('Service') }}</label>
                                <select name="packages[${i}][services][${item.id}][id]" id="package-service-${item.id}-1" class="form-select" disabled>
                                <option value="${item.id}" selected>${item.item.name}</option>
                                </select>
                                <input type="hidden" name="packages[${i}][services][${item.id}][id]" value="${item.id}">
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="package-employee-select-${item.item.id}-1">{{ _t('Employee') }}</label>
                                <select name="packages[${i}][services][${item.id}][employee]" id="package-employee-select-${item.item.id}-1" class="package_employee_select2_${item.item.id} form-select">
                                </select>
                            </div>
                        </div>
                        `;
                        } else if (item.item_type == 'App\\Models\\Stock') {
                            package_services_html += `
                        <input type="hidden" name="packages[${i}][stocks][${item.id}][id]" value="${item.id}">
                        `;
                            products.push({
                                name: item.item.product.name,
                                quantity: item.quantity,
                                price: item.price,
                                total: item.price * item.quantity,
                                type: "{{ _t('Package') }}"
                            });
                        }
                    });
                    package_services_html += `</div>`;
                    currentItemRepeater.find('.package-service-items').remove();
                    currentItemRepeater.append(package_services_html);
                    initPackageEmployeeSelect();
                    if (package) {
                        // let quantity = $(this).closest('.repeater-item').find('input[id^="quantity-repeater-"]').val() || 1;
                        packages.push({
                            name: package.text,
                            price: package.price
                        });
                    }
                }
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
                            type: "{{ _t('Selection') }}",
                            product_id: product.id
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
                        services.push({
                            id: service.id,
                            name: service.text,
                            quantity: quantity,
                            price: service.price,
                            total: service.price * quantity,
                            products: service_products,
                            type: "{{ _t('Selection') }}"
                        });
                    }
                }

                // After fetching all data, render the bill preview
                await renderBillPreview(products, services, packages);
            }

            async function renderBillPreview(products, services, packages) {
                let totalAmount = 0;
                let grandTotal = 0;
                let adminGiftAmount = 0;
                let discount = 0;
                let tax = 0;
                let tax_included = $('#tax_included').is(":checked");
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
        <tbody>`;
                products.forEach(product => {
                    previewHtml += `
            <tr>
                <td>${product.name}</td>
                <td>${product.type}</td>
                <td>${product.quantity}</td>
                <td>{{ _t('SAR') }} ${product.price}</td>
                <td>{{ _t('SAR') }} ${product.total}</td>
            </tr>`;
                    totalAmount += product.total;
                    tax += product.total * (tax_percentage / 100);
                });
                previewHtml += '</tbody></table>';

                // Render services table
                previewHtml += '<h5 class="mt-3">{{ _t('Services') }}</h5>';
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
        <tbody>`;
                services.forEach(service => {
                    previewHtml += `
            <tr>
                <td>${service.name}</td>
                <td>${service.type}</td>
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
                    tax += service.total * (tax_percentage / 100);
                });
                let selectedCoupon = $('.coupon_select2').select2('data')[0];
                let couponServices = [];
                let couponProducts = [];
                if (selectedCoupon) {
                    couponServices = selectedCoupon.services.map(service => service.id);
                    couponProducts = selectedCoupon.products.map(product => product.id);
                    if (couponServices.length === 0) {
                        services.forEach(service => {
                            discount += (service.total * selectedCoupon.discount / 100);
                        });
                    } else {
                        services.forEach(service => {
                            if (couponServices.includes(Number(service.id))) {
                                discount += (service.total * selectedCoupon.discount / 100);
                            }
                        });
                    }
                    if (couponProducts.length === 0) {
                        products.forEach(product => {
                            discount += (product.total * selectedCoupon.discount / 100);
                        });
                    } else {
                        products.forEach(product => {
                            console.log('product', product);
                            console.log('couponProducts', couponProducts);
                            if (couponProducts.includes(Number(product.product_id))) {
                                discount += (product.total * selectedCoupon.discount / 100);
                            }
                        });
                    }
                }
                previewHtml += '</tbody></table>';

                // Render packages table
                previewHtml += '<h5>{{ _t('Packages') }}</h5>';
                previewHtml += '<table class="table">';
                previewHtml += `
            <thead>
                <tr>
                    <th>{{ _t('Name') }}</th>
                    <th>{{ _t('Price') }}</th>
                </tr>
            </thead>
            <tbody>`;
                packages.forEach(package => {
                    previewHtml += `
            <tr>
                <td>${package.name}</td>
                <td>${package.price}</td>
            </tr>`;
                    // totalAmount += package.price;
                });
                previewHtml += '</tbody></table>';

                // Render total amount, discount, and final total
                if (admin_gift) {
                    discount = adminGiftAmount = totalAmount = 0;
                    // previewHtml += `<h6 class="mt-3">{{ _t('Admin Gift') }}: {{ _t('SAR') }} ${adminGiftAmount} </h6>`;
                }
                if (replace_point) {
                    discount += cash_from_points;
                    if (discount > totalAmount)
                        discount = totalAmount;
                    console.log(discount);
                }
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
            $(document).on('keyup', '[id^="form-repeater-"], [id^="quantity-repeater-"]', function() {
                updateBillPreview();
            });

            $(document).on('select2:select',
                '[class^="product_select2_"], [class^="service_select2_"], [class^="package_select2_"]',
                function() {
                    updateBillPreview();
                });
            $(document).on('select2:select', '.coupon_select2', function() {
                updateBillPreview();
            });
            $(document).on('change', '[class^="payment_select2_"]', function() {
                let type = $(this).val();
                let id = $(this).data('id'); // Get the data-id from the selected element

                if (type != 1) {
                    $(`.card-div-${id}`).removeClass('d-none');
                } else {
                    $(`.card-div-${id}`).addClass('d-none');
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
                    <h5 class="mb-0">{{ _t('Create Order') }}</h5> <small
                        class="text-muted float-end">{{ now() }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('order.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="order_type" name="type" class="order_type_select2 form-select"
                                            data-allow-clear="false">
                                            <option value="normal">{{ _t('Normal') }}</option>
                                            <option value="gift">{{ _t('Gift') }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('order_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div id="gifter_div" class="d-none">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Gifter') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                        <div class="col-sm-10">
                                            <select id="gifter" name="gifter_id" class="gifter_select2 form-select"
                                                data-allow-clear="false">
                                                <option value="admin">{{ _t('Admin') }}</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->type_id }}">
                                                        {{ $customer->name . ' - ' . $customer->phone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('order_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('End Date') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                        <div class="col-sm-10">
                                            <input id="gift_end_date" name="gift_end_date" type="date"
                                                min="{{ date('Y-m-d') }}" class="form-control">
                                        </div>
                                    </div>
                                    @error('gift_end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Customer') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-9">
                                        <select id="customer_id" name="customer_id" class="customer_select2 form-select"
                                            data-allow-clear="true" required>
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
                        <div class="customer_points_div row mb-3 d-none">
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
                        </div>
                        <div class="row mb-3">
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
                                    <textarea name="description" class="form-control" id="basic-icon-default-fullsku"
                                        placeholder="{{ _t('Description') }}" aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('description') }}</textarea>
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
                                        </select>
                                    </div>
                                </div>
                                @error('coupon_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
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
                            <div class="card-body repeater-service-wrapper">
                                <div class="repeater-service-items">
                                    <div class="repeater-service-item">
                                        <div class="row">
                                            <div class="mb-3 col-4">
                                                <label class="form-label"
                                                    for="service-repeater-1-1">{{ _t('Services') }}</label>
                                                <select id="service-repeater-1-1" name="services[0][id]"
                                                    class="service_select2_1 form-select" data-allow-clear="true"
                                                    required>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-4">
                                                <label class="form-label"
                                                    for="employee-repeater-1-1">{{ _t('Employee') }}</label>
                                                <select id="employee-repeater-1-1" name="services[0][employee]"
                                                    class="employee_select2_1 form-select" data-allow-clear="true">
                                                </select>
                                            </div>
                                            <div class="mb-3 col-2">
                                                <label class="form-label"
                                                    for="form-repeater-1-2">{{ _t('Quantity') }}</label>
                                                <input type="number" value="1" name="services[0][quantity]"
                                                    id="form-repeater-1-2" class="form-control"
                                                    placeholder="{{ _t('Quantity') }}" required />
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-danger repeater-remove"
                                                    title="{{ _t('Remove') }}"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </div>
                                            <div class="mb-3 col-1">
                                                <label class="form-label" for="remover">{{ _t('') }}</label>
                                                <button class="btn btn-info repeater-session"
                                                    title="{{ _t('Session') }}"><i
                                                        class="fa-solid fa-calendar-days"></i></button>
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
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    <!-- <div class="repeater-item">
                                                                                                                                                                                                                                                                                                                                                                    <div class="row">
                                                                                                                                                                                                                                                                                                                                                                        <div class="mb-3 col-8">
                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label" for="product-repeater-1-1">{{ 'Products' }}</label>
                                                                                                                                                                                                                                                                                                                                                                            <select name="stocks[0][id]" id="product-repeater-1-1" class="product_select2_1 form-select" data-allow-clear="true">
                                                                                                                                                                                                                                                                                                                                                                            </select>
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                        <div class="mb-3 col-3">
                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label" for="quantity-repeater-1-2">{{ _t('Quantity') }}</label>
                                                                                                                                                                                                                                                                                                                                                                            <input type="number" name="stocks[0][quantity]" id="quantity-repeater-1-2" class="form-control" placeholder="{{ _t('Quantity') }}" required />
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                        <div class="mb-3 col-1">
                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label" for="remover">{{ _t('') }}</label>
                                                                                                                                                                                                                                                                                                                                                                            <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                </div> -->
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
                                <h5 class="card-title mb-0">{{ _t('Packages') }}</h5>
                            </div>
                            <div class="card-body package-repeater-wrapper">
                                <div class="package-repeater-items">
                                    <!-- <div class="package-repeater-item">
                                                                                                                                                                                                                                                                                                                                                                    <div class="row">
                                                                                                                                                                                                                                                                                                                                                                        <div class="mb-3 col-8">
                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label" for="package-repeater-1-1">{{ 'package' }}</label>
                                                                                                                                                                                                                                                                                                                                                                            <select name="stocks[0][id]" id="package-repeater-1-1" class="package_select2_1 form-select" data-allow-clear="true">
                                                                                                                                                                                                                                                                                                                                                                            </select>
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                        <div class="mb-3 col-1">
                                                                                                                                                                                                                                                                                                                                                                            <label class="form-label" for="remover">{{ _t('') }}</label>
                                                                                                                                                                                                                                                                                                                                                                            <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary packages-repeater-add"><i
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
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="product-repeater-1-1">{{ _t('Type') }}</label>
                                                <select data-id="1" id="payment_type" name="payments[0][type]"
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
                                            <div class="mb-3 col-3 d-none card-div-1">
                                                <label class="form-label"
                                                    for="product-repeater-1-1">{{ _t('Card') }}</label>
                                                <select id="card_1" name="payments[0][card_id]"
                                                    class="card_select2_1 form-select" data-allow-clear="true">
                                                </select>
                                            </div>
                                            <div class="mb-3 col-3">
                                                <label class="form-label"
                                                    for="total-repeater-1-2">{{ _t('Amount') }}</label>
                                                <input type="number" name="payments[0][amount]" id="total-repeater-1-2"
                                                    value="0" step="any" class="form-control total-repeater-1"
                                                    placeholder="{{ _t('Amount') }}" required />
                                            </div>
                                            <div class="mb-3 col-2">
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
                                <h6 class="text-start">
                                    {{ _t('Remaining') }}:<span id="left-total"class="text">0.00</span>
                                    <input type="hidden" id="left" name="left" value="0">
                                </h6>
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
