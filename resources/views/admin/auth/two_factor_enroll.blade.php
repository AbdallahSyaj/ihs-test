@extends('admin.auth.two_factor_layout')

@section('content')
    <h2 class="form-title">{{ __('dashboard.two_factor.enroll_title') }}</h2>
    <p class="form-subtitle">{{ __('dashboard.two_factor.enroll_subtitle') }}</p>
    <p>{{ __('dashboard.two_factor.enroll_account', ['email' => $accountEmail]) }}</p>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="qr-frame">
        {!! $qrCodeSvg !!}
    </div>

    <div class="form-group">
        <label>{{ __('dashboard.two_factor.manual_key_label') }}</label>
        <div class="manual-key">{{ $manualKey }}</div>
    </div>

    <form method="POST" action="{{ route('super_admin.twoFactor-enrollConfirm') }}">
        @csrf
        <div class="form-group">
            <label for="code">{{ __('dashboard.two_factor.enroll_code_label') }}</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                maxlength="6" pattern="[0-9]{6}" class="form-control" required
                placeholder="{{ __('dashboard.two_factor.code_placeholder') }}">
        </div>
        <button type="submit" class="login-button">{{ __('dashboard.two_factor.enroll_button') }}</button>
    </form>
@endsection
