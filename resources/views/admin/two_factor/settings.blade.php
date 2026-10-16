@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.two_factor.settings_title') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.two_factor.settings_title') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <p>
                            <strong>{{ __('dashboard.two_factor.status_label') }}:</strong>
                            {{ $confirmed ? __('dashboard.two_factor.status_enabled') : __('dashboard.two_factor.status_disabled') }}
                        </p>

                        @if (! $confirmed)
                            <p>{{ __('dashboard.two_factor.next_login_enrolls') }}</p>
                        @else
                            <hr>
                            <h4>{{ __('dashboard.two_factor.regenerate_title') }}</h4>
                            <p>{{ __('dashboard.two_factor.regenerate_help') }}</p>
                            <form method="POST" action="{{ route('super_admin.twoFactor-regenerateRecoveryCodes') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="regenerate_password" class="form-label">{{ __('dashboard.shared.password') }}</label>
                                    <input id="regenerate_password" type="password" name="current_password" class="form-control" required autocomplete="current-password">
                                </div>
                                @error('current_password')
                                    <div class="text-danger mb-3">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-secondary">{{ __('dashboard.two_factor.regenerate_button') }}</button>
                            </form>

                            <hr>
                            <h4>{{ __('dashboard.two_factor.disable_title') }}</h4>
                            <p>{{ __('dashboard.two_factor.disable_help') }}</p>
                            <form method="POST" action="{{ route('super_admin.twoFactor-disable') }}"
                                onsubmit="return confirm(@json(__('dashboard.two_factor.disable_confirm')));">
                                @csrf
                                <div class="mb-3">
                                    <label for="disable_password" class="form-label">{{ __('dashboard.shared.password') }}</label>
                                    <input id="disable_password" type="password" name="current_password" class="form-control" required autocomplete="current-password">
                                </div>
                                @error('current_password')
                                    <div class="text-danger mb-3">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.two_factor.disable_button') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
