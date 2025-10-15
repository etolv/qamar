@extends('layouts/layoutMaster')

@section('title', 'Edit booking')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.status_select2').select2({});
            $('.customer_select2').select2({
                placeholder: '{{ _t('Select Customer') }}',
                ajax: {
                    url: route('customer.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.type_id,
                                    text: item.name + " - " + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.employee_select2').select2({
                placeholder: '{{ _t('Select Employee') }}',
                ajax: {
                    url: route('employee.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            type: 'bookings_hairdresser' // your custom parameter
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.type_id,
                                    text: item.name + " - " + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.driver_select2').select2({
                placeholder: '{{ _t('Select Driver') }}',
                ajax: {
                    url: route('employee.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            type: 'employee_driver' // your custom parameter
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.type_id,
                                    text: item.name + " - " + item.phone
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.product_select2').select2({
                placeholder: '{{ _t('Select Products') }}',
                ajax: {
                    url: route('stock.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            min_quantity: '1', // your custom parameter
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.product.name + " - " + item.barcode
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            $('.service_select2').select2({
                placeholder: '{{ _t('Select Services') }}',
                ajax: {
                    url: route('service.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            department: 1, // Salon department ID
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
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

            lng = "{{ $booking->lng }}";
            lat = "{{ $booking->lat }}";
            var map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            function onMapClick(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
            }
            map.on('click', onMapClick);
            var marker = L.marker([lat, lng]).addTo(map);
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
                    <form method="post" action="{{ route('booking.update', $booking->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Status') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="status" name="status" class="status_select2 form-select"
                                            data-allow-clear="true" required>
                                            @foreach (App\Enums\StatusEnum::cases() as $status)
                                                <option value="{{ $status->value }}"
                                                    @if ($booking->status->value == $status->value) selected @endif>
                                                    {{ _t($status->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Date') }}
                                *</label>
                            <div class="col-sm-10">
                                @php
                                    // Get the current date and time
                                    $now = \Carbon\Carbon::now()->format('Y-m-d\TH:i');
                                @endphp
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <input type="datetime-local" name="date" min="{{ $now }}"
                                        value="{{ old('date') ?? $booking->date }}" class="form-control"
                                        id="basic-icon-default-fullname" placeholder="{{ _t('Date') }}"
                                        aria-describedby="basic-icon-default-fullname2" required />
                                </div>
                                @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-fullname">{{ _t('Description') }} *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <textarea name="description" class="form-control" id="basic-icon-default-fullsku" placeholder="{{ _t('Description') }}"
                                        aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('description') ?? $booking->description }}</textarea>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Customer') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="customer_id" name="customer_id" class="customer_select2 form-select"
                                            data-allow-clear="true" required>
                                            <option value="{{ $booking->customer_id }}" selected>
                                                {{ $booking->customer?->user?->name . ' - ' . $booking->customer?->user?->phone }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @error('customer_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Employee') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="employee_id" name="employee_id" class="employee_select2 form-select"
                                            data-allow-clear="true" required>
                                            @if ($booking->employee)
                                                <option value="{{ $booking->employee_id }}" selected>
                                                    {{ $booking->employee->user->name . ' - ' . $booking->employee->user->phone }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('employee_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Driver') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="driver_id" name="driver_id" class="driver_select2 form-select"
                                            data-allow-clear="true" required>
                                            @if ($booking->driver)
                                                <option value="{{ $booking->driver_id }}" selected>
                                                    {{ $booking->driver->user->name . ' - ' . $booking->driver->user->phone }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @error('driver_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Products') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="products" name="stocks[]" class="product_select2 form-select"
                                            data-allow-clear="true" multiple>
                                            @foreach ($booking->products as $product)
                                                <option value="{{ $product->id }}" selected>
                                                    {{ $product->product->name . ' - ' . $product->barcode }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('products')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Services') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="services" name="services[]" class="service_select2 form-select"
                                            data-allow-clear="true" required multiple>
                                            @foreach ($booking->services as $service)
                                                <option value="{{ $service->id }}" selected>{{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @error('services')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{ _t('Address') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                            class="ti ti-clipboard"></i></span>
                                    <textarea name="address" class="form-control" id="basic-icon-default-fullsku" placeholder="{{ _t('address') }}"
                                        aria-describedby="basic-icon-default-fullsku2" rows="3">{{ old('address') ?? $booking->address }}</textarea>
                                </div>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ _t('Location') }} *</h5>
                            </div>
                            <div class="card-body">
                                <div data-repeater-list="group-a">
                                    <div data-repeater-item>
                                        <div class="row mb-3">
                                            <div id="map" style="height: 300px; cursor: pointer;"></div>
                                            <input type="hidden" id="lat" value="{{ $booking->lat }}"
                                                name="lat">
                                            <input type="hidden" id="lng" value="{{ $booking->lng }}"
                                                name="lng">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-start">
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
