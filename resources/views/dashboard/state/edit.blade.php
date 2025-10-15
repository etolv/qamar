@extends('layouts/layoutMaster')

@section('title', 'Edit Category')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection


@section('page-script')
<script>
    $(document).ready(function() {

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
                <form method="post" action="{{route('state.update', $state->id)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @foreach($availableLocales as $index => $locale)
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name') . " " . _t($locale)}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-home"></i></span>
                                <input type="text" name="{{$index.'[name]'}}" value="{{$state->translate($index)?->name}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name') . ' '  . _t($locale)}}" aria-label="{{_t('Name') . ' ' . _t($locale)}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error($index.'[name]')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    @endforeach
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