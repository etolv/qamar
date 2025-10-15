@extends('layouts/layoutMaster')

@section('title', 'Edit Bill Type')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection


@section('page-script')
<script>
    $(document).ready(function() {
        $('.static-input').change(function() {
            let is_checked = $(this).is(':checked');
            if (is_checked) {
                $('.price-div').removeClass('d-none');
                $('.price-input').prop('required', true);
            } else {
                $('.price-div').addClass('d-none');
                $('.price-input').prop('required', false);
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
                <h5 class="mb-0">{{_t('Update')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('bill-type.update', $type->id)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-home"></i></span>
                                <input type="text" name="name" value="{{$type->name}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name')}}" required />
                            </div>
                            @error('name')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Description')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-comment"></i></span>
                                <input type="text" name="description" value="{{old('description') ?? ''}}" class="form-control" id="basic-icon-default-fulldescription" placeholder="{{_t('Description')}}" />
                            </div>
                            @error('description')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Static')}} *</label>
                        <div class="col-sm-10">
                            <label class="switch switch-primary">
                                <input type="checkbox" name="static" title="Activate" class="switch-input static-input" {{$type->static ? 'checked' : ''}} />
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">
                                        <i class="ti ti-check"></i>
                                    </span>
                                    <span class="switch-off">
                                        <i class="ti ti-x"></i>
                                    </span>
                                </span>
                            </label>
                            @error('static')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 price-div {{$type->static ? '' : 'd-none'}}">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Price')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-home"></i></span>
                                <input type="number" step="any" name="price" value="{{$type->price}}" required class="form-control" id="basic-icon-default-fullprice" placeholder="{{_t('Price')}}" />
                            </div>
                            @error('price')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">{{_t('Update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection