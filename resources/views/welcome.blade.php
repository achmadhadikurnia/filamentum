<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Filamentum') }}</title>
        <meta name="description" content="Filamentum - Laravel starter kit with Filament admin panel">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            *, *::before, *::after {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            :root {
                --color-primary: #6366f1;
                --color-primary-dark: #4f46e5;
                --color-bg-light: #fafafa;
                --color-bg-dark: #0a0a0a;
                --color-text-light: #171717;
                --color-text-dark: #fafafa;
                --color-text-muted-light: #737373;
                --color-text-muted-dark: #a3a3a3;
            }

            html {
                font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
                line-height: 1.5;
                -webkit-text-size-adjust: 100%;
            }

            body {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                background-color: var(--color-bg-light);
                color: var(--color-text-light);
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    background-color: var(--color-bg-dark);
                    color: var(--color-text-dark);
                }
            }

            /* Header */
            .header {
                position: fixed;
                top: 0;
                right: 0;
                padding: 1.5rem 2rem;
                z-index: 10;
            }

            .nav {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .nav-link {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1.25rem;
                font-size: 0.875rem;
                font-weight: 500;
                text-decoration: none;
                border-radius: 9999px;
                transition: all 0.2s ease;
                color: var(--color-text-light);
            }

            @media (prefers-color-scheme: dark) {
                .nav-link {
                    color: var(--color-text-dark);
                }
            }

            .nav-link:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            @media (prefers-color-scheme: dark) {
                .nav-link:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                }
            }

            .nav-link--primary {
                background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
                color: white !important;
                box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
            }

            .nav-link--primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
                background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary));
            }

            /* Main Content */
            .main {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            /* Brand */
            .brand {
                text-align: center;
                animation: fadeInUp 0.8s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .brand__logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }

            .brand__icon {
                width: 56px;
                height: 56px;
                background: linear-gradient(135deg, var(--color-primary), #8b5cf6);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 32px rgba(99, 102, 241, 0.25);
            }

            .brand__icon svg {
                width: 32px;
                height: 32px;
                color: white;
            }

            .brand__title {
                font-size: 3.5rem;
                font-weight: 600;
                letter-spacing: -0.02em;
                background: linear-gradient(135deg, var(--color-primary), #8b5cf6, #ec4899);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            @media (min-width: 640px) {
                .brand__title {
                    font-size: 4.5rem;
                }
            }

            .brand__tagline {
                font-size: 1.125rem;
                color: var(--color-text-muted-light);
                font-weight: 300;
                max-width: 400px;
                margin: 0 auto;
            }

            @media (prefers-color-scheme: dark) {
                .brand__tagline {
                    color: var(--color-text-muted-dark);
                }
            }

            /* Footer */
            .footer {
                padding: 1.5rem 2rem;
                text-align: center;
            }

            .footer__text {
                font-size: 0.75rem;
                color: var(--color-text-muted-light);
            }

            @media (prefers-color-scheme: dark) {
                .footer__text {
                    color: var(--color-text-muted-dark);
                }
            }

            .footer__link {
                color: var(--color-primary);
                text-decoration: none;
                transition: opacity 0.2s ease;
            }

            .footer__link:hover {
                opacity: 0.8;
            }
        </style>
    </head>
    <body>
        @if (Route::has('filament.app.auth.login'))
            <header class="header">
                <nav class="nav">
                    @auth
                        <a href="{{ filament()->getUrl() }}" class="nav-link nav-link--primary">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ filament()->getLoginUrl() }}" class="nav-link">
                            Log in
                        </a>

                        @if (filament()->hasRegistration())
                            <a href="{{ filament()->getRegistrationUrl() }}" class="nav-link nav-link--primary">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </header>
        @endif

        <main class="main">
            <div class="brand">
                <div class="brand__logo">
                    <div class="brand__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                </div>
                <h1 class="brand__title">Filamentum</h1>
                <p class="brand__tagline">Laravel starter kit with Filament admin panel</p>
            </div>
        </main>

        <footer class="footer">
            <p class="footer__text">
                Built with <a href="https://laravel.com" target="_blank" rel="noopener" class="footer__link">Laravel</a> & <a href="https://filamentphp.com" target="_blank" rel="noopener" class="footer__link">Filament</a>
            </p>
        </footer>
    </body>
</html>
