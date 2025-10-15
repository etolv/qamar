@extends('layouts/layoutMaster')

@section('title', 'Add Notification')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
            $('.type_select2').select2({
                placeholder: '{{ _t('Select Type') }}'
            }).on('change', function(e) {
                if (this.value == 'users') {
                    $('.users-div').show();
                } else {
                    $('.users-div').hide();
                }
            })
            $('.users_select2').select2({
                placeholder: '{{ _t('Select Users') }}',
                ajax: {
                    url: route('user.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                console.log(item);
                                return {
                                    id: item.id,
                                    text: item.name + ' - ' + item.phone
                                };
                            })
                        };
                    },
                    cache: true
                }
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
                    <h5 class="mb-0">{{ _t('Create') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('notification.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Title') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-comment"></i></span>
                                    <input type="text" name="{{ 'name' }}" value="{{ old('name') ?? '' }}"
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
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Body') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-comments"></i></span>
                                    <textarea name="{{ 'body' }}" required class="form-control" id="basic-icon-default-fullname"
                                        placeholder="{{ _t('Body') }}" aria-label="{{ _t('Body') }}" aria-describedby="basic-icon-default-fullname2"
                                        required>{{ old('body') ?? '' }}</textarea>
                                </div>
                                @error('body')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('To') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type" name="type" class="select2 type_select2 form-select"
                                            required>
                                            <option value="all">{{ _t('All') }}</option>
                                            <option value="customers">{{ _t('Customers') }}</option>
                                            <option value="employees">{{ _t('Employees') }}</option>
                                            <option value="drivers">{{ _t('Driver') }}</option>
                                            <option value="users" {{ isset($user) ? 'selected' : '' }}>
                                                {{ _t('Specific users') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 users-div" style="{{ isset($user) ? '' : 'display: none;' }}">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Users') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <div class="col-sm-10">
                                        <select id="users" name="users[]" class="users_select2 form-select"
                                            multiple="multiple" data-allow-clear="true">
                                            @if (isset($user))
                                                <option value="{{ $user->id }}" selected>
                                                    {{ $user->name . ' - ' . $user->phone }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('users[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
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
