@extends('layouts/layoutMaster')

@section('title', 'Order')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/leaflet/leaflet.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/leaflet/leaflet.js'])
@endsection

@section('page-style')
    <style>
        .qr-code {
            max-width: 200px;
            margin: 10px;
        }

        .rating-color {
            color: #fbc634 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $('[class^="status_select2_"]').select2();
            $('.print-button').click(function(event) {
                event.preventDefault();
                window.print();
            });
            $('[class^="status_select2_"]').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">

            <div class="d-flex flex-column justify-content-center gap-2 gap-sm-0">
                <h5 class="mb-1 mt-3 d-flex flex-wrap gap-2 align-items-end">#{{ $task->id }}</h5>
                <p class="text-body">{{ Carbon\Carbon::parse($task->created_at)->diffForHumans() }}</p>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-2">
                <p class="text-body"><a target="_blank" class="btn btn-primary print-button" title="{{ _t('Print') }}"
                        href="#"><i class="fa-solid fa-print"></i></a></p>
                @if ($task->getFirstMedia('file'))
                    <p class="text-body">
                        <a href="{{ $task->getFirstMediaUrl('file') }}" download class="btn btn-success"><i
                                title="{{ _t('File') }}" class="fa-solid fa-file"></i></a>
                    </p>
                @endif
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col">
                            <h5 class="card-title m-0">{{ _t('Task Details') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="{{ $bill->supplier->getFirstMedia('profile') ? $order->supplier->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png') }}"
                                    alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('supplier.edit', $bill->supplier_id) }}" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ $bill->supplier->name }}</h6>
                                </a>
                                <small class="text-muted">{{ _t('Supplier ID') }}: #{{ $bill->supplier_id }}</small>
                            </div>
                        </div> --}}
                        <p class=" mb-1">{{ _t('Title') }}: {{ $task->title }}</p>
                        <p class=" mb-1">{{ _t('Description') }}: {{ $task->description }}</p>
                    </div>
                </div>
                @if ($task->user_id == auth()->id())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title m-0">{{ _t('Task Employees') }}</h6>
                        </div>
                        <div class="card-body">
                            @foreach ($task->employeeTasks as $index => $employee_task)
                                <form method="POST" id="status-form-{{ $index }}"
                                    action="{{ route('employee-task.update', $employee_task->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="employee" class="form-label">{{ _t('Employee') }}:</label>
                                                <select id="employee-{{ $index }}" class="form-select" disabled>
                                                    <option value="">{{ $employee_task->employee->user->name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">{{ _t('Status') }}:</label>
                                                <select id="status-input-{{ $index }}" name="status"
                                                    class="status_select2_{{ $index }} form-select">
                                                    @foreach (App\Enums\TaskStatusEnum::cases() as $status)
                                                        <option value="{{ $status->value }}"
                                                            {{ $employee_task->status == $status ? 'selected' : '' }}>
                                                            {{ _t($status->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @else
                    @php
                        $employee_task = $task->employeeTasks->where('employee_id', auth()->user()->type_id)->first();
                    @endphp
                    @if ($employee_task)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title m-0">{{ _t('Status') }}</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="status-form-{{ $employee_task->id }}"
                                    action="{{ route('employee-task.update', $employee_task->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <select id="status-input-{{ $employee_task->id }}" name="status"
                                                    class="status_select2_{{ $employee_task->id }} form-select">
                                                    @foreach (App\Enums\TaskStatusEnum::cases() as $status)
                                                        <option value="{{ $status->value }}"
                                                            {{ $employee_task->status == $status ? 'selected' : '' }}>
                                                            {{ _t($status->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{ _t('Ticket Owner') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="{{ $task->user->getFirstMedia('profile') ? $task->user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png') }}"
                                    alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-0">{{ $task->user->name }}</h6>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>{{ _t('Contact info') }}</h6>
                        </div>
                        <p class=" mb-1">{{ _t('Phone') }}: {{ $task->user->phone }}</p>
                        <p class=" mb-1">{{ _t('Email') }}: {{ $task->user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endsection
