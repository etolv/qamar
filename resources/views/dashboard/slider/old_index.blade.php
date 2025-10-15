@extends('layouts/layoutMaster')

@section('title', 'Setting List')

@section('vendor-style')

@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />
@endsection

@section('vendor-script')

@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/apex-charts/apexcharts.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        new FroalaEditor('textarea#froala-editor-privacy-policy');
        new FroalaEditor('textarea#froala-editor-terms');
        new FroalaEditor('textarea#froala-editor-about-us');
        setTimeout(function() {
            $('[id="fr-logo"]').hide();
            $('[href="https://www.froala.com/wysiwyg-editor?k=u"]').hide();
        }, 1500);
    });
</script>
@endsection
@section('content')
<!-- <h6 class="pb-1 mb-4 text-muted">{{_t('Sliders')}}</h6> -->
<div class="card mb-2">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-3">{{_t('Settings')}}</h5>
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
            <div class="col-md-4">
                @can('create_setting')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSettingModal">
                    {{_t('New Setting')}}
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
    @php
    $privacy_policy = $settings->where('key', 'privacy_policy')->first();
    $terms_and_conditions = $settings->where('key', 'terms_and_conditions')->first();
    $about_us = $settings->where('key', 'about_us')->first();
    $instagram = $settings->where('key', 'instagram')->first();
    $snapchat = $settings->where('key', 'snapchat')->first();
    $tiktok = $settings->where('key', 'tiktok')->first();
    $email = $settings->where('key', 'email')->first();
    $youtube = $settings->where('key', 'youtube')->first();
    $phone = $settings->where('key', 'phone')->first();
    $address = $settings->where('key', 'address')->first();
    $tax = $settings->where('key', 'tax')->first();
    $profit_percentage = $settings->where('key', 'profit_percentage')->first();
    @endphp
    <form method="post" action="{{route('setting.update', $instagram->id)}}" id="{{$instagram->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Instagram')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$instagram->value}}" />
                    </p>
                    @if($instagram->description)
                    <p class="card-text">
                        {{$instagram->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $snapchat->id)}}" id="{{$snapchat->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Snap Chat')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$snapchat->value}}" />
                    </p>
                    @if($snapchat->description)
                    <p class="card-text">
                        {{$snapchat->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $tiktok->id)}}" id="{{$tiktok->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Tik Tok')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$tiktok->value}}" />
                    </p>
                    @if($tiktok->description)
                    <p class="card-text">
                        {{$tiktok->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $youtube->id)}}" id="{{$youtube->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Youtube')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$youtube->value}}" />
                    </p>
                    @if($youtube->description)
                    <p class="card-text">
                        {{$youtube->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $email->id)}}" id="{{$email->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Email')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$email->value}}" />
                    </p>
                    @if($email->description)
                    <p class="card-text">
                        {{$email->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $phone->id)}}" id="{{$phone->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Phone')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$phone->value}}" />
                    </p>
                    @if($phone->description)
                    <p class="card-text">
                        {{$phone->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $profit_percentage->id)}}" id="{{$profit_percentage->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Profit Percentage')}}</h5>
                    <p class="card-text">
                        <input class="form-control" type="text" name="value" value="{{$profit_percentage->value}}" />
                    </p>
                    @if($profit_percentage->description)
                    <p class="card-text">
                        {{$profit_percentage->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $address->id)}}" id="{{$address->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Address')}}</h5>
                    <p class="card-text">
                        <textarea class="form-control" row="3" name="value">{{$address->value}}</textarea>
                    </p>
                    @if($address->description)
                    <p class="card-text">
                        {{$address->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $privacy_policy->id)}}" id="{{$privacy_policy->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Privacy policy')}}</h5>
                    <p class="card-text">
                        <textarea class="form-control" row="3" id="froala-editor-privacy-policy" name="value">{{$privacy_policy->value}}</textarea>
                    </p>
                    @if($privacy_policy->description)
                    <p class="card-text">
                        {{$privacy_policy->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $terms_and_conditions->id)}}" id="{{$terms_and_conditions->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('Terms and conditions')}}</h5>
                    <p class="card-text">
                        <textarea class="form-control" row="3" id="froala-editor-terms" name="value">{{$terms_and_conditions->value}}</textarea>
                    </p>
                    @if($terms_and_conditions->description)
                    <p class="card-text">
                        {{$terms_and_conditions->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="{{route('setting.update', $about_us->id)}}" id="{{$about_us->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{_t('About us')}}</h5>
                    <p class="card-text">
                        <textarea class="form-control" row="3" id="froala-editor-about-us" name="value">{{$about_us->value}}</textarea>
                    </p>
                    @if($about_us->description)
                    <p class="card-text">
                        {{$about_us->description}}
                    </p>
                    @endif
                    <p>
                        @can('update_setting')
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                        @endcan
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal-onboarding modal fade animate__animated" id="newSettingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body">{{_t('New setting')}}</h4>
                    <form method="POST" action="{{route('setting.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname1">{{_t('Key')}} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname1" class="input-group-text"><i class="ti ti-comment"></i></span>
                                    <input type="text" name="key" value="{{old('key')}}" required class="form-control" id="key" placeholder="{{_t('Key')}}" required />
                                </div>
                                @error('key')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname2">{{_t('Value')}} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-comment"></i></span>
                                    <input type="text" name="value" value="{{old('value')}}" required class="form-control" id="value" placeholder="{{_t('Value')}}" required />
                                </div>
                                @error('value')
                                <span class="text-danger">{{$message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">{{ _t('Store') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer border-0">
            </div>
        </div>
    </div>
</div>
<!-- end modal -->
@endsection