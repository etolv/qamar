@extends('layouts/layoutMaster')

@section('title', 'Add City')

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
        $('select2').select2({});
    });
</script>
@endsection

@section('content')

<div class="row">
    <!-- Basic with Icons -->
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{_t('Create')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('city.store')}}" enctype="multipart/form-data">
                    @csrf
                    @foreach($availableLocales as $index => $locale)
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name') . " " . _t($locale)}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-home"></i></span>
                                <input type="text" name="{{$index.'[name]'}}" value="{{old($index)['name'] ?? ''}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name') . ' '  . _t($locale)}}" aria-label="{{_t('Name') . ' ' . _t($locale)}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error($index.'[name]')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('State') . " " . _t($locale)}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                            <select class="select2 form-select" name="state_id">
                                @foreach($states as $key => $state)
                                <option value="{{$state->id}}">{{$state->name}}</option>
                                @endforeach
                            </select>
                            </div>
                            @error('state_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">{{_t('Create')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection