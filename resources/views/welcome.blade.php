<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BuildBoard') }}</title>
        <style>
            :root {
                color-scheme: dark;
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                --bg: #070809;
                --panel: #111316;
                --panel-soft: #181b20;
                --line: #272b33;
                --text: #f7f7f7;
                --muted: #a7adb7;
                --accent: #f59e0b;
                --accent-strong: #d97706;
                --green: #22c55e;
                --red: #ef4444;
            }

            * {
                box-sizing: border-box;
            }

            body {
                background: var(--bg);
                color: var(--text);
                margin: 0;
                min-height: 100vh;
            }

            .page {
                display: grid;
                grid-template-rows: auto 1fr;
                min-height: 100vh;
            }

            .nav,
            .hero,
            .workflow {
                margin-inline: auto;
                max-width: 1180px;
                width: min(100% - 32px, 1180px);
            }

            .nav {
                align-items: center;
                display: flex;
                justify-content: space-between;
                padding: 22px 0;
            }

            .brand {
                align-items: center;
                display: inline-flex;
                gap: 12px;
                font-size: 18px;
                font-weight: 800;
            }

            .brand img {
                background: #ffffff;
                border-radius: 12px;
                height: 38px;
                object-fit: contain;
                padding: 6px;
                width: 38px;
            }

            .nav a,
            .hero-actions a {
                align-items: center;
                border-radius: 12px;
                display: inline-flex;
                font-weight: 700;
                justify-content: center;
                min-height: 42px;
                padding: 0 18px;
                text-decoration: none;
            }

            .nav a {
                border: 1px solid var(--line);
                color: var(--text);
            }

            .hero {
                align-items: center;
                display: grid;
                gap: 44px;
                grid-template-columns: minmax(0, .95fr) minmax(420px, 1.05fr);
                padding: 56px 0 36px;
            }

            .eyebrow {
                color: var(--accent);
                font-size: 13px;
                font-weight: 800;
                margin: 0 0 12px;
                text-transform: uppercase;
            }

            h1 {
                font-size: clamp(42px, 7vw, 72px);
                letter-spacing: 0;
                line-height: .98;
                margin: 0;
                max-width: 720px;
            }

            .lead {
                color: var(--muted);
                font-size: 18px;
                line-height: 1.65;
                margin: 24px 0 0;
                max-width: 620px;
            }

            .hero-actions {
                align-items: center;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 30px;
            }

            .primary {
                background: var(--accent-strong);
                color: #ffffff;
            }

            .secondary {
                border: 1px solid var(--line);
                color: var(--text);
            }

            .metrics {
                display: grid;
                gap: 12px;
                grid-template-columns: repeat(3, 1fr);
                margin-top: 34px;
                max-width: 620px;
            }

            .metric {
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: 14px;
            }

            .metric strong {
                display: block;
                font-size: 24px;
            }

            .metric span {
                color: var(--muted);
                display: block;
                font-size: 13px;
                margin-top: 4px;
            }

            .product {
                border: 1px solid var(--line);
                border-radius: 8px;
                background: #0d0f12;
                box-shadow: 0 28px 90px rgb(0 0 0 / 40%);
                overflow: hidden;
            }

            .product-top {
                align-items: center;
                border-bottom: 1px solid var(--line);
                display: flex;
                justify-content: space-between;
                padding: 14px 16px;
            }

            .product-top span {
                color: var(--muted);
                font-size: 13px;
            }

            .product-body {
                display: grid;
                gap: 16px;
                padding: 18px;
            }

            .stat-grid {
                display: grid;
                gap: 12px;
                grid-template-columns: repeat(3, 1fr);
            }

            .product-card,
            .table-preview {
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: 14px;
            }

            .product-card span,
            .table-preview span {
                color: var(--muted);
                display: block;
                font-size: 12px;
            }

            .product-card strong {
                display: block;
                font-size: 22px;
                margin-top: 8px;
            }

            .table-row {
                align-items: center;
                border-top: 1px solid var(--line);
                display: grid;
                gap: 12px;
                grid-template-columns: 1.4fr .8fr .8fr .7fr;
                padding: 12px 0;
            }

            .table-row:first-of-type {
                border-top: 0;
                margin-top: 10px;
            }

            .badge {
                border-radius: 999px;
                color: #ffffff;
                font-size: 12px;
                justify-self: start;
                padding: 4px 9px;
            }

            .badge.green {
                background: var(--green);
            }

            .badge.orange {
                background: var(--accent-strong);
            }

            .badge.red {
                background: var(--red);
            }

            .workflow {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(4, 1fr);
                padding: 22px 0 56px;
            }

            .workflow-item {
                border-top: 1px solid var(--line);
                padding-top: 16px;
            }

            .workflow-item strong {
                display: block;
                font-size: 15px;
            }

            .workflow-item span {
                color: var(--muted);
                display: block;
                font-size: 13px;
                line-height: 1.55;
                margin-top: 6px;
            }

            @media (max-width: 960px) {
                .hero {
                    grid-template-columns: 1fr;
                    padding-top: 28px;
                }

                .product {
                    min-width: 0;
                }

                .workflow {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 620px) {
                .metrics,
                .stat-grid,
                .workflow {
                    grid-template-columns: 1fr;
                }

                .table-row {
                    grid-template-columns: 1fr;
                }

                .hero-actions a,
                .nav a {
                    width: 100%;
                }

                .nav {
                    align-items: stretch;
                    flex-direction: column;
                    gap: 14px;
                }

                .language-switcher {
                    margin-left: 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="nav">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }} logo">
                    <span>{{ config('app.name', 'BuildBoard') }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="{{ $adminUrl }}">{{ $isAuthenticated ? 'Dashboard' : 'Admin panel' }}</a>
                    <select onchange="window.location.href = '/locale/' + this.value" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel); color: var(--text); cursor: pointer;">
                        @php
                            $availableLocales = config('app.available_locales', ['en', 'ka']);
                            $currentLocale = app()->getLocale();
                            $localeNames = [
                                'en' => 'English',
                                'ka' => 'ქართული'
                            ];
                        @endphp
                        @foreach($availableLocales as $locale)
                            <option value="{{ $locale }}" {{ $currentLocale === $locale ? 'selected' : '' }}>
                                {{ $localeNames[$locale] ?? ucfirst($locale) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <main>
                <section class="hero">
                    <div>
                        <p class="eyebrow">{{ __('welcome.platform') }}</p>
                        <h1>{{ __('welcome.title') }}</h1>
                        <p class="lead">
                            {{ __('welcome.description') }}
                        </p>

                        <div class="hero-actions">
                            <a class="primary" href="{{ $adminUrl }}">{{ __('welcome.login') }}</a>
                            <a class="secondary" href="#workflow">{{ __('welcome.how_it_works') }}</a>
                        </div>

                        <div class="metrics" aria-label="BuildBoard metrics">
                            <div class="metric">
                                <strong>{{ __('welcome.tenant') }}</strong>
                                <span>{{ __('welcome.tenant_desc') }}</span>
                            </div>
                            <div class="metric">
                                <strong>{{ __('welcome.roles') }}</strong>
                                <span>{{ __('welcome.roles_desc') }}</span>
                            </div>
                            <div class="metric">
                                <strong>{{ __('welcome.flow') }}</strong>
                                <span>{{ __('welcome.flow_desc') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="product" aria-label="BuildBoard dashboard preview">
                        <div class="product-top">
                            <strong>{{ __('welcome.admin_panel') }}</strong>
                            <span>{{ __('welcome.live_overview') }}</span>
                        </div>
                        <div class="product-body">
                            <div class="stat-grid">
                                <div class="product-card">
                                    <span>{{ __('welcome.active_projects') }}</span>
                                    <strong>12</strong>
                                </div>
                                <div class="product-card">
                                    <span>{{ __('welcome.pending_orders') }}</span>
                                    <strong>7</strong>
                                </div>
                                <div class="product-card">
                                    <span>{{ __('welcome.unpaid_balance') }}</span>
                                    <strong>18k GEL</strong>
                                </div>
                            </div>
                            <div class="table-preview">
                                <span>{{ __('welcome.upcoming_work') }}</span>
                                <div class="table-row">
                                    <strong>Apartment renovation</strong>
                                    <span>Nino Client</span>
                                    <span>Sep 09</span>
                                    <span class="badge orange">Pending</span>
                                </div>
                                <div class="table-row">
                                    <strong>Kitchen demolition</strong>
                                    <span>Mariam Client</span>
                                    <span>Jun 24</span>
                                    <span class="badge green">Active</span>
                                </div>
                                <div class="table-row">
                                    <strong>Bathroom repair</strong>
                                    <span>Giorgi Client</span>
                                    <span>Jun 18</span>
                                    <span class="badge red">Risk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="workflow" class="workflow" aria-label="BuildBoard workflow">
                    <div class="workflow-item">
                        <strong>{{ __('welcome.workflow_1_title') }}</strong>
                        <span>{{ __('welcome.workflow_1_desc') }}</span>
                    </div>
                    <div class="workflow-item">
                        <strong>{{ __('welcome.workflow_2_title') }}</strong>
                        <span>{{ __('welcome.workflow_2_desc') }}</span>
                    </div>
                    <div class="workflow-item">
                        <strong>{{ __('welcome.workflow_3_title') }}</strong>
                        <span>{{ __('welcome.workflow_3_desc') }}</span>
                    </div>
                    <div class="workflow-item">
                        <strong>{{ __('welcome.workflow_4_title') }}</strong>
                        <span>{{ __('welcome.workflow_4_desc') }}</span>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
