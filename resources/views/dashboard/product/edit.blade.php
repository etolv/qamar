@extends('layouts/layoutMaster')

@section('title', 'New product')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
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
            $('.brand_select2').select2({
                placeholder: '{{ _t('Select Brand') }}',
                ajax: {
                    url: route('brand.search'),
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
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            // $('.branch_select2').select2({
            //     placeholder: '{{ _t('Select Branch') }}',
            //     ajax: {
            //         url: route('branch.search'),
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
                    <form method="post" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="department" value="{{ $product->department }}" />
                        <input type="hidden" name="id" value="{{ $product->id }}" />
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="text" name="name" value="{{ old('name') ?? $product->name }}"
                                        required class="form-control" id="basic-icon-default-fullname"
                                        placeholder="{{ _t('Name') }}" aria-label="{{ _t('Name') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Barcode') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="text" name="sku" value="{{ old('sku') ?? $product->sku }}" required
                                        class="form-control" id="basic-icon-default-fullsku"
                                        placeholder="{{ _t('Barcode') }}" aria-label="{{ _t('Barcode') }}"
                                        aria-describedby="basic-icon-default-fullsku2" required />
                                </div>
                                @error('sku')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Min Quantity') }} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="number" name="min_quantity"
                                        value="{{ old('min_quantity') ?? $product->min_quantity }}" required
                                        class="form-control" id="basic-icon-default-fullmin_quantity"
                                        placeholder="{{ _t('min_quantity') }}" aria-label="{{ _t('min_quantity') }}"
                                        aria-describedby="basic-icon-default-fullmin_quantity2" required />
                                </div>
                                @error('min_quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Refundable') }}
                                *</label>
                            <div class="col-sm-10">
                                <label class="switch switch-primary me-0">
                                    <input type="checkbox" class="switch-input" id="refundable" name="refundable"
                                        data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false"
                                        aria-controls="collapseExample" {{ $product->refundable ? 'checked' : '' }} />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label"></span>
                                </label>
                            </div>
                            @error('refundable')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
                                @if ($product->getFirstMedia('image'))
                                    <img src="{{ $product->getFirstMediaUrl('image') }}" id="profile-img" alt=""
                                        class="rounded-circle" style="width: 30px; height: 30px;" />
                                @endif
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Consumption Type') }} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="consumption_type" name="consumption_type"
                                            class="consumption_select2 form-select" data-allow-clear="true" required>
                                            @foreach (App\Enums\ConsumptionTypeEnum::cases() as $consumption_type)
                                                <option value="{{ $consumption_type->value }}"
                                                    {{ $product->consumption_type == $consumption_type ? 'selected' : '' }}>
                                                    {{ $consumption_type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('consumption_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Brand') }}
                            </label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="brand_id" name="brand_id" class="brand_select2 form-select"
                                            data-allow-clear="true">
                                            @if ($product->brand)
                                                <option value="{{ $product->brand_id }}">{{ $product->brand?->name }}
                                            @endif
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @error('brand_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Category') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="category_id" name="category_id" class="category_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="{{ $product->category_id }}">{{ $product->category->name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="row mb-3">
                                                    <label class="col-sm-2 form-label" for="basic-icon-default-phone">Phone No</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group input-group-merge">
                                                            <span id="basic-icon-default-phone2" class="input-group-text"><i class="ti ti-phone"></i></span>
                                                            <input type="text" name="" id="basic-icon-default-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
                                                        </div>
                                                    </div>
                                                </div> -->
                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ _t('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
