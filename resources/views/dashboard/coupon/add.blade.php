@extends('layouts/layoutMaster')

@section('title', 'Add Coupon')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
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
            $('.service_select2').select2({
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
                            department: 1, // Salon department ID
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
            $('.all_services').change(function() {
                if ($(this).is(':checked')) {
                    $('.service_select2').val(null).trigger('change');
                    $('.service_select2').prop('disabled', true);
                } else {
                    $('.service_select2').prop('disabled', false);
                }
            });
            $('.all_products').change(function() {
                if ($(this).is(':checked')) {
                    $('.product_select2').val(null).trigger('change');
                    $('.product_select2').prop('disabled', true);
                } else {
                    $('.product_select2').prop('disabled', false);
                }
            });
            // Initial check on page load
            checkSectionValue();

            // Bind change event to the section_id dropdown
            $('#section_id').change(function() {
                // Check the selected value when the dropdown changes
                checkSectionValue();
            });

            // Function to check the selected value of section_id and show/hide registrationFilterDiv
            function checkSectionValue() {
                var selectedSection = $('#section_id').val();

                // Check if the selected section_id is equal to 2
                if (selectedSection == 2) {
                    // Show the registrationFilterDiv if section_id is 2
                    $('#registrationFilterDiv').show();
                } else {
                    // Hide the registrationFilterDiv for other values
                    $('#registrationFilterDiv').hide();
                }
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
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('coupon.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="fa-solid fa-t"></i></span>
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
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Code') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" name="code" value="{{ old('code') }}"
                                        id="basic-icon-default-code" class="form-control" placeholder="{{ _t('code') }}"
                                        aria-label="{{ _t('code') }}" aria-describedby="basic-icon-default-code2" />
                                </div>
                                @error('code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('All Services') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input all_services" name="all_services"
                                            id="all_services" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('all_services')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 services-div">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Services') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="services" name="services[]" class="service_select2 form-select"
                                            multiple>
                                        </select>
                                    </div>
                                </div>
                                @error('services')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('All Products') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <label class="switch switch-primary me-0">
                                        <input type="checkbox" class="switch-input all_products" name="all_products"
                                            id="all_products" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label"></span>
                                    </label>
                                </div>
                                @error('all_products')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 products-div">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Products') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="products" name="products[]" class="product_select2 form-select"
                                            multiple>
                                        </select>
                                    </div>
                                </div>
                                @error('products')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Discount Percentage') }} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-percent"></i></span>
                                    <input type="number" step="0.1" name="discount" value="{{ old('discount') }}"
                                        id="basic-icon-default-discount" class="form-control"
                                        placeholder="{{ _t('discount') }}" required />
                                </div>
                                @error('discount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('From') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                    <input type="date" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                        name="from_date" value="{{ old('from_date') }}" id="basic-icon-default-discount"
                                        class="form-control" placeholder="{{ _t('From Date') }}" required />
                                </div>
                                @error('from_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('To') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                    <input type="date" min="{{ Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                        name="to_date" value="{{ old('to_date') }}" id="basic-icon-default-discount"
                                        class="form-control" placeholder="{{ _t('To Date') }}" required />
                                </div>
                                @error('to_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row justify-content-end">
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
