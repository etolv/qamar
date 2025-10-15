@extends('layouts/layoutMaster')

@section('title', _t('Translations'))

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />

<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
{{--<link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
--}}
@include('dashboard.translation.style')


<style>
    span.select2.select2-container {
        max-width: 100%;
    }

    span.select2.select2-container:before {
        position: absolute;
        top: 40%;
        right: 10px;
        width: 10px;
        opacity: 0.7;
        height: 10px;
        transform: translate(0px, -50%);
        content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAMAAAC67D+PAAAAAXNSR0IB2cksfwAAAAlwSFlzAAAbrwAAG68BXhqRHAAAADZQTFRFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAt3ZocgAAABJ0Uk5TAAh2uQsMkP+zEbyUBwSWxYqVSsfhlwAAAENJREFUeJxVjEsOgCAQQysoHYTKcP/LKiEYfZs26Qf4sL0S4j7cEQMSLQP5ZAIKrVZjeSJdbI2uUVMnu+ZY7lp/Ev7cUOIBou+37tgAAAAASUVORK5CYII=);
    }
</style>
@endsection

@section('content')



<section id="basic-datatable">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{route('translation.index')}}" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-1">
                                <label class="form-label" for="lang_name">{{_t('Choose language')}}</label>
                                <select class="select2 form-select search" name="lang_name">
                                    <option value="{{NULL}}" selected>{{_t('All')}}</option>
                                    @foreach($locales as $key => $locale)
                                    <option {{ isset( $_GET['lang_name']) && $_GET['lang_name'] == $key ? 'selected' : ''}} value="{{$key}}">{{$locale}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-4">
                            <div class="mb-1">
                                <label class="form-label" for="lang_file_name">{{_t('Translation key')}}</label>
                                <select class="select2 form-select" name="lang_file_name">
                                    <option value="{{NULL}}" selected>{{_t('All')}}</option>
                                    @foreach($translationTypes as $key => $locale)
                                    <option value="{{$locale}}" @if(request()->lang_file_name == $locale) selected @endif>{{$locale}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">{{_t('Search')}}</label>
                                <input class="form-control" name="key" type="text" value="{{$_GET['key'] ?? ''}}">
                            </div>
                        </div>


                    </div>


                    <div class="row">


                    </div>

                    <div class="text-left">
                        <button type="submit" class="btn btn-primary mt-2">{{_t('Search')}}</button>
                    </div>
                </form>
                <!-- <button class="btn btn-info mt-2">{{_t('Auto Translate')}}</button> -->
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-datatable overflow-auto">
                    <table class="category compact table">
                        <thead>
                            <tr>
                                <th>{{_t('Action')}}</th>
                                <th>{{_t('ID')}}</th>
                                <th>{{_t('Key')}}</th>
                                <th>{{_t('Title')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($translations as $translation)
                            <tr>
                                <td>
                                    @can('update_translation')
                                    <div class="d-inline-flex">
                                        <a href="{{route('translation.edit',$translation->id)}}" class="pe-1 item-edit">
                                            <i data-feather="trash" class="me-50 ti ti-edit"></i>
                                        </a>

                                    </div>
                                    @endcan
                                    @can('delete_translation')
                                    <div class="d-inline-flex">
                                        <form method="post" id="myForm{{$translation->id}}" action="{{route('translation.destroy',$translation->id)}}">
                                            @csrf
                                            <a href="#" onclick="document.getElementById('myForm{{$translation->id}}').submit()">
                                                <i data-feather="trash" class="me-50 ti ti-trash"></i>
                                            </a>
                                        </form>
                                    </div>
                                    @endcan

                                </td>
                                <td>{{$translation->id}}</td>
                                <td>
                                    <strong> {{ strtok($translation->key, '.')}}</strong>

                                </td>
                                <td>{{_t($translation->value)}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2" style="text-align: center;">
                    {!! $translations->appends(request()->query())->links("pagination::bootstrap-4") !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('vendor-script')
{{-- vendor files --}}
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

<script src="{{ asset('vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('vendors/js/extensions/polyfill.min.js') }}"></script>
<script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
@endsection

@section('page-script')
{{-- Page js files --}}
{{--<script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>--}}


@endsection