@extends('layouts/layoutMaster')

@section('title', 'Product List')

@section('vendor-style')

    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-style')
    <style>
        .slider-admin-card {
            padding: 20px;
            height: calc(100% - 20px);
            margin-block: 20px;
        }

        .slider-admin-card img {
            max-height: 300px;
            height: 300px;
            display: -ms-flexbox;
            display: flex;
            margin-inline: auto;
            margin-block: 0px 20px;
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            window.deleteOptions('Slider');
        });
    </script>
@endsection
@section('content')
    <!-- <h6 class="pb-1 mb-4 text-muted">{{ _t('Sliders') }}</h6> -->
    <div class="card mb-2">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">{{ _t('Sliders') }}</h5>
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                <div class="col-md-4">
                    <a href="{{ route('slider.create') }}" class="btn btn-primary mb-3"
                        id="newUserButton">{{ _t('New Slider') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        @foreach ($sliders as $slider)
            <div class="col">
                <div class="card slider-admin-card">
                    <img class="card-img-top" src="{{ $slider->getFirstMediaUrl('image') }}" alt="Card image" />
                    <div class="card-body">
                        <h5 class="card-title"><a href="{{ $slider->url ?? '#' }}" target="_blank">{{ $slider->title }}</a>
                        </h5>
                        <p class="card-text">
                            {{ $slider->description }}
                        </p>
                        <p class="card-text">
                            <a href="{{ route('slider.edit', $slider->id) }}" class="text-body"><i
                                    class="ti ti-edit ti-sm me-2"></i></a>
                            <a href="javascript:;" class="text-body item-delete" data-with-trashed="1"
                                data-object-id="{{ $slider->id }}"><i class="ti ti-trash ti-sm mx-2"></i></a>
                            <label class="switch switch-primary">
                                <input type="checkbox" title="Activate" class="soft-delete activate-object switch-input"
                                    data-object-id="{{ $slider->id }}"
                                    {{ $slider->deleted_at != null ? '' : 'checked' }} />
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">
                                        <i class="ti ti-check"></i>
                                    </span>
                                    <span class="switch-off">
                                        <i class="ti ti-x"></i>
                                    </span>
                                </span>
                            </label>
                        </p>
                        <p class="card-text">
                        </p>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
