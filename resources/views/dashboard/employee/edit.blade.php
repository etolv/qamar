@extends('layouts/layoutMaster')

@section('title', ' Horizontal Layouts - Forms')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.country_code_select2').select2({});
            $('.role_select2').select2({
                placeholder: '{{ _t('Select Role') }}'
            });
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
            $('.job_select2').select2({
                placeholder: '{{ _t('Select Job') }}',
                ajax: {
                    url: route('job.search'),
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
                                    text: item.title
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
            $('.repeater-add').click(function(e) {
                e.preventDefault();
                var repeaterWrapper = $(this).closest('.repeater-wrapper');
                // var maxAdvantages = 5; // Maximum allowed advantages
                var currentCount = repeaterWrapper.find('.repeater-item').length;

                // if (currentCount < maxAdvantages) {
                // var template = repeaterWrapper.find('.repeater-item').html();
                // var item = $('<div class="repeater-item">' + template + '</div>');
                // item.find('.form-control').val(''); // Clear input values
                var newRepeaterItem = `
                    <div class="repeater-item">
                        <div class="row">
                            <div class="mb-3 col-3">
                                <label class="form-label" for="name-repeater-${currentCount+1}-2">{{ _t('Name') }}</label>
                                <input type="text" name="employee_infos[${currentCount}][name]" id="name-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Name') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="value-repeater-${currentCount+1}-2">{{ _t('Value') }}</label>
                                <input type="text" name="employee_infos[${currentCount}][value]" id="value-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('Value') }}" required />
                            </div>
                            <div class="mb-3 col-3">
                                <label class="form-label" for="price-repeater-${currentCount+1}-2">{{ _t('File') }}</label>
                                <input type="file" name="employee_infos[${currentCount}][file]" id="file-repeater-${currentCount+1}-2" class="form-control" placeholder="{{ _t('File') }}" />
                            </div>
                            <div class="mb-3 col-1">
                                <label class="form-label" for="remover">{{ _t('Remove') }}</label>
                                <button class="btn btn-danger repeater-remove"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                repeaterWrapper.find('.repeater-items').append(newRepeaterItem);
            });
            $('.repeater-wrapper').on('click', '.repeater-remove', function(e) {
                e.preventDefault();
                var item = $(this).closest('.repeater-item');
                item.remove();
            });
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Edit') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" value="{{ $employee->user_id }}" name="id" />
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-user"></i></span>
                                    <input type="text" name="name" value="{{ old('name') ?? $employee->user->name }}"
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
                                @include('components.phone_with_code', ['user' => $employee->user])
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
                                    <input type="email" name="email"
                                        value="{{ old('email') ?? $employee->user->email }}" id="basic-icon-default-email"
                                        class="form-control" placeholder="{{ _t('Email') }}"
                                        aria-label="{{ _t('Email') }}" aria-describedby="basic-icon-default-email2" />
                                </div>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Password') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="password" value="{{ old('password') }}"
                                        value="{{ old('password') }}" id="basic-icon-default-email" class="form-control"
                                        placeholder="******" aria-label="john.doe"
                                        aria-describedby="basic-icon-default-email2" />
                                </div>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Employee No') . ' (' . _t('Fingerprit device') . ')' }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="text" name="employee_no"
                                        value="{{ old('employee_no') ?? $employee->employee_no }}"
                                        id="basic-icon-default-email" class="form-control"
                                        placeholder="{{ _t('Employee No') . ' (' . _t('Fingerprit device') . ')' }}" />
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
                                @if ($employee->user->getFirstMedia('profile'))
                                    <img src="{{ $employee->user->getFirstMedia('profile')?->getUrl() }}" id="profile-img"
                                        alt="" class="rounded-circle" style="width: 30px; height: 30px;" />
                                @endif
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Role') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="role_id" name="role_id" class="role_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="">Select</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ $employee->user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('role_id')
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
                                            <option value="{{ $employee->city_id }}" selected>{{ $employee->city->name }}
                                            </option>
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
                                            <option value="{{ $employee->branch_id }}" selected>
                                                {{ $employee->branch->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Job') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="job_id" name="job_id" class="job_select2 form-select"
                                            data-allow-clear="true">
                                            <option value="{{ $employee->job_id }}" selected>{{ $employee->job->title }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @error('job_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Nationality') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-globe"></i></span>
                                    <div class="col-sm-10">
                                        <select id="nationality_id" name="nationality_id"
                                            class="nationality_select2 form-select" data-allow-clear="true" required>
                                            <option value="{{ $employee->nationality_id }}" selected>
                                                {{ $employee->nationality->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('nationality_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Birthday') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-calendar"></i></span>
                                    <input type="date" value="{{ old('birthday') ?? $employee->birthday }}"
                                        name="birthday" id="birthday" class="form-control"
                                        placeholder="{{ _t('Birthday') }}" />
                                </div>
                            </div>
                            @error('birthday')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Start Work') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-calendar"></i></span>
                                    <input type="date" value="{{ old('start_work') ?? $employee->start_work }}"
                                        name="start_work" id="start_work" class="form-control"
                                        placeholder="{{ _t('Start Work') }}" />
                                </div>
                            </div>
                            @error('start_work')
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
                                    <input type="text"
                                        value="{{ old('residence_number') ?? $employee->residence_number }}"
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
                                    <input type="date"
                                        value="{{ old('residence_expiration') ?? $employee->residence_expiration }}"
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
                                    <input type="text"
                                        value="{{ old('insurance_company') ?? $employee->insurance_company }}"
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
                                    <input type="text"
                                        value="{{ old('insurance_number') ?? $employee->insurance_number }}"
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
                                    <input type="date"
                                        value="{{ old('insurance_expiration') ?? $employee->insurance_expiration }}"
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
                                    <input type="date"
                                        value="{{ old('insurance_card_expiration') ?? $employee->insurance_card_expiration }}"
                                        name="insurance_card_expiration" id="insurance_card_expiration"
                                        class="form-control" placeholder="{{ _t('Insurance Card Expiration') }}" />
                                </div>
                            </div>
                            @error('insurance_card_expiration')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Health Number') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="text" value="{{ old('health_number') ?? $employee->health_number }}"
                                        name="health_number" id="health_number" class="form-control"
                                        placeholder="{{ _t('Health Number') }}" />
                                </div>
                            </div>
                            @error('health_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Passport Number') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="text"
                                        value="{{ old('passport_number') ?? $employee->passport_number }}"
                                        name="passport_number" id="passport_number" class="form-control"
                                        placeholder="{{ _t('Passport Number') }}" />
                                </div>
                            </div>
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label"
                                for="basic-icon-default-phone">{{ _t('Passport Expiration') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="fa-solid fa-notes-medical"></i></span>
                                    <input type="date"
                                        value="{{ old('passport_expiration') ?? $employee->passport_expiration }}"
                                        name="passport_expiration" id="passport_expiration" class="form-control"
                                        placeholder="{{ _t('Passport Expiration') }}" />
                                </div>
                            </div>
                            @error('insurance_expiration')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 form-label" for="basic-icon-default-phone">{{ _t('Vacation Days') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i
                                            class="ti ti-phone"></i></span>
                                    <input type="number" value="{{ old('vacation_days') ?? $employee->vacation_days }}"
                                        name="vacation_days" id="vacation_days" class="form-control phone-mask"
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
                                            data-allow-clear="true" required>
                                            @foreach (App\Enums\WeekDaysEnum::cases() as $day)
                                                <option value="{{ $day->value }}"
                                                    {{ old('holiday') == $day->value ? 'selected' : ($employee?->holiday == $day ? 'selected' : '') }}>
                                                    {{ _t($day->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('holiday')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Information') }}</h5>
                            </div>
                            @error('employee_infos')
                                <span class=" text-danger">{{ $message }}</span>
                            @enderror
                            <div class="card-body repeater-wrapper">
                                <div class="repeater-items">
                                    @foreach ($employee->employeeInfos as $index => $info)
                                        <div class="repeater-item">
                                            <input type="hidden" name="employee_infos[{{ $index }}][id]"
                                                value="{{ $info->id }}">
                                            <div class="row">
                                                <div class="mb-3 col-3">
                                                    <label class="form-label"
                                                        for="name-repeater-{{ $index }}-2">{{ _t('Name') }}</label>
                                                    <input type="text"
                                                        name="employee_infos[{{ $index }}][name]"
                                                        value="{{ $info->name }}"
                                                        id="name-repeater-{{ $index }}-2" class="form-control"
                                                        placeholder="{{ _t('Name') }}" required />
                                                </div>
                                                <div class="mb-3 col-3">
                                                    <label class="form-label"
                                                        for="value-repeater-{{ $index }}-2">{{ _t('Value') }}</label>
                                                    <input type="text"
                                                        name="employee_infos[{{ $index }}][value]"
                                                        value="{{ $info->value }}"
                                                        id="value-repeater-{{ $index }}-2" class="form-control"
                                                        placeholder="{{ _t('Value') }}" required />
                                                </div>
                                                <div class="mb-3 col-3">
                                                    <label class="form-label"
                                                        for="price-repeater-{{ $index }}-2">{{ _t('File') }}</label>
                                                    <input type="file"
                                                        name="employee_infos[{{ $index }}][file]"
                                                        id="file-repeater-{{ $index }}-2" class="form-control"
                                                        placeholder="{{ _t('File') }}" />
                                                    @if ($info->getFirstMedia('file'))
                                                        <a href="{{ $info->getFirstMediaUrl('file') }}" download> <i
                                                                class="fa-solid fa-file"></i></a>
                                                    @endif
                                                </div>
                                                <div class="mb-3 col-1">
                                                    <label class="form-label" for="remover">{{ _t('Remove') }}</label>
                                                    <button class="btn btn-danger repeater-remove"><i
                                                            class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- repeater item --}}
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-primary repeater-add"><i
                                                class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ _t('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
