<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Admin Portal</title>
    <link href="{{ asset('style_files/backend/src/assets/libs/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #4895ef;
            --accent-color: #4cc9f0;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --gray-color: #adb5bd;
            --bg-color: #f5f9ff;
            --card-bg: #ffffff;

            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(67, 97, 238, 0.1);

            --border-radius-sm: 6px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;

            --transition-fast: all 0.2s ease;
            --transition-normal: all 0.3s ease;
            --transition-slow: all 0.5s ease;

            --font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            background-color: var(--bg-color);
            background-image:
                radial-gradient(at 30% 20%, rgba(67, 97, 238, 0.03) 0px, transparent 50%),
                radial-gradient(at 80% 80%, rgba(76, 201, 240, 0.03) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 900px;
            display: flex;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            background-color: var(--card-bg);
            animation: fadeIn 0.8s ease;
        }

        .login-sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 50px 40px;
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 1;
            z-index: 0;
        }

        .sidebar-content {
            position: relative;
            z-index: 1;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .logo-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 12px;
            background-color: white;
            padding: 5px;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-text {
            font-weight: 600;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        .sidebar-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .sidebar-subtitle {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .feature-icon {
            margin-right: 12px;
            background-color: rgba(255, 255, 255, 0.2);
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 12px;
        }

        .sidebar-footer {
            font-size: 14px;
            opacity: 0.8;
        }

        .login-form-container {
            padding: 50px 40px;
            width: 55%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            margin-bottom: 40px;
            animation: slideUp 0.5s ease;
            animation-fill-mode: both;
        }

        .form-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 15px;
            color: var(--gray-color);
            font-weight: 400;
        }

        .login-form {
            animation: slideUp 0.5s ease 0.1s;
            animation-fill-mode: both;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
            transition: var(--transition-fast);
        }

        .form-control {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1.5px solid #e9ecef;
            border-radius: var(--border-radius-md);
            font-size: 15px;
            transition: var(--transition-fast);
            color: var(--dark-color);
            background-color: #fff;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .form-control:focus+.input-icon {
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
            cursor: pointer;
            transition: var(--transition-fast);
            background: none;
            border: none;
            padding: 0;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: var(--dark-color);
        }

        .remember-forgot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }

        .custom-checkbox input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            width: 18px;
            height: 18px;
            border: 1.5px solid #d1d9e6;
            border-radius: 4px;
            transition: var(--transition-fast);
        }

        .custom-checkbox input:checked~.checkmark {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 4px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox input:checked~.checkmark:after {
            display: block;
        }

        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition-fast);
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius-md);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .login-button:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
            transform: translateY(-2px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .separator {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--gray-color);
            font-size: 14px;
        }

        .separator-line {
            flex: 1;
            height: 1px;
            background-color: #e9ecef;
        }

        .separator-text {
            padding: 0 15px;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f8f9fa;
            border: 1.5px solid #e9ecef;
            color: var(--dark-color);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .social-button:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .social-button i {
            font-size: 20px;
        }

        .auth-footer {
            text-align: center;
            color: var(--gray-color);
            font-size: 14px;
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-danger {
            background-color: rgba(239, 71, 111, 0.08);
            border-radius: var(--border-radius-md);
            padding: 16px 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--danger-color);
            animation: slideUp 0.5s ease;
            animation-fill-mode: both;
        }

        .alert-danger h3 {
            color: var(--danger-color);
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger hr {
            border: none;
            border-top: 1px solid rgba(239, 71, 111, 0.2);
            margin: 12px 0;
        }

        .alert-danger ul {
            padding-left: 20px;
            color: #92364a;
        }

        .alert-danger li {
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 550px;
            }

            .login-sidebar,
            .login-form-container {
                width: 100%;
            }

            .login-sidebar {
                padding: 40px 30px;
                text-align: center;
            }

            .sidebar-logo {
                justify-content: center;
                margin-bottom: 30px;
            }

            .feature-list {
                display: inline-block;
                text-align: left;
            }
        }

        @media (max-width: 576px) {
            .login-form-container {
                padding: 40px 25px;
            }

            .form-title {
                font-size: 22px;
            }

            .social-login {
                gap: 10px;
            }

            .social-button {
                width: 45px;
                height: 45px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Sidebar Section -->
        <div class="login-sidebar">
            <div class="sidebar-content">
                <div class="sidebar-logo">
                    <img src="{{ asset('style_files/logo/blueray_logo.jpg') }}" alt="Logo" class="logo-image">
                    <div class="logo-text">Admin Dashboard</div>
                </div>

                <h1 class="sidebar-title">{{ __('dashboard.auth.welcome_title') }}</h1>
                <p class="sidebar-subtitle">{{ __('dashboard.auth.welcome_subtitle') }}</p>

                <ul class="feature-list">
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <span>{{ __('dashboard.auth.feature_secure_access') }}</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <span>{{ __('dashboard.auth.feature_analytics') }}</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-file-invoice"></i></div>
                        <span>{{ __('dashboard.auth.feature_checks') }}</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <span>{{ __('dashboard.auth.feature_user_admin') }}</span>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                {{ __('dashboard.auth.copyright') }}
            </div>
        </div>

        <!-- Form Section -->
        <div class="login-form-container">
            <div class="form-header">
                <h2 class="form-title">{{ __('dashboard.auth.sign_in_title') }}</h2>
                <p class="form-subtitle">{{ __('dashboard.auth.sign_in_subtitle') }}</p>
            </div>

            <form method="POST" action="{{ route('super_admin.loginFormSubmit') }}" class="login-form">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h3><i class="fas fa-exclamation-circle"></i> {{ __('dashboard.auth.correct_errors') }}</h3>
                        <hr>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- email --}}
                <div class="form-group">
                    <label for="email">{{ __('dashboard.shared.email') }}</label>
                    <div class="input-group">
                        <input type="email" id="email" class="form-control" name="email"
                            value="{{ old('email') }}" placeholder="{{ __('dashboard.auth.email_placeholder') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">{{ __('dashboard.shared.password') }}</label>
                    <div class="input-group">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="{{ __('dashboard.auth.password_placeholder') }}" required>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Password --}}
                <div class="remember-forgot">
                    <label class="checkbox-container">
                        <span class="custom-checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                        </span>
                        {{ __('dashboard.auth.remember_me') }}
                    </label>

                    <a href="#" class="forgot-link">{{ __('dashboard.auth.forgot_password') }}</a>
                </div>

                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> {{ __('dashboard.auth.login_button') }}
                </button>

                <div class="auth-footer">
                    <p>{{ __('dashboard.auth.need_help') }} <a href="#">{{ __('dashboard.auth.contact_support') }}</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Change the icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Form validation example (can be expanded as needed)
            const loginForm = document.querySelector('.login-form');
            loginForm.addEventListener('submit', function(event) {
                const email = document.querySelector('#email').value;
                const password = document.querySelector('#password').value;

                let valid = true;

                // Basic validation example - can be enhanced as needed
                if (!email || !email.includes('@')) {
                    valid = false;
                }

                if (!password || password.length < 6) {
                    valid = false;
                }

                if (!valid) {
                    event.preventDefault();
                    // Here you could add custom validation messages
                }
            });
        });
    </script>
</body>

</html>
