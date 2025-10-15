@extends('layouts/layoutMaster')

@section('title', 'Edit Category')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
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
                                console.log(item);
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
                    <form method="post" action="{{ route('category.update', $category->id) }}" enctype="multipart/form-data">
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
                                            value="{{ $category->translate($index)->name }}" required class="form-control"
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
                        <div class="row mb-3 d-none">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Parent') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-color-swatch"></i></span>
                                    <div class="col-sm-10">
                                        <select id="category_id" name="category_id" class="category_select2 form-select"
                                            data-allow-clear="true">
                                            @if ($category->category_id)
                                                <option value="{{ $category->category_id }}">{{ $category->parent->name }}
                                                </option>
                                            @endif
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
                                for="basic-icon-default-email">{{ _t('Image') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-image"></i></span>
                                    <input type="file" name="image" value="{{ old('image') }}"
                                        id="basic-icon-default-email" accept="image/*" class="form-control"
                                        aria-describedby="basic-icon-default-email2" />
                                </div>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @if ($category->getFirstMedia('image')?->getUrl())
                                    <div class="col-lg-6 col-md-6 mb-sm-0">
                                        <img src="{{ $category->getFirstMedia('image')?->getUrl() }}" id="profile-img"
                                            alt="" class="rounded-circle" style="width: 30px; height: 30px;" />
                                    </div>
                                @endif
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
