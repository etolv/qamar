@extends('layouts/layoutMaster')

@section('title', 'Edit Municipal')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection

@section('content')

    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Update') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('municipal.update', $municipal->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @foreach ($availableLocales as $index => $locale)
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-fullname">{{ _t('Name') . ' ' . _t($locale) }} *</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                class="ti ti-home"></i></span>
                                        <input type="text" name="{{ $index . '[name]' }}"
                                            value="{{ $municipal->translate($index)?->name }}" required class="form-control"
                                            id="basic-icon-default-fullname"
                                            placeholder="{{ _t('Name') . ' ' . _t($locale) }}"
                                            aria-label="{{ _t('Name') . ' ' . _t($locale) }}"
                                            aria-describedby="basic-icon-default-fullname2" required />
                                    </div>
                                    @error($index . '[name]')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Delivery Fees') }} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <input type="number" steps="any" name="delivery_fee" id="delivery_fee"
                                        value="{{ old('delivery_fee') ?? $municipal->delivery_fee }}"
                                        placeholder="{{ _t('Delivery Fees') }}" class="form-control" />
                                </div>
                                @error('delivery_fee')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="lang_file_name">{{ _t('City') }} *</label>
                            <div class="col-sm-10">
                                <select class="city_select2 form-select" name="city_id">
                                    <option value="{{ $municipal->city_id }}" selected>{{ $municipal->city->name }}
                                    </option>
                                </select>
                            </div>
                        </div>
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
