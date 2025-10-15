@extends('layouts/layoutMaster')

@section('title', _t('dashboard.Translations'))

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<!-- vendor css files -->
<link rel="stylesheet" href="{{asset('vendors/css/extensions/toastr.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />

@endsection

@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
<link rel="stylesheet" href="{{asset('css/base/plugins/extensions/ext-component-toastr.css')}}">
<link rel="stylesheet" href="{{asset('build/css/intlTelInput.css')}}">
<link rel="stylesheet" href="{{asset('build/css/demo.css')}}">

@endsection


@section('content')
<!-- Validation -->

<section class="bs-validation">
    <div class="row">
        <!-- Bootstrap Validation -->
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <form action="{{route('translation.update',$translations[0]->key)}}" method="post" class="needs-validation" novalidate>--}}
                    <form action="{{route('translation.update',$translations[0]->key)}}" method="post">
                        @csrf
                        @foreach(config('translation')['locales'] as $key => $locale)
                        @php
                        $translation = $translations->where('language_code',$key)->first();
                        @endphp
                        <div class="mb-2">
                            <label class="form-label">{{_t('dashboard.Title')}} ({{$locale}})</label>
                            <textarea name="{{ $key }}[title]" id="title_{{$key}}" class="form-control" required cols="30" rows="4">{{$translation->value ?? ''}}</textarea>
                        </div>
                        @endforeach
                        @can('update_translation')
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">{{_t('dashboard.Submit')}}</button>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
        <!-- /Bootstrap Validation -->

    </div>
</section>
<!-- /Validation -->
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection
@section('page-script')

@endsection