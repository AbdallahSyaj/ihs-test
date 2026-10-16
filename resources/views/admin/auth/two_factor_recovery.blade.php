@extends('admin.auth.two_factor_layout')

@section('content')
    <h2 class="form-title">{{ __('dashboard.two_factor.recovery_title') }}</h2>
    <p class="form-subtitle">{{ __('dashboard.two_factor.recovery_once') }}</p>

    <ul class="recovery-list">
        @foreach ($codes as $code)
            <li>{{ $code }}</li>
        @endforeach
    </ul>

    <a href="{{ route('super_admin.dashboard') }}">
        <button type="button" class="login-button">{{ __('dashboard.two_factor.continue_button') }}</button>
    </a>
@endsection
