@extends('layouts/layoutMaster')

@section('title', 'Edit booking')

@section('vendor-style')
  @vite([
      'resources/assets/vendor/libs/select2/select2.scss',
      'resources/assets/vendor/libs/leaflet/leaflet.scss',
      'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
      'resources/assets/vendor/libs/moment/moment.js',
      'resources/assets/vendor/libs/select2/select2.js',
      'resources/assets/vendor/libs/cleavejs/cleave.js',
      'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
      'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
      'resources/assets/vendor/libs/leaflet/leaflet.js'
  ])
@endsection

@section('page-script')
  <script>
    $(document).ready(function() {
      $('.status_select2').select2({});
      $('.customer_select2').select2({
        placeholder: '{{ _t('Select Customer') }}',
        ajax: {
          url: route('customer.search'),
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          processResults: function(data) {
            return {
              results: data.data.map(function(item) {
                return { id: item.type_id, text: item.name + " - " + item.phone };
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
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return { q: params.term, type: 'bookings_hairdresser' };
          },
          processResults: function(data) {
            return {
              results: data.data.map(function(item) {
                return { id: item.type_id, text: item.name + " - " + item.phone };
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
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return { q: params.term, type: 'employee_driver' };
          },
          processResults: function(data) {
            return {
              results: data.data.map(function(item) {
                return { id: item.type_id, text: item.name + " - " + item.phone };
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
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return { q: params.term, min_quantity: '1' };
          },
          processResults: function(data) {
            return {
              results: data.data.map(function(item) {
                return { id: item.id, text: item.product.name + " - " + item.barcode };
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
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return { q: params.term, department: 1 };
          },
          processResults: function(data) {
            return {
              results: data.data.data.map(function(item) {
                return { id: item.id, text: item.name };
              })
            };
          },
          cache: false
        }
      });

      // ✅ Coupon Select2
      $('.coupon_select2').select2({
        placeholder: '{{ _t('Select Coupon') }}',
        ajax: {
          url: route('coupon.search'),
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return { q: params.term };
          },
          processResults: function(data) {
            return {
              results: data.data.map(function(item) {
                return {
                  id: item.id,
                  text: item.name + " - " + item.code,
                  discount: item.discount,
                  is_percentage: item.is_percentage
                };
              })
            };
          },
          cache: false
        }
      });

      // === Map ===
      lng = "{{ $booking->lng }}";
      lat = "{{ $booking->lat }}";
      var map = L.map('map').setView([lat, lng], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      function onMapClick(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lng = e.latlng.lng.toFixed(6);
        $('#lat').val(lat);
        $('#lng').val(lng);
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
      }

      map.on('click', onMapClick);
      var marker = L.marker([lat, lng]).addTo(map);
    });
  </script>
@endsection

@section('content')
  <div class="row">
    <div class="col-xxl">
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">{{ _t('Edit Booking') }}</h5>
          <small class="text-muted float-end">{{ _t('Info') }}</small>
        </div>
        <div class="card-body">
          <form method="post" action="{{ route('booking.update', $booking->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Status --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Status') }} *</label>
              <div class="col-sm-10">
                <select id="status" name="status" class="status_select2 form-select" required>
                  @foreach (App\Enums\StatusEnum::cases() as $status)
                    <option value="{{ $status->value }}"
                            @if ($booking->status->value == $status->value) selected @endif>
                      {{ _t($status->name) }}
                    </option>
                  @endforeach
                </select>
                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Date --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Date') }} *</label>
              <div class="col-sm-10">
                @php $now = \Carbon\Carbon::now()->format('Y-m-d\TH:i'); @endphp
                <input type="datetime-local" name="date" min="{{ $now }}"
                       value="{{ old('date') ?? $booking->date }}" class="form-control" required />
                @error('date') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Description --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Description') }} *</label>
              <div class="col-sm-10">
                                <textarea name="description" class="form-control" rows="3"
                                          placeholder="{{ _t('Description') }}">{{ old('description') ?? $booking->description }}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Customer --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Customer') }} *</label>
              <div class="col-sm-10">
                <select id="customer_id" name="customer_id" class="customer_select2 form-select" required>
                  @if ($booking->customer)
                    <option value="{{ $booking->customer_id }}" selected>
                      {{ $booking->customer?->user?->name . ' - ' . $booking->customer?->user?->phone }}
                    </option>
                  @endif
                </select>
                @error('customer_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Employee --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Employee') }} *</label>
              <div class="col-sm-10">
                <select id="employee_id" name="employee_id" class="employee_select2 form-select" required>
                  @if ($booking->employee)
                    <option value="{{ $booking->employee_id }}" selected>
                      {{ $booking->employee->user->name . ' - ' . $booking->employee->user->phone }}
                    </option>
                  @endif
                </select>
                @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Coupon --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Coupon') }}</label>
              <div class="col-sm-10">
                <select id="coupon_id" name="coupon_id" class="coupon_select2 form-select" data-allow-clear="true">
                  @if ($booking->coupon)
                    <option value="{{ $booking->coupon->id }}" selected>
                      {{ $booking->coupon->name }} - {{ $booking->coupon->code }}
                    </option>
                  @endif
                </select>
                @error('coupon_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Products --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Products') }}</label>
              <div class="col-sm-10">
                <select id="products" name="stocks[]" class="product_select2 form-select" multiple>
                  @foreach ($booking->products as $product)
                    <option value="{{ $product->id }}" selected>
                      {{ $product->product->name . ' - ' . $product->barcode }}
                    </option>
                  @endforeach
                </select>
                @error('stocks') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Services --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Services') }}</label>
              <div class="col-sm-10">
                <select id="services" name="services[]" class="service_select2 form-select" multiple>
                  @foreach ($booking->services as $service)
                    <option value="{{ $service->id }}" selected>{{ $service->name }}</option>
                  @endforeach
                </select>
                @error('services') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Address --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Address') }} *</label>
              <div class="col-sm-10">
                                <textarea name="address" class="form-control" rows="3"
                                          placeholder="{{ _t('address') }}">{{ old('address') ?? $booking->address }}</textarea>
                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            {{-- Location --}}
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">{{ _t('Location') }} *</h5>
              </div>
              <div class="card-body">
                <div id="map" style="height: 300px; cursor: pointer;"></div>
                <input type="hidden" id="lat" name="lat" value="{{ $booking->lat }}">
                <input type="hidden" id="lng" name="lng" value="{{ $booking->lng }}">
              </div>
            </div>

            {{-- Submit --}}
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
