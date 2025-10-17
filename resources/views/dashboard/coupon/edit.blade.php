@extends('layouts/layoutMaster')

@section('title', 'Edit Coupon')

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
      'resources/assets/vendor/libs/select2/select2.js',
      'resources/assets/vendor/libs/dropzone/dropzone.js',
      'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
      'resources/assets/vendor/libs/flatpickr/flatpickr.js',
      'resources/assets/vendor/libs/tagify/tagify.js',
      'resources/assets/vendor/libs/leaflet/leaflet.js'
  ])
@endsection

@section('page-script')
  <script>
    $(document).ready(function() {
      // ====== Services ======
      const serviceSelect = $('.service_select2');
      serviceSelect.select2({
        placeholder: '{{ _t('Select Services') }}',
        ajax: {
          url: route('service.search'),
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          processResults: function(data) {
            return {
              results: data.data.data.map(function(item) {
                return { id: item.id, text: item.name };
              })
            };
          },
          cache: true
        }
      });

      // ✅ أضف الخدمات المرتبطة بالكوبون الحالي
      const selectedServices = @json($coupon->services->map(fn($s) => ['id' => $s->id, 'text' => $s->name]));
      selectedServices.forEach(service => {
        const option = new Option(service.text, service.id, true, true);
        serviceSelect.append(option).trigger('change');
      });

      // ====== Products ======
      const productSelect = $('.product_select2');
      productSelect.select2({
        placeholder: '{{ _t('Select Products') }}',
        ajax: {
          url: route('product.search'),
          headers: { 'Authorization': 'Bearer ' + $('meta[name="csrf-token"]').attr('content') },
          dataType: 'json',
          delay: 250,
          processResults: function(data) {
            return {
              results: data.data.data.map(function(item) {
                return { id: item.id, text: item.name + ' - ' + item.sku };
              })
            };
          },
          cache: true
        }
      });

      // ✅ أضف المنتجات المرتبطة بالكوبون الحالي
      const selectedProducts = @json($coupon->products->map(fn($p) => ['id' => $p->id, 'text' => $p->name . ' - ' . $p->sku]));
      selectedProducts.forEach(product => {
        const option = new Option(product.text, product.id, true, true);
        productSelect.append(option).trigger('change');
      });

      // ====== Toggle all services/products ======
      $('.all_services').change(function() {
        $('.service_select2').prop('disabled', $(this).is(':checked')).val(null).trigger('change');
      });

      $('.all_products').change(function() {
        $('.product_select2').prop('disabled', $(this).is(':checked')).val(null).trigger('change');
      });
    });
  </script>
@endsection

@section('content')
  <div class="row">
    <div class="col-xxl">
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">{{ _t('Edit Coupon') }}</h5>
          <small class="text-muted float-end">{{ _t('Update coupon details') }}</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('coupon.update', $coupon->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Coupon Name --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Name') }} *</label>
              <div class="col-sm-10">
                <input type="text" name="name" value="{{ old('name', $coupon->name) }}" class="form-control" required>
                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
              </div>
            </div>

            {{-- Coupon Code --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Code') }}</label>
              <div class="col-sm-10">
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" class="form-control">
                @error('code')<span class="text-danger">{{ $message }}</span>@enderror
              </div>
            </div>

            {{-- All Services --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('All Services') }}</label>
              <div class="col-sm-10">
                <label class="switch switch-primary">
                  <input type="checkbox" class="switch-input all_services" name="all_services"
                    {{ old('all_services', $coupon->all_services ?? false) ? 'checked' : '' }}>
                  <span class="switch-toggle-slider"></span>
                </label>
              </div>
            </div>

            {{-- Services --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Services') }}</label>
              <div class="col-sm-10">
                <select name="services[]" class="service_select2 form-select" multiple></select>
                @error('services')<span class="text-danger">{{ $message }}</span>@enderror
              </div>
            </div>

            {{-- All Products --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('All Products') }}</label>
              <div class="col-sm-10">
                <label class="switch switch-primary">
                  <input type="checkbox" class="switch-input all_products" name="all_products"
                    {{ old('all_products', $coupon->all_products ?? false) ? 'checked' : '' }}>
                  <span class="switch-toggle-slider"></span>
                </label>
              </div>
            </div>

            {{-- Products --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Products') }}</label>
              <div class="col-sm-10">
                <select name="products[]" class="product_select2 form-select" multiple></select>
                @error('products')<span class="text-danger">{{ $message }}</span>@enderror
              </div>
            </div>

            {{-- Discount --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('Discount Percentage') }} *</label>
              <div class="col-sm-10">
                <input type="number" step="0.1" name="discount" value="{{ old('discount', $coupon->discount) }}" class="form-control" required>
                @error('discount')<span class="text-danger">{{ $message }}</span>@enderror
              </div>
            </div>

            {{-- Dates --}}
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">{{ _t('From Date') }}</label>
              <div class="col-sm-4">
                <input type="date" name="from_date" value="{{ old('from_date', $coupon->from_date) }}" class="form-control">
              </div>
              <label class="col-sm-2 col-form-label">{{ _t('To Date') }}</label>
              <div class="col-sm-4">
                <input type="date" name="to_date" value="{{ old('to_date', $coupon->to_date) }}" class="form-control">
              </div>
            </div>

            {{-- Submit --}}
            <div class="row justify-content-end">
              <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">{{ _t('Update') }}</button>
                <a href="{{ route('coupon.index') }}" class="btn btn-secondary">{{ _t('Cancel') }}</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
