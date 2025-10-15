@extends('layouts/layoutMaster')

@section('title', 'Add Unit')

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
'resources/assets/vendor/libs/dropzone/dropzone.js',
'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/leaflet/leaflet.js',
'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        $('.unit_select2').select2({
            placeholder: '{{_t("Select Unit")}}',
            ajax: {
                url: route('unit.search'),
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
                <h5 class="mb-0">{{_t('Create')}}</h5> <small class="text-muted float-end">{{_t('Info')}}</small>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('unit.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Name')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-tag"></i></span>
                                <input type="text" name="name" value="{{old('name')}}" required class="form-control" id="basic-icon-default-fullname" placeholder="{{_t('Name')}}" aria-label="{{_t('Name')}}" aria-describedby="basic-icon-default-fullname2" required />
                            </div>
                            @error('name')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{_t('Parent Unit')}} *</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                <div class="col-sm-10">
                                    <select id="unit_id" name="unit_id" class="unit_select2 form-select" data-allow-clear="true">
                                    </select>
                                </div>
                            </div>
                            @error('unit_id')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-icon-default-fullname">{{_t('Factor')}}</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-tag"></i></span>
                                <input type="number" name="factor" value="{{old('factor') ?? 1}}" class="form-control" id="basic-icon-default-factor" placeholder="{{_t('factor')}}" aria-label="{{_t('factor')}}" aria-describedby="basic-icon-default-factor2" required />
                            </div>
                            @error('factor')
                            <span class="text-danger">{{$message }}</span>
                            @enderror
                        </div>
                    </div>
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