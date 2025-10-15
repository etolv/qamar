@extends('layouts/layoutMaster')

@section('title', 'Media')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection
@section('page-style')
    @ensection
@section('page-script')
    <script>
        $(document).ready(function() {});
    </script>
@endsection

@section('content')
    <div class="row">
        <!-- Basic with Icons -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ _t('Media') }}</h5> <small
                        class="text-muted float-end">{{ _t('Info') }}</small>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
                        @foreach ($mediaItems as $media)
                            <div class="col">
                                <div class="card h-100">
                                    @if (str_starts_with($media->mime_type, 'image'))
                                        <img loading="lazy" src="{{ $media->getUrl() }}" alt="{{ $media->name }}"
                                            class="card-img-top">
                                    @else
                                        <div class="p-4 text-center">
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="text-blue-500 underline">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                    viewBox="0 0 512 512">
                                                    <path
                                                        d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $media->name }} {{ $media->size / 1024 }}</h5>
                                        <small class="card-text">{{ $media->mime_type }}
                                            ({{ round($media->size / 1024 / 1024, 2) }}
                                            MB)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $mediaItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
