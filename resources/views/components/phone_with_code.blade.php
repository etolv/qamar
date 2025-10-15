<div class="row">
    <!-- Country Code Input -->
    <div class="col-3">
        <div class="input-group">
            <select id="dial_code" name="dial_code" class="form-control country_code_select2" required>
                @foreach ($countries_code as $code)
                    <option value="{{ $code['dial_code'] }}"
                        {{ $user?->dial_code == $code['dial_code'] ? 'selected' : '' }}>
                        {{ $code['code'] . ' (' . $code['dial_code'] . ')' }}</option>
                @endforeach
            </select>
            <!-- <input type="tel" name="country_code" value="{{ old('country_code') }}" class="form-control" placeholder="Code" aria-label="Country Code" aria-describedby="basic-addon-code" required> -->
        </div>
    </div>

    <!-- Phone Number Input -->
    <div class="col-9">
        <input type="number" name="phone" value="{{ old('phone') ?? $user?->phone }}" id="basic-icon-default-phone"
            class="form-control" placeholder="{{ _t('Phone') }}" aria-label="{{ _t('Phone') }}"
            {{ isset($required) ? 'required' : '' }}>
    </div>
</div>
