@extends('layouts/layoutMaster')

@section('title', ' Edit driver')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection
@section('page-style')
    @ensection
@section('page-script')
    <script>
        $(document).ready(function() {
            $('.country_code_select2').select2({});
            $('.city_select2').select2({
                placeholder: '{{ _t('Select City') }}',
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
            $('.branch_select2').select2({
                placeholder: '{{ _t('Select Branch') }}',
                ajax: {
                    url: route('branch.search'),
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
            $('.nationality_select2').select2({
                placeholder: '{{ _t('Select Nationality') }}',
                ajax: {
                    url: route('nationality.search'),
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
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('driver.update', $driver->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-user"></i></span>
                                    <input type="text" name="name" value="{{ old('name') ?? $driver->user->name }}"
                                        required class="form-control" id="basic-icon-default-fullname"
                                        placeholder="{{ _t('Name') }}" aria-label="{{ _t('Name') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Phone') }}
                                *</label>
                            <div class="col-sm-10">
                                @include('components.phone_with_code', ['user' => $driver->user])
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Email') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="email" value="{{ old('email') ?? $driver->user->email }}"
                                        id="basic-icon-default-email" class="form-control"
                                        placeholder="{{ _t('Email') }}" aria-label="{{ _t('Email') }}"
                                        aria-describedby="basic-icon-default-email2" required />
                                </div>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Password') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password" value="{{ old('password') }}"
                                        id="basic-icon-default-email" class="form-control" placeholder="******"
                                        aria-label="john.doe" aria-describedby="basic-icon-default-email2" />
                                </div>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Profile image') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-image"></i></span>
                                    <input type="file" name="image" value="{{ old('image') }}"
                                        value="{{ old('image') }}" id="basic-icon-default-email" accept="image/*"
                                        class="form-control" aria-describedby="basic-icon-default-email2" />
                                </div>
                                @if ($driver->user->getFirstMedia('profile'))
                                    <div class="mt-2">
                                        <img src="{{ $driver->user->getFirstMedia('profile')->getUrl() }}" id="profile-img"
                                            alt="" class="rounded-circle" style="width: 30px; height: 30px;" />
                                    </div>
                                @endif
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('City') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="city_id" name="city_id" class="city_select2 form-select"
                                            data-allow-clear="true" required>
                                            @if ($driver->city)
                                                <option value="{{ $driver->city->id }}" selected>
                                                    {{ $driver->city->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('city_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 {{ $user_branch ? 'd-none' : '' }}">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Branch') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="branch_id" name="branch_id" class="branch_select2 form-select"
                                            data-allow-clear="true" required>
                                            @if ($user_branch)
                                                <option value="{{ $user_branch->id }}" selected>{{ $user_branch->name }}
                                                </option>
                                            @elseif ($driver->branch)
                                                <option value="{{ $driver->branch->id }}" selected>
                                                    {{ $driver->branch->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Nationality') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="nationality_id" name="nationality_id"
                                            class="nationality_select2 form-select" data-allow-clear="true" required>
                                            @if ($driver->nationality)
                                                <option value="{{ $driver->nationality->id }}" selected>
                                                    {{ $driver->nationality->name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('nationality_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Birthday') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-calendar"></i></span>
                                    <input type="date" value="{{ old('birthday') ?? null }}" name="birthday"
                                        id="birthday" class="form-control" placeholder="{{ _t('Birthday') }}" />
                                </div>
                            </div>
                            @error('birthday')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Residence Number') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-house"></i></span>
                                    <input type="text" value="{{ old('residence_number') ?? null }}"
                                        name="residence_number" id="residence_number" class="form-control"
                                        placeholder="{{ _t('Residence Number') }}" />
                                </div>
                            </div>
                            @error('residence_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Residence Expiration') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-house"></i></span>
                                    <input type="date" value="{{ old('residence_expiration') ?? null }}"
                                        name="residence_expiration" id="residence_expiration" class="form-control"
                                        placeholder="{{ _t('Residence Expiration') }}" />
                                </div>
                            </div>
                            @error('residence_expiration')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Insurance Company') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="text" value="{{ old('insurance_company') ?? null }}"
                                        name="insurance_company" id="insurance_company" class="form-control"
                                        placeholder="{{ _t('Insurance Company') }}" />
                                </div>
                            </div>
                            @error('insurance_company')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Insurance Number') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="text" value="{{ old('insurance_number') ?? null }}"
                                        name="insurance_number" id="insurance_number" class="form-control"
                                        placeholder="{{ _t('Insurance Number') }}" />
                                </div>
                            </div>
                            @error('insurance_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Insurance Expiration') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="date" value="{{ old('insurance_expiration') ?? null }}"
                                        name="insurance_expiration" id="insurance_expiration" class="form-control"
                                        placeholder="{{ _t('Insurance Expiration') }}" />
                                </div>
                            </div>
                            @error('insurance_expiration')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Insurance Card Expiration') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="date" value="{{ old('insurance_card_expiration') ?? null }}"
                                        name="insurance_card_expiration" id="insurance_card_expiration"
                                        class="form-control" placeholder="{{ _t('Insurance Card Expiration') }}" />
                                </div>
                            </div>
                            @error('insurance_card_expiration')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label" for="basic-icon-default-phone">{{ _t('Vacation Days') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-calendar"></i></span>
                                    <input type="number" value="{{ old('vacation_days') ?? 0 }}" name="vacation_days"
                                        id="vacation_days" class="form-control phone-mask"
                                        placeholder="{{ _t('Vacation Days') }}" required />
                                </div>
                            </div>
                            @error('vacation_days')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Holiday') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="holiday" name="holiday" class="holiday_select2 form-select"
                                            data-allow-clear="false" required>
                                            @foreach (App\Enums\WeekDaysEnum::cases() as $day)
                                                <option value="{{ $day->value }}"
                                                    {{ old('holiday') == $day->value ? 'selected' : '' }}>
                                                    {{ _t($day->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('holiday')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
