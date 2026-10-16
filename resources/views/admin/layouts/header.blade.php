<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-lg navbar-dark">
        {{-- ============================================================== --}}
        {{-- ====================== Header Menu Section =================== --}}
        {{-- ============================================================== --}}
        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            {{-- logo --}}
            <a class="navbar-brand" href="{{ route('super_admin.dashboard') }}">
                <b class="logo-icon">
                    <!-- Light Logo icon -->
                    <img src="{{ asset('style_files/shared/images_default/blueray_logo.jpg') }}" alt="BlueRay Logo"
                        class="rounded-circle logo-image" width="60" height="60" />

                        <span class="logo-text">Admin Dashboard</span>
                </b>
            </a>

            {{-- ============================================================== --}}
            {{-- ====================== Left Side Toggle ====================== --}}
            {{-- ============================================================== --}}
            <ul class="navbar-nav me-auto">
                {{-- Toggle Visible on Mobile Only --}}
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
                        data-sidebartype="mini-sidebar">
                        <i class="icon-arrow-left-circle"></i>
                    </a>
                </li>
            </ul>

            {{-- ============================================================== --}}
            {{-- ====================== Right Side Toggle ===================== --}}
            {{-- ============================================================== --}}
            <ul class="navbar-nav">
                {{-- Language Switcher --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark d-flex align-items-center" href="#"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-web"></i>
                        <span class="ms-1">{{ strtoupper(app()->getLocale()) }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end animated flipInY">
                        @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a class="dropdown-item {{ app()->getLocale() === $localeCode ? 'active' : '' }}"
                                href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($localeCode) }}">
                                <i class="feather-sm text-primary me-2" data-feather="globe"></i>
                                {{ $properties['native'] }}
                            </a>
                        @endforeach
                    </div>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark d-flex align-items-center" href="#"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset(auth()->guard('super_admin')->user()->personal_image && file_exists(auth()->guard('super_admin')->user()->personal_image) ? auth()->guard('super_admin')->user()->personal_image : 'style_files/shared/images_default/blueray_logo.jpg') }}"
                            alt="user" width="30" height="30" class="profile-pic rounded-circle">
                        <span class="ms-2">
                            <strong>{{ auth()->guard('super_admin')->user()->name ?? __('dashboard.shared.undefined') }}</strong>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-dd animated flipInY">
                        <div class="d-flex align-items-center p-3 bg-info text-white rounded-top">
                            <div class="me-3">
                                <img src="{{ asset(auth()->guard('super_admin')->user()->personal_image && file_exists(auth()->guard('super_admin')->user()->personal_image) ? auth()->guard('super_admin')->user()->personal_image : 'style_files/shared/images_default/blueray_logo.jpg') }}"
                                    class="rounded-circle border border-white shadow" width="60" height="60"
                                    alt="Profile Image">
                            </div>

                            <div>
                                <h5 class="mb-0 text-white">
                                    {{ auth()->guard('super_admin')->user()->name ?? __('dashboard.shared.undefined') }}
                                </h5>
                                <small
                                    class="text-white-50">{{ auth()->guard('super_admin')->user()->email ?? __('dashboard.shared.undefined') }}</small>
                            </div>

                        </div>
                        <div class="p-3">
                            <a class="dropdown-item" href="{{ route('super_admin.support_tickets-index') }}">
                                <i class="feather-sm text-primary me-2" data-feather="clipboard"></i>
                                {{ __('dashboard.shared.modules.support_tickets') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('super_admin.twoFactor-settings') }}">
                                <i class="feather-sm text-primary me-2" data-feather="shield"></i>
                                {{ __('dashboard.two_factor.menu_label') }}
                            </a>

                            <a class="dropdown-item text-danger" href="{{ route('super_admin.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="feather-sm text-danger me-2" data-feather="log-out"></i> {{ __('dashboard.shared.logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('super_admin.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
