<div>
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Name') }} *</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-control"
                    id="basic-icon-default-fullname" placeholder="{{ _t('Name') }}" aria-label="{{ _t('Name') }}"
                    aria-describedby="basic-icon-default-fullname2" required />
            </div>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Phone') }} *</label>
        <div class="col-sm-10">
            <div class="row">
                @include('components.phone_with_code', ['user' => null])
            </div>
            @error('phone')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Password') }} *</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                <input type="password" name="password" value="{{ 'password' }}" id="basic-icon-default-email"
                    class="form-control" placeholder="******" aria-label="john.doe"
                    aria-describedby="basic-icon-default-email2" readonly />
            </div>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('City') }} *</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-globe"></i></span>
                <div class="col-sm-10">
                    <select id="city_id" name="city_id" class="city_select2 form-select" data-allow-clear="true"
                        required>
                    </select>
                </div>
            </div>
            @error('city_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mb-3 {{ $user_branch ? 'd-none' : '' }}">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Branch') }} *</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                <div class="col-sm-10">
                    <select id="customer_branch_id" name="branch_id" class="customer_branch_select2 form-select"
                        data-allow-clear="true" required>
                        @if ($user_branch)
                            <option value="{{ $user_branch->id }}">{{ $user_branch->name }}</option>
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
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Address') }}</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-home"></i></span>
                <textarea name="address" value="{{ old('address') }}" id="basic-icon-default-email" class="form-control"
                    aria-label="john.doe" aria-describedby="basic-icon-default-email2"> {{ old('address') }}</textarea>
            </div>
            @error('address')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Profile image') }}</label>
        <div class="col-sm-10">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-image"></i></span>
                <input type="file" name="image" value="{{ old('image') }}" value="{{ old('image') }}"
                    id="basic-icon-default-email" accept="image/*" class="form-control"
                    aria-describedby="basic-icon-default-email2" />
            </div>
            @error('image')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="row justify-content-end">
        <div class="col-sm-10">
            <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
        </div>
    </div>
</div>
