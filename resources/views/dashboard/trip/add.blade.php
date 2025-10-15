@extends('layouts/layoutMaster')

@section('title', ' Create new driver')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css"
        integrity="sha512-eD3SR/R7bcJ9YJeaUe7KX8u8naADgalpY/oNJ6AHvp1ODHF3iR8V9W4UgU611SD/jI0GsFbijyDBAzSOg+n+iQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/leaflet/leaflet.js', 'resources/assets/vendor/libs/leaflet/leaflet-routing-config.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection
@section('page-style')
    @ensection
@section('page-script')
    <script>
        $(document).ready(function() {
            $('.type_select2').select2();
            $('.driver_select2').select2({
                placeholder: '{{ _t('Select Driver') }}',
                ajax: {
                    url: route('driver.search'),
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
            $('.booking_select2').select2({
                placeholder: '{{ _t('Select Booking') }}',
                ajax: {
                    url: route('booking.search'),
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="csrf-token"]')
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: `#${item.id} Customer: ${item.customer.user.name} - ${item.customer.user.phone} Address: ${item.address}`
                                };
                            })
                        };
                    },
                    cache: false
                }
            });
            // Initialize map and set view to Riyadh, Saudi Arabia
            var map = L.map('map').setView([24.7136, 46.6753], 13); // Riyadh's coordinates

            // Add OpenStreetMap tile layer to the map
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // Markers for "from" and "to" points
            var fromMarker = null;
            var toMarker = null;
            var routeControl = null; // To store the routing control object

            // Boolean to track if we are setting the "from" or "to" point
            var isSettingFrom = true;

            // Event listener for map clicks
            map.on('click', function(e) {
                if (isSettingFrom) {
                    // Set the "from" point marker
                    if (fromMarker) {
                        map.removeLayer(fromMarker); // Remove the old marker
                    }
                    fromMarker = L.marker(e.latlng).addTo(map).bindPopup("From Point").openPopup();

                    // Set hidden inputs for the "from" point
                    document.getElementById('from_lat').value = e.latlng.lat;
                    document.getElementById('from_lng').value = e.latlng.lng;

                    // Now switch to setting the "to" point
                    isSettingFrom = false;

                    // Remove existing route if it exists
                    if (routeControl) {
                        map.removeControl(routeControl);
                        routeControl = null;
                    }

                } else {
                    // Set the "to" point marker
                    if (toMarker) {
                        map.removeLayer(toMarker); // Remove the old marker
                    }
                    var greenIcon = new L.Icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                    toMarker = L.marker(e.latlng, {
                        icon: greenIcon
                    }).addTo(map).bindPopup("To Point").openPopup();

                    // Set hidden inputs for the "to" point
                    document.getElementById('to_lat').value = e.latlng.lat;
                    document.getElementById('to_lng').value = e.latlng.lng;

                    // Draw a route between the "from" and "to" points
                    if (fromMarker) {
                        var fromLatLng = fromMarker.getLatLng();
                        var toLatLng = toMarker.getLatLng();

                        // Create a routing control to display the route
                        routeControl = L.Routing.control({
                            waypoints: [
                                L.latLng(fromLatLng.lat, fromLatLng.lng),
                                L.latLng(toLatLng.lat, toLatLng.lng)
                            ],
                            routeWhileDragging: true,
                            show: true,
                            lineOptions: {
                                styles: [{
                                        color: 'blue',
                                        opacity: 0.7,
                                        weight: 3
                                    } // Customize the route line style
                                ]
                            }
                        }).addTo(map);
                    }

                    // Reset to setting the "from" point
                    isSettingFrom = true;
                }
            });

            $('.type_select2').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue == 'booking') {
                    $('.booking-div').removeClass('d-none');
                    $('.booking_select2').attr('required', true);
                } else {
                    $('.booking-div').addClass('d-none');
                    $('.booking_select2').attr('required', false);
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
                    <form method="post" action="{{ route('trip.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Driver') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="driver_id" name="driver_id" class="driver_select2 form-select"
                                            data-allow-clear="true" required>
                                        </select>
                                    </div>
                                </div>
                                @error('driver_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="type" name="type" class="type_select2 form-select" required>
                                            <option value="general" selected>{{ _t('General') }}</option>
                                            <option value="booking">{{ _t('Booking') }}</option>
                                        </select>
                                    </div>
                                </div>
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 booking-div d-none">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Booking') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <select id="booking_id" name="booking_id" class="booking_select2 form-select"
                                            data-allow-clear="true">
                                        </select>
                                    </div>
                                </div>
                                @error('booking_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('Description') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <textarea rows="2" class="form-control" name="description" placeholder="{{ _t('description') }}">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"
                                for="basic-icon-default-email">{{ _t('From Description') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <textarea rows="2" class="form-control" name="from" placeholder="{{ _t('From') }}" required>{{ old('from') }}</textarea>
                                    </div>
                                </div>
                                @error('from')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('To') }}
                                *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-settings"></i></span>
                                    <div class="col-sm-10">
                                        <textarea rows="2" class="form-control" name="to" placeholder="{{ _t('To') }}" required>{{ old('to') }}</textarea>
                                    </div>
                                </div>
                                @error('to')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title m-0">{{ _t('Trip From - To') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id="map" style="height:400px;"></div>
                            </div>
                        </div>
                        <input type="hidden" name="from_lng" id="from_lng" value="" />
                        <input type="hidden" name="from_lat" id="from_lat" value="" />
                        <input type="hidden" name="to_lng" id="to_lng" value="" />
                        <input type="hidden" name="to_lat" id="to_lat" value="" />
                        <div class="row justify-content-start">
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
