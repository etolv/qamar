@extends('layouts/layoutMaster')

@section('title', 'Add Customer')

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
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/leaflet/leaflet.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        $('.country_code_select2').select2({});
        $('.role_select2').select2({
            placeholder: '{{_t("Select City")}}'
        });
        $('.city_select2').select2({
            placeholder: '{{_t("Select City")}}',
            ajax: {
                url: route('city.search'),
                headers: {
                    'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                },
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    console.log('data');
                    console.log(data.data.data)
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });
        $('.customer_branch_select2').select2({
            placeholder: '{{_t("Select Branch")}}',
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
                <form method="post" action="{{route('customer.store')}}" enctype="multipart/form-data">
                    @csrf
                    @include('models.newCustomer')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection