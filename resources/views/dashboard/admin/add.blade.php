@extends('layouts/layoutMaster')

@section('title', ' Horizontal Layouts - Forms')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection
@section('page-style')
<style>
    .iti {
        width: 100%;
        display: block;
    }

    .iti--allow-dropdown .form-control {
        padding-left: 3.5rem;
        /* Increased padding to allow space for the flag */
        padding-right: 1rem;
        /* Maintain default Bootstrap padding */
        height: calc(2.25rem + 2px);
        /* Match Bootstrap's form-control height */
        font-size: 1rem;
        line-height: 1.5;
        background-clip: padding-box;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
</style>
@ensection
@section('page-script')
<script>
    $(document).ready(function() {
        $('.role_select2').select2({
            placeholder: '{{_t("Select Role")}}'
        });
        $('.country_code_select2').select2({});
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            // Customize the plugin with any options here
            initialCountry: "auto",
            geoIpLookup: function(success, failure) {
                $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "us";
                    success(countryCode);
                });
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });
        input.addEventListener("countrychange", function() {
            console.log("countrychange");
            console.log(iti.getSelectedCountryData());
            var countryCode = iti.getSelectedCountryData().iso2;
            var dialCode = iti.getSelectedCountryData().dialCode;
            document.querySelector("#country-code").value = countryCode;
            document.querySelector("#dial-code").value = countryCode;
            console.log("Selected country code:", countryCode);
        });
        document.querySelector("#country-code").value = iti.getSelectedCountryData().iso2;
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
                <form method="post" action="{{route('admin.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                                <input type="text" name="name" value="{{old('name')}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name')}}" aria-label="{{_t('Name')}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error('name')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Email')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="text" name="email" value="{{old('email')}}" id="basic-icon-default-email" class="form-control" placeholder="{{_t('Email')}}" aria-label="{{_t('Email')}}" aria-describedby="basic-icon-default-email2" required />
                            </div>
                            @error('email')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Phone')}} *</label>
                        <div class="col-sm-10">
                            @include('components.phone_with_code', ['user' => null])
                            @error('phone')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Password')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input type="password" name="password" value="{{old('password') ?? 'password'}}" value="{{old('password') ?? 'password'}}" id="basic-icon-default-email" class="form-control" placeholder="******" aria-label="john.doe" aria-describedby="basic-icon-default-email2" readonly />
                            </div>
                            @error('password')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Profile image')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-image"></i></span>
                                <input type="file" name="image" value="{{old('image')}}" value="{{old('image')}}" id="basic-icon-default-email" accept="image/*" class="form-control" aria-describedby="basic-icon-default-email2" />
                            </div>
                            @error('image')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Role')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="role_id" name="role_id" class="role_select2 form-select" data-allow-clear="true" required>
                                        <option value="">Select</option>
                                        @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('role_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label class="col-sm-2 form-label" for="basic-icon-default-phone">Phone No</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-phone2" class="input-group-text"><i class="ti ti-phone"></i></span>
                                <input type="text" name="" id="basic-icon-default-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
                            </div>
                        </div>
                    </div> -->
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