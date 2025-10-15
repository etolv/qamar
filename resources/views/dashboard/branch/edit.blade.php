@extends('layouts/layoutMaster')

@section('title', 'Edit branch')

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
        $('.city_select2').select2({
            placeholder: '{{_t("Select City")}}',
            ajax: {
                url: route('city.search'),
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
        // Initial check on page load
        checkSectionValue();

        // Bind change event to the section_id dropdown
        $('#section_id').change(function() {
            // Check the selected value when the dropdown changes
            checkSectionValue();
        });

        // Function to check the selected value of section_id and show/hide registrationFilterDiv
        function checkSectionValue() {
            var selectedSection = $('#section_id').val();

            // Check if the selected section_id is equal to 2
            if (selectedSection == 2) {
                // Show the registrationFilterDiv if section_id is 2
                $('#registrationFilterDiv').show();
            } else {
                // Hide the registrationFilterDiv for other values
                $('#registrationFilterDiv').hide();
            }
        }
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
                <form method="post" action="{{route('branch.update', $branch->id)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                                <input type="text" name="name" value="{{$branch->name}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('name')}}" aria-label="{{_t('name')}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error('name')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Physical')}} *</label>
                        <div class="col-sm-10">
                            <label class="switch switch-primary me-0">
                                <input type="checkbox" class="switch-input" id="is_physical" name="is_physical" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" {{ $branch->is_physical ? 'checked' : '' }} />
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label"></span>
                            </label>
                        </div>
                        @error('is_physical')
                        <span class="text-danger">{{$message }}</span>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('City')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="city_id" name="city_id" class="city_select2 form-select" data-allow-clear="true" required>
                                        <option value="{{$branch->city_id}}" selected>{{$branch->city->name}}</option>
                                    </select>
                                </div>
                            </div>
                            @error('city_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Address')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-home"></i></span>
                                <textarea name="address" id="basic-icon-default-email" class="form-control" aria-label="john.doe" aria-describedby="basic-icon-default-email2" required> {{$branch->address}}</textarea>
                            </div>
                            @error('address')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Street')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-home"></i></span>
                                <textarea name="street" id="basic-icon-default-email" class="form-control" aria-label="john.doe" aria-describedby="basic-icon-default-email2" required> {{$branch->street}}</textarea>
                            </div>
                            @error('street')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Building')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-home"></i></span>
                                <textarea name="building" id="basic-icon-default-email" class="form-control" aria-label="john.doe" aria-describedby="basic-icon-default-email2" required> {{$branch->building}}</textarea>
                            </div>
                            @error('building')
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