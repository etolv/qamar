@extends('layouts/layoutMaster')

@section('title', 'Edit Supplier')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/quill/typography.scss',
'resources/assets/vendor/libs/quill/katex.scss',
'resources/assets/vendor/libs/quill/editor.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/dropzone/dropzone.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/leaflet/leaflet.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/quill/katex.js',
'resources/assets/vendor/libs/quill/quill.js',
'resources/assets/vendor/libs/dropzone/dropzone.js',
'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/leaflet/leaflet.js'
])
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@section('page-script')
<script>
    $('.role_select2').select2({
        placeholder: '{{_t("Select City")}}'
    });
    $(document).ready(function() {
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
                <h5 class="mb-0">{{_t('Update')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('supplier.update', $supplier->id)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{$supplier->id}}" name="id">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                                <input type="text" name="name" value="{{$supplier->name}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name')}}" aria-label="{{_t('Name')}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error('name')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Email')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="text" name="email" value="{{$supplier->email}}" id="basic-icon-default-email" class="form-control" placeholder="{{_t('Email')}}" aria-label="{{_t('Email')}}" aria-describedby="basic-icon-default-email2" />
                            </div>
                            @error('email')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Phone')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                <input type="tel" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="phone" value="{{$supplier->phone}}" id="basic-icon-default-phone" class="form-control" placeholder="{{_t('Phone')}}" aria-label="{{_t('Phone')}}" aria-describedby="basic-icon-default-email2" required />
                            </div>
                            @error('phone')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('City')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="city_id" name="city_id" class="role_select2 form-select" data-allow-clear="true" required>
                                        <option value="">Select</option>
                                        @foreach($cities as $city)
                                        <option value="{{$city->id}}" {{$supplier->city_id == $city->id ? 'selected' : ''}}>{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('city_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Password')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <textarea name="address" id="basic-icon-default-email" class="form-control" aria-label="john.doe" aria-describedby="basic-icon-default-email2">{{$supplier->address}}</textarea>
                            </div>
                            @error('address')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Image')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-image"></i></span>
                                <input type="file" name="image" value="{{old('image')}}" value="{{old('image')}}" id="basic-icon-default-email" accept="image/*" class="form-control" aria-describedby="basic-icon-default-email2" />
                            </div>
                            @if($supplier->getFirstMedia('profile')?->getUrl())
                            <div class="col-lg-6 col-md-6 mb-sm-0">
                                <img src="{{ $supplier->getFirstMedia('profile')?->getUrl() }}" id="profile-img" alt="" class="rounded-circle" style="width: 30px; height: 30px;" />
                            </div>
                            @endif
                            @error('image')
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