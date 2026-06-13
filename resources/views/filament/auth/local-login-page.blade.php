<x-filament-panels::page.simple>
    <style>
        .bb-login-shell {
            margin-inline: auto;
            width: min(100%, 390px);
        }

        .bb-login-card {
            border: 1px solid rgb(255 255 255 / 10%);
            border-radius: 20px;
            background: #111113;
            box-shadow: 0 24px 80px rgb(0 0 0 / 28%);
            color: #ffffff;
            padding: 24px;
        }

        .bb-login-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .bb-logo-box {
            align-items: center;
            background: #ffffff;
            border-radius: 14px;
            display: inline-flex;
            height: 42px;
            justify-content: center;
            margin-bottom: 14px;
            width: 42px;
        }

        .bb-logo-box img {
            display: block;
            max-height: 28px;
            max-width: 30px;
            object-fit: contain;
        }

        .bb-login-badge {
            color: rgb(251 191 36);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            margin: 0;
            text-transform: uppercase;
        }

        .bb-login-title {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.25;
            margin: 8px 0 0;
        }

        .bb-login-note {
            color: #a1a1aa;
            font-size: 13px;
            line-height: 1.45;
            margin: 8px 0 0;
        }

        .bb-login-form {
            display: grid;
            gap: 13px;
        }

        .bb-field-label {
            color: #ffffff;
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .bb-field-input {
            background: rgb(255 255 255 / 5%);
            border: 1px solid rgb(255 255 255 / 12%);
            border-radius: 12px;
            color: #ffffff;
            display: block;
            font-size: 14px;
            outline: none;
            padding: 9px 12px;
            transition: border-color .16s ease, box-shadow .16s ease;
            width: 100%;
        }

        .bb-field-input:focus {
            border-color: rgb(251 191 36);
            box-shadow: 0 0 0 3px rgb(251 191 36 / 14%);
        }

        .bb-login-remember {
            align-items: center;
            color: #d4d4d8;
            display: flex;
            font-size: 14px;
            gap: 10px;
        }

        .bb-login-remember input {
            height: 16px;
            width: 16px;
        }

        .bb-login-submit {
            align-items: center;
            background: rgb(217 119 6);
            border: 0;
            border-radius: 12px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            font-weight: 700;
            justify-content: center;
            padding: 10px 16px;
            transition: background .16s ease;
            width: 100%;
        }

        .bb-login-submit:hover {
            background: rgb(245 158 11);
        }

        .bb-login-error,
        .bb-demo-box {
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.6;
            margin-top: 14px;
            padding: 10px 12px;
        }

        .bb-login-error {
            background: rgb(239 68 68 / 10%);
            border: 1px solid rgb(239 68 68 / 28%);
            color: #fecaca;
            margin-bottom: 18px;
            margin-top: 0;
        }

        .bb-demo-box {
            background: rgb(255 255 255 / 5%);
            border: 1px solid rgb(255 255 255 / 10%);
            color: #a1a1aa;
        }

        .bb-demo-box strong,
        .bb-demo-box span {
            color: #ffffff;
            font-weight: 600;
        }
    </style>

    <div class="bb-login-shell">
        <div class="bb-login-card">
            <div class="bb-login-header">
                <div class="bb-logo-box">
                    <img
                        src="{{ filament()->getBrandLogo() ?? asset('images/logo.png') }}"
                        alt="{{ config('app.name') }} logo"
                    >
                </div>

                <p class="bb-login-badge">{{ __('auth.login.badge') }}</p>
                <h1 class="bb-login-title">{{ __('auth.login.title') }}</h1>
                <p class="bb-login-note">{{ __('auth.login.side_note') }}</p>
            </div>

            @if (session('plain_login_error'))
                <div class="bb-login-error">
                    {{ session('plain_login_error') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/admin/login') }}" class="bb-login-form">
                @csrf

                <div>
                    <label for="local-email" class="bb-field-label">{{ __('auth.login.email') }}</label>
                    <input
                        id="local-email"
                        type="email"
                        name="email"
                        value="{{ old('email', '') }}"
                        required
                        autofocus
                        class="bb-field-input"
                    >
                </div>

                <div>
                    <label for="local-password" class="bb-field-label">{{ __('auth.login.password') }}</label>
                    <input
                        id="local-password"
                        type="password"
                        name="password"
                        required
                        class="bb-field-input"
                    >
                </div>

                <label class="bb-login-remember">
                    <input type="checkbox" name="remember" value="1">
                    <span>{{ __('auth.login.remember') }}</span>
                </label>

                <button type="submit" class="bb-login-submit">
                    {{ __('auth.login.submit') }}
                </button>
            </form>

            @if (app()->isLocal())
                <div class="bb-demo-box">
                    <strong>{{ __('auth.login.demo_accounts') }}:</strong>
                    <span>admin@test.com</span>,
                    <span>manager@test.com</span>,
                    <span>worker@test.com</span>
                    <br>
                    <strong>{{ __('auth.login.demo_password') }}:</strong>
                    <span>password</span>
                </div>
            @endif
        </div>

        @php
            $languages = [
                ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
                ['code' => 'ka', 'name' => 'ქართული', 'flag' => 'ge'],
            ];

            $currentLanguage = collect($languages)->firstWhere('code', app()->getLocale()) ?? $languages[0];
        @endphp

        @include('filament-language-switcher::language-switcher', [
            'otherLanguages' => $languages,
            'currentLanguage' => $currentLanguage,
            'showFlags' => true,
            'floating' => true,
        ])
    </div>
</x-filament-panels::page.simple>
