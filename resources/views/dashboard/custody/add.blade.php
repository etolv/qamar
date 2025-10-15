@extends('layouts/layoutMaster')

@section('title', 'New Custody')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        $('.stock_select2').select2({
            placeholder: '{{_t("Select Stock")}}',
            ajax: {
                url: route('stock.search'),
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
                                text: item.product.name + " - " + item.product.sku,
                                price: item.price,
                                quantity: item.quantity
                            };
                        })
                    };
                },
                cache: false
            }
        });
        $('.employee_select2').select2({
            placeholder: '{{_t("Select Employee")}}',
            ajax: {
                url: route('employee.search'),
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
        $('.stock_select2').change(function() {
            let product = $(this).select2('data')[0];
            $('#price').val(product.price);
            $('#quantity').attr('max', product.quantity);
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
                <form method="post" action="{{route('custody.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Stock')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="stock_id" name="stock_id" class="stock_select2 form-select" data-allow-clear="true" required>
                                    </select>
                                </div>
                            </div>
                            @error('stock_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Employee')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="employee_id" name="employee_id" class="employee_select2 form-select" data-allow-clear="true" required>
                                    </select>
                                </div>
                            </div>
                            @error('employee_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Quantity')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-clipboard"></i></span>
                                <input type="number" min="1" name="quantity" value="{{old('quantity') ?? 1}}" required class="form-control" id="quantity" placeholder="{{_t('quantity')}}" aria-label="{{_t('quantity')}}" aria-describedby="basic-icon-default-fullquantity2" required />
                            </div>
                            @error('quantity')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Price')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="span-price" class="input-group-text"><i class="ti ti-clipboard"></i></span>
                                <input type="number" min="1" step="0.01" name="price" value="{{old('price')}}" required class="form-control" id="price" placeholder="{{_t('quantity')}}" aria-label="{{_t('quantity')}}" aria-describedby="basic-icon-default-fullquantity2" required />
                            </div>
                            @error('price')
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