@extends('layouts/layoutMaster')

@section('title', 'Package')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-user-view.js', 'resources/assets/js/app-user-view-account.js'])
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('package.index') }}">{{ _t('Packages') }}</a> /
            {{ $package->id }} / {{ _t('View') }}</span>
    </h4>
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $package->name }}</h4>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                        <div class="d-flex align-items-start me-4 mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-checkbox ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">1.23k</p>
                                <small>Tasks Done</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-briefcase ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">568</p>
                                <small>Projects Done</small>
                            </div>
                        </div>
                    </div> --}}
                    <p class="mt-4 small text-uppercase text-muted">{{ _t('Details') }}</p>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-medium me-1">{{ _t('Start Date') }}:</span>
                                <span>{{ $package->start_date }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('End Date') }}:</span>
                                <span>{{ $package->end_date }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Created At') }}:</span>
                                <span>{{ $package->created_at }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Services') }}:</span>
                                <span>{{ $package->items()->hasMorph('item', [App\Models\Service::class])->count() }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Products') }}:</span>
                                <span>{{ $package->items()->hasMorph('item', [App\Models\Stock::class])->count() }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Orders') }}:</span>
                                <span>{{ $package->orders()->count() }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Total') }}:</span>
                                <span>{{ $package->total }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Description') }}:</span>
                                <span>{{ $package->description }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">{{ _t('Status') }}:</span>
                                <span
                                    class="badge bg-label-{{ $package->deleted_at ? 'danger' : 'success' }}">{{ $package->deleted_at ? _t('Archived') : _t('Active') }}</span>
                            </li>
                        </ul>
                        @can('update_package')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-start">
                                        <a href="{{ route('package.edit', $package->id) }}"
                                            class="btn btn-primary me-3">{{ _t('Edit') }}</a>
                                        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#roundVacationModal">
                                            {{ _t('Round Vacations') }}
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <!-- /User Card -->
            <!-- Plan Card -->
            {{-- <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="badge bg-label-primary">Standard</span>
                        <div class="d-flex justify-content-center">
                            <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary fw-normal">$</sup>
                            <h1 class="mb-0 text-primary">99</h1>
                            <sub class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                        </div>
                    </div>
                    <ul class="ps-3 g-2 my-3">
                        <li class="mb-2">10 Users</li>
                        <li class="mb-2">Up to 10 GB storage</li>
                        <li>Basic Support</li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mb-1 fw-medium text-heading">
                        <span>Days</span>
                        <span>65% Completed</span>
                    </div>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span>4 days remaining</span>
                    <div class="d-grid w-100 mt-4">
                        <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">Upgrade
                            Plan</button>
                    </div>
                </div>
            </div> --}}
            <!-- /Plan Card -->
        </div>
        <!--/ User Sidebar -->


        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Pills -->
            {{-- <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
                            class="ti ti-user-check ti-xs me-1"></i>Account</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/security') }}"><i
                            class="ti ti-lock ti-xs me-1"></i>Security</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/billing') }}"><i
                            class="ti ti-currency-dollar ti-xs me-1"></i>Billing & Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/notifications') }}"><i
                            class="ti ti-bell ti-xs me-1"></i>Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/connections') }}"><i
                            class="ti ti-link ti-xs me-1"></i>Connections</a></li>
            </ul> --}}
            <!--/ User Pills -->

            <!-- Project table -->
            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Services') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-stock-test table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Image') }}</th>
                                <th>{{ _t('Name') }}</th>
                                <th>{{ _t('Price') }}</th>
                                <th>{{ _t('Qty') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($package->items()->where('item_type', App\Models\Service::class)->get() as $item)
                                <tr>
                                    <td>{{ $item->id }} </td>
                                    <td>
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2"><img
                                                    src="{{ $item->item->getFirstMedia('image') ? $item->item->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png') }}"
                                                    alt="" class="rounded-2"></div>
                                        </div>
                                    </td>
                                    <td>{{ $item->item->name }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">{{ _t('Products') }}</h5>
                <div class="table-responsive mb-3">
                    <table class="datatables-withdrawal table dt-responsive">
                        <thead class="border-top">
                            <tr>
                                <!-- Filter -->
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>{{ _t('Image') }}</th>
                                <th>{{ _t('Name') }}</th>
                                <th>{{ _t('Price') }}</th>
                                <th>{{ _t('Quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($package->items()->where('item_type', App\Models\Stock::class)->get() as $item)
                                <tr>
                                    <td>{{ $item->id }} </td>
                                    <td>
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2"><img
                                                    src="{{ $item->item->product->getFirstMedia('image') ? $item->item->product->getFirstMediaUrl('image') : asset('assets/img/illustrations/default.png') }}"
                                                    alt="" class="rounded-2"></div>
                                        </div>
                                    </td>
                                    <td>{{ $item->item->product->name }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--/ User Content -->
    </div>

    <!-- Modal -->

    <!-- end modal -->
@endsection
