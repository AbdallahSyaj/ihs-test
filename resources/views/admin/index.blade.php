@extends('admin.layouts.app')

@section('content')
    <!-- Dashboard Main Content -->
    <div class="container-fluid px-4">
        <div class="row mb-2">
            <div class="col-md-6">
                <h1 class="mt-4 mb-2">{{ __('dashboard.shared.modules.dashboard') }}</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">{{ __('dashboard.dashboard_home.overview') }}</li>
                </ol>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center mt-4">
                <div class="time-display">
                    <div class="btn btn-secondary">
                        {{ $carbonGetNowTime ?? '---' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="stats-container">
            <!-- About-Us -->
            <a href="{{ route('super_admin.about_us-index') }}" class="stat-card stat-about">
                <div class="stat-info">
                    <h4>{{ __('dashboard.dashboard_home.visit') }}</h4>
                    <p>{{ __('dashboard.shared.modules.about_us') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-info-circle"></i>
                </div>
            </a>

            <!-- Contact-US -->
            <a href="{{ route('super_admin.contact_us-index') }}" class="stat-card stat-contact">
                <div class="stat-info">
                    <h4>{{ __('dashboard.dashboard_home.visit') }}</h4>
                    <p>{{ __('dashboard.shared.modules.contact_us') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-address-card"></i>
                </div>
            </a>

            <!-- Contact-US Request -->
            <a href="{{ route('super_admin.contact_us_requests-index') }}" class="stat-card stat-request">
                <div class="stat-info">
                    <h2>{{ $contactUsRequestsCount ?? 0 }}</h2>
                    <p>{{ __('dashboard.shared.modules.contact_us_requests') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-envelope"></i>
                </div>
            </a>

            <!-- Privacy & Policy -->
            <a href="{{ route('super_admin.privacy_policies-index') }}" class="stat-card stat-privacy">
                <div class="stat-info">
                    <h4>{{ __('dashboard.dashboard_home.visit') }}</h4>
                    <p>{{ __('dashboard.shared.modules.privacy_policy') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-shield-alt"></i>
                </div>
            </a>

            <!-- Terms & Conditions -->
            <a href="{{ route('super_admin.terms_and_conditions-index') }}" class="stat-card stat-terms">
                <div class="stat-info">
                    <h4>{{ __('dashboard.dashboard_home.visit') }}</h4>
                    <p>{{ __('dashboard.shared.modules.terms_and_conditions') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-file-alt"></i>
                </div>
            </a>

            <!-- Users -->
            <a href="{{ route('super_admin.users-index') }}" class="stat-card stat-users">
                <div class="stat-info">
                    <h2>{{ $usersCount ?? 0 }}</h2>
                    <p>{{ __('dashboard.shared.modules.users') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-user"></i>
                </div>
            </a>

            <!-- Admins -->
            <a href="{{ route('super_admin.admins-index') }}" class="stat-card stat-admins">
                <div class="stat-info">
                    <h2>{{ $adminsCount ?? 0 }}</h2>
                    <p>{{ __('dashboard.shared.modules.admins') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-user-circle"></i> <!-- Changed to a more basic icon -->
                </div>
            </a>

            <!-- Sliders -->
            <a href="{{ route('super_admin.sliders-index') }}" class="stat-card stat-sliders">
                <div class="stat-info">
                    <h2>{{ $slidersCount ?? 0 }}</h2>
                    <p>{{ __('dashboard.shared.modules.sliders') }}</p>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-images"></i>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('extra_js')
    <script>
        // Any additional JavaScript can go here
        $(document).ready(function() {
            console.log('Dashboard loaded successfully');
        });
    </script>
@endsection
