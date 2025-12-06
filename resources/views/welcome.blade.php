<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Filamentum') }}</title>
        <meta name="description" content="Filamentum - Laravel starter kit with Filament admin panel">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

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

            /* Brand Links */
            .brand__links {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1rem;
                margin-top: 2rem;
            }

            .brand__link {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
                font-weight: 500;
                text-decoration: none;
                border-radius: 9999px;
                transition: all 0.2s ease;
                color: var(--color-text-muted-light);
                border: 1px solid rgba(0, 0, 0, 0.15);
                background: transparent;
            }

            @media (prefers-color-scheme: dark) {
                .brand__link {
                    color: var(--color-text-muted-dark);
                    border-color: rgba(255, 255, 255, 0.15);
                }
            }

            .brand__link:hover {
                color: var(--color-primary);
                border-color: var(--color-primary);
            }

            .brand__link--primary {
                background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
                color: white !important;
                border: none;
                box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
            }

            .brand__link--primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
            }

            .brand__link svg {
                width: 18px;
                height: 18px;
            }
        </style>
    </head>
    <body>
        @if (filament()->hasLogin())
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <!-- Diamond outline -->
                            <path d="M12 1L1 12L12 23L23 12L12 1ZM12 4L20 12L12 20L4 12L12 4Z"/>
                            <!-- Flame inside - larger -->
                            <path d="M12 6L8 11C8 11 8 14 10 15.5C9 14.5 9.5 13 10.5 11.5C11.5 10 12 9 12 9C12 9 12.5 10 13.5 11.5C14.5 13 15 14.5 14 15.5C16 14 16 11 16 11L12 6Z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="brand__title">Filamentum</h1>
                <p class="brand__tagline">Laravel starter kit with Filament admin panel</p>
                <div class="brand__links">
                    <a href="https://filamentum.kanekes.com" target="_blank" rel="noopener" class="brand__link brand__link--primary">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        Explore
                    </a>
                    <a href="https://github.com/kanekescom/filamentum" target="_blank" rel="noopener" class="brand__link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        Repository
                    </a>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p class="footer__text">
                Built with <a href="https://laravel.com" target="_blank" rel="noopener" class="footer__link">Laravel</a> & <a href="https://filamentphp.com" target="_blank" rel="noopener" class="footer__link">Filament</a>
            </p>
        </footer>
    </body>
</html>
