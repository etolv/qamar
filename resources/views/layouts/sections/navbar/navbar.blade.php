@php
    $containerNav =
        isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
            ? 'container-xxl'
            : 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
        id="layout-navbar">
@endif
@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="{{ $containerNav }}">
@endif

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['height' => 20])
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="ti ti-x ti-sm align-middle"></i>
        </a>
    </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    @if (!isset($menuHorizontal))
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->
    @endif
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Language -->
        <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <i class='ti ti-language rounded-circle ti-md'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                        href="{{ url('lang/en') }}" data-language="en" data-text-direction="ltr">
                        <span class="align-middle">English</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                        href="{{ url('lang/ar') }}" data-language="ar" data-text-direction="rtl">
                        <span class="align-middle">Arabic</span>
                    </a>
                </li>
            </ul>
        </li>
        <!--/ Language -->

        @if (isset($menuHorizontal))
            <!-- Search -->
            <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
                <a class="nav-link search-toggler" href="javascript:void(0);">
                    <i class="ti ti-search ti-md"></i>
                </a>
            </li>
            <!-- /Search -->
        @endif
        @if ($configData['hasCustomizer'] == true)
            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class='ti ti-md'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                        </a>
                    </li>
                    <!-- <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                  <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                </a>
              </li> -->
                </ul>
            </li>
            <!--/ Style Switcher -->
        @endif

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                data-bs-auto-close="outside" aria-expanded="false">
                <i class="ti ti-bell ti-md"></i>
                <span class="badge bg-danger rounded-pill badge-notifications"
                    id="numberAdminNotification">{{ isset($user_notifications) ? $user_notifications->count() : 0 }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                        <h5 class="text-body mb-0 me-auto">{{ _t('Notifications') }}</h5>
                        <a href="javascript:void(0)" class="dropdown-notifications-all text-body"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i
                                class="ti ti-mail-opened fs-4"></i></a>
                    </div>
                </li>
                <li class="dropdown-notifications-list scrollable-container">
                    <ul class="list-group list-group-flush">
                        @isset($user_notifications)
                            @foreach ($user_notifications as $notification)
                                @include('components.notification_item', [
                                    'notification' => $notification,
                                ])
                            @endforeach
                        @endisset
                    </ul>
                </li>
                <li class="dropdown-menu-footer border-top">
                    <a href="javascript:void(0);"
                        class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                        {{ _t('View all notifications') }}
                    </a>
                </li>
            </ul>
        </li>
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::check() ? ($authenticated_user?->getFirstMedia('profile') ? $authenticated_user?->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png')) : '' }}"
                        alt class="h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::check() ? ($authenticated_user?->getFirstMedia('profile') ? $authenticated_user?->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png')) : '' }}"
                                        alt class="h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-medium d-block">
                                    @if (Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        John Doe
                                    @endif
                                </span>
                                <small class="text-muted">{{ _t('Admin') }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                @if (Auth::user()->type_type == 'App\Models\Employee')
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ Route::has('profile.dashboard') ? route('profile.dashboard') : '#' }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ _t('My Profile') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                @if (Auth::check())
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class='ti ti-logout me-2'></i>
                            <span class="align-middle">Logout</span>
                        </a>
                    </li>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                        @csrf
                    </form>
                @else
                    <li>
                        <a class="dropdown-item"
                            href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                            <i class='ti ti-login me-2'></i>
                            <span class="align-middle">Login</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>

<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
    <input type="text"
        class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0"
        placeholder="Search..." aria-label="Search...">
    <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
</div>
@if (isset($navbarDetached) && $navbarDetached == '')
    </div>
@endif
</nav>
<!-- / Navbar -->
