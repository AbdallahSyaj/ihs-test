@extends('admin.auth.two_factor_layout')

@section('content')
    <h2 class="form-title">{{ __('dashboard.two_factor.challenge_title') }}</h2>
    <p class="form-subtitle">{{ __('dashboard.two_factor.challenge_subtitle') }}</p>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('super_admin.twoFactor-verify') }}">
        @csrf
        <div class="form-group">
            <label for="code">{{ __('dashboard.two_factor.code_label') }}</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                maxlength="6" pattern="[0-9]{6}" class="form-control"
                placeholder="{{ __('dashboard.two_factor.code_placeholder') }}">
        </div>
        <button type="submit" class="login-button">{{ __('dashboard.two_factor.verify_button') }}</button>
    </form>

    <form method="POST" action="{{ route('super_admin.twoFactor-verify') }}">
        @csrf
        <div class="form-group" style="margin-top: 28px;">
            <label for="recovery_code">{{ __('dashboard.two_factor.recovery_label') }}</label>
            <input id="recovery_code" name="recovery_code" type="text" autocomplete="off" class="form-control"
                placeholder="{{ __('dashboard.two_factor.recovery_placeholder') }}">
        </div>
        <button type="submit" class="secondary-button">{{ __('dashboard.two_factor.recovery_button') }}</button>
    </form>
@endsection
