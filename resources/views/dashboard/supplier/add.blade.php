@extends('layouts/layoutMaster')

@section('title', 'Add Supplier')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.country_code_select2').select2({});

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            let supplier_id = getUrlParameter('supplier_id');
            if (!supplier_id) {
                $('.main-supplier-div').addClass('d-none');
            } else {
                $('.company-div').addClass('d-none');
            }
            $('.role_select2').select2({
                placeholder: '{{ _t('Select City') }}'
            });
            $('.type_select2').select2({
                placeholder: '{{ _t('Select Type') }}'
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
            $('.type_select2').change(function() {
                let type = $(this).val();
                if (type == 2) {
                    $('.tax-div').addClass('d-none');
                    // $('#tax_number').prop('required', false);
                    $('.link-div').removeClass('d-none');
                } else {
                    $('.tax-div').removeClass('d-none');
                    // $('#tax_number').prop('required', true);
                    $('.link-div').addClass('d-none');
                }
            });
            $('#add-card').click(function(e) {
                e.preventDefault();
                var currentCount = $('.card-repeater-item').length;

                const attributeRow = `
                    <div class="card-repeater-item">
                        <div class="row attribute-row mb-3">
                            <div class="col-md-3">
                                <label class="form-label" for="attributes">{{ _t('Name') }}</label>
                                <input type="text" class="form-control" id="cards_name${currentCount}"
                                    placeholder="{{ _t('Name') }}" name="cards[${currentCount}][name]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="attributes">{{ _t('Number') }}</label>
                                <input type="text" class="form-control" id="cards_number${currentCount}"
                                    placeholder="{{ _t('Number') }}" name="cards[${currentCount}][number]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="attributes">{{ _t('IBAN') }}</label>
                                <input type="text" class="form-control" id="cards_iban${currentCount}"
                                    placeholder="{{ _t('IBAN') }}" name="cards[${currentCount}][iban]" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger card-remove-attribute"
                                    title="{{ _t('Remove') }}"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                        <hr>
                    </div>
                                `;
                $('.card-repeater-items').append(attributeRow);
            });

            // Remove attribute row
            $(document).on('click', '.card-remove-attribute', function() {
                $(this).closest('.card-repeater-item').remove();
                // updateExcludedAttributeIds();
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
                    <form method="post" action="{{ route('supplier.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="main-supplier-div">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Name') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="ti ti-user"></i></span>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="form-control" id="basic-icon-default-fullname"
                                            placeholder="{{ _t('Name') }}" />
                                    </div>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Phone') }}</label>
                                <div class="col-sm-10">
                                    @include('components.phone_with_code', ['user' => null])
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Email') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input type="text" name="email" value="{{ old('email') }}"
                                            id="basic-icon-default-email" class="form-control"
                                            placeholder="{{ _t('Email') }}" />
                                    </div>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3 d-none">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Main Supplier') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                        <div class="col-sm-10">
                                            <select id="supplier_id" name="supplier_id" class="supplier_select2 form-select"
                                                data-allow-clear="false">
                                                @if ($supplier)
                                                    <option value="{{ $supplier->id }}" selected>{{ $supplier->name }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @error('supplier_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('City') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-globe"></i></span>
                                    <div class="col-sm-10">
                                        <select id="city_id" name="city_id" class="role_select2 form-select"
                                            data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('city_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-globe"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type" name="type" class="type_select2 form-select" required>
                                            @foreach ($types as $type)
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
                        <div class="company-div">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Address') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-home"></i></span>
                                        <textarea name="address" value="{{ old('address') }}" id="basic-icon-default-email" class="form-control"
                                            aria-label="john.doe" aria-describedby="basic-icon-default-email2"> {{ old('address') }}</textarea>
                                    </div>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3 tax-div">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Tax Number') }}</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-money-check"></i></span>
                                        <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                                            class="form-control" id="tax_number" placeholder="{{ _t('Tax number') }}" />
                                    </div>
                                    @error('tax_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Company') }}
                                </label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="fa-solid fa-building"></i></span>
                                        <input type="text" name="company" value="{{ old('company') }}"
                                            class="form-control" id="basic-icon-default-fullcompany"
                                            placeholder="{{ _t('company') }}" aria-label="{{ _t('company') }}"
                                            aria-describedby="basic-icon-default-fullcompany2" />
                                    </div>
                                    @error('company')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 link-div d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Link') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="fa-solid fa-link"></i></span>
                                    <input type="url" name="link" value="{{ old('link') }}"
                                        class="form-control" id="link" placeholder="{{ _t('Link') }}" />
                                </div>
                                @error('link')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Bank Number') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="fa-solid fa-credit-card"></i></span>
                                    <input type="text" name="bank_number" value="{{ old('bank number') }}"
                                        class="form-control" id="basic-icon-default-fullbank_number"
                                        placeholder="{{ _t('bank_number') }}" aria-label="{{ _t('bank_number') }}"
                                        aria-describedby="basic-icon-default-fullbank_number2" />
                                </div>
                                @error('bank_number')
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
                        @if (!$supplier)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ _t('Cards') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="card-repeater-items">
                                        {{-- repeater item --}}
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-start">
                                            <button type="button" id="add-card" class="btn btn-primary">
                                                {{ _t('Add Card') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
