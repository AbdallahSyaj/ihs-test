<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('dashboard.two_factor.page_title') }}</title>
    <link href="{{ asset('style_files/backend/src/assets/libs/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --danger-color: #ef476f;
            --dark-color: #2b2d42;
            --gray-color: #adb5bd;
            --bg-color: #f5f9ff;
            --card-bg: #ffffff;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: var(--bg-color);
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 560px;
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.1);
            padding: 40px 32px;
        }

        .form-title { margin: 0 0 8px; color: var(--dark-color); font-size: 26px; }
        .form-subtitle { margin: 0 0 24px; color: var(--gray-color); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark-color); }
        .form-control {
            width: 100%;
            padding: 14px;
            border: 1.5px solid #e9ecef;
            border-radius: var(--border-radius-md);
            font-size: 15px;
        }
        .form-control:focus { outline: none; border-color: var(--primary-color); }
        .login-button, .secondary-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--border-radius-md);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .login-button { background: var(--primary-color); color: #fff; }
        .login-button:hover { background: var(--primary-dark); }
        .secondary-button { margin-top: 12px; background: #f8f9fa; color: var(--dark-color); border: 1.5px solid #e9ecef; }
        .alert-danger {
            background: rgba(239, 71, 111, 0.08);
            border-radius: var(--border-radius-md);
            padding: 16px;
            margin-bottom: 20px;
            border-left: 4px solid var(--danger-color);
            color: #92364a;
        }
        html[dir="rtl"] .alert-danger { border-left: none; border-right: 4px solid var(--danger-color); }
        .qr-frame { display: flex; justify-content: center; margin: 16px 0; }
        .qr-frame svg { width: 220px; height: 220px; }
        .manual-key {
            font-family: Consolas, monospace;
            letter-spacing: 1px;
            word-break: break-word;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }
        .recovery-list { padding-inline-start: 20px; }
        .recovery-list li { font-family: Consolas, monospace; margin-bottom: 8px; }
    </style>
</head>

<body>
    <div class="login-container">
        @yield('content')
    </div>
</body>

</html>
