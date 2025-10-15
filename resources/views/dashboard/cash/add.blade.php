@extends('layouts/layoutMaster')

@section('title', 'New Cash Withdrawal')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
'resources/assets/vendor/libs/pickr/pickr.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        let status = 0;
        $('.type_select2').select2({});
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
                                text: item.name + ' - ' + item.phone
                            };
                        })
                    };
                },
                cache: false
            }
        });

        $('.type_select2').change(function() {
            let type = $(this).val();
            if (type != 2) {
                $('.employee_select2').prop('required', true);
                $('.employee-div').removeClass('d-none');
                $('#due_date').prop('required', true);
                $('.due-date-div').removeClass('d-none');
                $('.split-payment-div').removeClass('d-none');
                $('.split-div').removeClass('d-none');
            } else {
                $('.employee_select2').prop('required', false);
                $('.employee-div').addClass('d-none');
                $('#due_date').prop('required', false);
                $('.due-date-div').addClass('d-none');
                $('.split-payment-div').addClass('d-none');
                $('.split-div').addClass('d-none');
            }
        });
        $('.split_payment').change(function() {
            let checked = $(this).is(':checked');
            if (checked) {
                $('.due-date-div').addClass('d-none');
                $('#due_date').prop('required', false);
                $('#split_months_count').prop('required', true);
                $('.split-div').removeClass('d-none');
            } else {
                $('.due-date-div').removeClass('d-none');
                $('#due_date').prop('required', true);
                $('#split_months_count').prop('required', false);
                $('.split-div').addClass('d-none');
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
                <h5 class="mb-0">{{_t('Create')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('cash-flow.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Type')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fa-solid fa-t"></i></span>
                                <div class="col-sm-10">
                                    <select id="type" name="type" class="type_select2 form-select" data-allow-clear="true">
                                        @foreach(App\Enums\CashFlowTypeEnum::cases() as $type)
                                        <option value="{{$type->value}}">{{_t($type->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('type')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 employee-div">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Employee')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
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
                    <div class="row mb-3 split-payment-div">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Split Payments')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <label class="switch switch-primary me-0">
                                    <input type="checkbox" class="switch-input split_payment" name="split_payment" id="split_payment" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label"></span>
                                </label>
                            </div>
                            @error('split_payment')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 split-div d-none">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Split Months Count')}} *</label>
                        <div class="col-sm-10">
                            <input type="number" step="1" min="1" class="form-control" value="{{old('split_months_count')}}" placeholder="{{_t('Split Months Count')}}" name="split_months_count" id="split_months_count" />
                            @error('split_months_count')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 due-date-div">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Due Date')}} *</label>
                        <div class="col-sm-10">
                            <input type="date" min="{{date('Y-m-d')}}" class="form-control" value="{{old('due_date')}}" placeholder="{{_t('Due date')}}" name="due_date" id="due_date" required />
                            @error('due_date')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Amount')}} *</label>
                        <div class="col-sm-10">
                            <input type="number" step="any" class="form-control" value="{{old('amount')}}" placeholder="{{_t('Amount')}}" name="amount" />
                            @error('amount')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Reason')}} *</label>
                        <div class="col-sm-10">
                            <textarea rows="3" class="form-control" placeholder="{{_t('Reason')}}" name="reason">{{old('reason')}}</textarea>
                            @error('reason')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('End Date')}}</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" value="{{old('end')}}" name="end_date" />
                            @error('end_date')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div> -->
                    <div class="row justify-content-start">
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