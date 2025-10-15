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
        new FroalaEditor('textarea#froala-editor-privacy_policy');
        new FroalaEditor('textarea#froala-editor-terms_and_conditions');
        new FroalaEditor('textarea#froala-editor-about_us');
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
    @foreach($settings as $setting)
    <form method="post" action="{{route('setting.update', $setting->id)}}" id="{{$setting->id}}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{$setting->key}}</h5>
                    <p class="card-text">
                        {{_t("Appear in the app")}}:
                        <label class="switch switch-primary">
                            <input type="checkbox" name="appear_app" title="Activate" class="switch-input" {{$setting->appear_app ? 'checked' : '' }} />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    </p>
                    @if($setting->type->value == 1)
                    <p class="card-text">
                        <textarea class="form-control" row="3" name="value">{{$setting->value}}</textarea>
                    </p>
                    @elseif($setting->type->value == 2)
                    <p class="card-text">
                        <input class="form-control" type="number" name="value" value="{{$setting->value}}" />
                    </p>
                    @elseif($setting->type->value == 3)
                    <p class="card-text">
                        <label class="switch switch-primary">
                            <input type="checkbox" name="value" title="Activate" class="switch-input" {{$setting->value ? 'checked' : '' }} />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    </p>
                    @elseif($setting->type->value == 4)
                    <p class="card-text">
                        <img class="card-img-top" src="{{$setting->getFirstMediaUrl('image')}}" alt="Card image" />
                        <input type="file" name="image" class="form-control" />
                    </p>
                    @elseif($setting->type->value == 5)
                    <p class="card-text">
                        <textarea class="form-control" row="3" name="value" id="froala-editor-{{$setting->key}}">{{$setting->value}}</textarea>
                    </p>
                    @endif
                    <p>
                        <button type="submit" class="btn btn-outline-primary">{{_t('Update')}}</button>
                    </p>
                </div>
            </div>
        </div>
    </form>
    @endforeach
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