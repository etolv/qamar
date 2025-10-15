@extends('layouts/layoutMaster')

@section('title', 'New service')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
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
            let categoryId = 0;
            $('.category_select2').change(function() {
                categoryId = $(this).val();
            });
            $('.consumption_select2').select2({});
            $('.category_select2').select2({
                placeholder: '{{ _t('Select Category') }}',
                ajax: {
                    url: route('category.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            category_id: null
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
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
            $('.sub_category_select2').select2({
                placeholder: '{{ _t('Select Sub Categories') }}',
                ajax: {
                    url: route('category.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            category_id: categoryId
                        };
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
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
            $('.service_select2').select2({
                placeholder: '{{ _t('Select Section') }}',
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
                            department: department, // Salon department ID
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
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
                                // min_quantity: '1', // your custom parameter
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + " - " + item.sku,
                                        // price: item.price
                                    };
                                })
                            };
                        },
                        cache: false
                    }
                });
            }
            initializeProductSelect2('.product_select2_1');
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
                                <label class="form-label" for="product-repeater-${currentCount+1}-1">{{ 'Product' }}</label>
                                <select id="product-repeater-${currentCount+1}-2" name="products[${currentCount}][id]" class="product_select2_${currentCount+1} form-select" data-allow-clear="true"></select>
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="required-repeater-${currentCount+1}-2">{{ _t('Required') }}</label>
                                <div class="col-sm-10">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input" id="required-repeater-${currentCount+1}-2" name="products[${currentCount}][required]" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" />
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
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-items').append(newRepeaterItem);
                initializeProductSelect2(`.product_select2_${currentCount+1}`);
            });
            $('.repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                // var repeaterItems = $(this).closest('.repeater-items');
                var item = $(this).closest('.repeater-item');
                // if (repeaterItems.children('.repeater-item').length > 1) {
                item.remove();
                updateBillPreview();
                // } else {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Oops..',
                //         text: "At least one product is required",
                //     });
                // }
            });
            // checkSectionValue();

            // Bind change event to the section_id dropdown
            // $('#section_id').change(function() {
            //     // Check the selected value when the dropdown changes
            //     checkSectionValue();
            // });

            // // Function to check the selected value of section_id and show/hide registrationFilterDiv
            // function checkSectionValue() {
            //     var selectedSection = $('#section_id').val();

            //     // Check if the selected section_id is equal to 2
            //     if (selectedSection == 2) {
            //         // Show the registrationFilterDiv if section_id is 2
            //         $('#registrationFilterDiv').show();
            //     } else {
            //         // Hide the registrationFilterDiv for other values
            //         $('#registrationFilterDiv').hide();
            //     }
            // }
            $('#has_terms').change(function() {
                if ($(this).is(':checked')) {
                    $('.terms-div').removeClass('d-none');
                    $('#terms').prop('required', true);
                } else {
                    $('.terms-div').addClass('d-none');
                    $('#terms').prop('required', false);
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
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('service.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="department" value="{{ request()->department }}" />
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Category') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="category_id" name="category_id" class="category_select2 form-select"
                                            data-allow-clear="true" required>
                                        </select>
                                    </div>
                                </div>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Sub Categories') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="sub_categories" name="sub_categories[]"
                                            class="sub_category_select2 form-select" data-allow-clear="true" multiple>
                                        </select>
                                    </div>
                                </div>
                                @error('sub_categories')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{--
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Section')}}</label>
                    <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti ti-settings"></i></span>
                            <div class="col-sm-10">
                                <select id="service_id" name="service_id" class="service_select2 form-select" data-allow-clear="true">
                                </select>
                            </div>
                        </div>
                        @error('service_id')
                        <span class="text-danger">{{$message }}</span>
                        @enderror
                    </div>
            </div>
            --}}
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="form-control" id="basic-icon-default-fullname"
                                        placeholder="{{ _t('Name') }}" aria-label="{{ _t('Name') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="row mb-3">
                                                                                                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('SKU') }} *</label>
                                                                                                        <div class="col-sm-10">
                                                                                                            <div class="input-group input-group-merge">
                                                                                                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-clipboard"></i></span>
                                                                                                                <input type="text" name="sku" value="{{ old('sku') }}" required class="form-control" id="basic-icon-default-fullsku" placeholder="{{ _t('sku') }}" aria-label="{{ _t('sku') }}" aria-describedby="basic-icon-default-fullsku2" />
                                                                                                            </div>
                                                                                                            @error('sku')
        <span class="text-danger">{{ $message }}</span>
    @enderror
                                                                                                        </div>
                                                                                                    </div> -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Price') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="number" name="price" value="{{ old('price') ?? 0 }}" required
                                        class="form-control" id="basic-icon-default-fullprice"
                                        placeholder="{{ _t('price') }}" aria-label="{{ _t('price') }}"
                                        aria-describedby="basic-icon-default-fullprice" required />
                                </div>
                                @error('price')
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
                                    <textarea name="description" class="form-control" id="basic-icon-default-fullmin_quantity"
                                        placeholder="{{ _t('Description') }}" aria-describedby="basic-icon-default-fullmin_quantity2">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
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
                        @if (request()->department == 1)
                            <div class="row mb-3 profit-div">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Has Terms') }} *</label>
                                <div class="col-sm-10">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input" id="has_terms" name="has_terms"
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('has_terms')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row mb-3 terms-div d-none">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Terms') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <input type="file" name="terms" value="{{ old('terms') }}" id="terms"
                                        accept="*/*" class="form-control" />
                                    @error('terms')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ _t('Related Products') }}</h5>
                                </div>
                                <div class="card-body repeater-wrapper">
                                    <div class="repeater-items">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <button class="btn btn-primary repeater-add">{{ _t('Add product') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
