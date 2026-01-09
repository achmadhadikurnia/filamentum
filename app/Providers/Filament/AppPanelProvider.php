<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Register;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path(config('filamentum.path', 'app'))
            ->when(config('filamentum.domain'), fn ($panel) => $panel->domain(config('filamentum.domain')))
            ->login()
            ->when(config('filamentum.registration', false), fn ($panel) => $panel->registration(Register::class))
            ->when(config('filamentum.password_reset', false), fn ($panel) => $panel->passwordReset())
            ->when(config('filamentum.email_verification', false), fn ($panel) => $panel->emailVerification())
            ->when(config('filamentum.email_change_verification', false), fn ($panel) => $panel->emailChangeVerification())
            ->when(config('filamentum.profile', true), fn ($panel) => $panel->profile())
            ->colors([
                'primary' => $this->getColor(config('filamentum.primary_color', 'amber')),
            ])
            ->darkMode(config('filamentum.dark_mode', true))
            ->defaultThemeMode($this->getThemeMode(config('filamentum.default_theme_mode', 'system')))
            ->when(config('filamentum.database_notifications', false), fn ($panel) => $panel->databaseNotifications())
            // SPA & Navigation
            ->when(config('filamentum.spa_mode', true), fn ($panel) => $panel->spa(hasPrefetching: config('filamentum.spa_prefetching', false)))
            // Content Layout
            ->maxContentWidth($this->getWidth(config('filamentum.max_content_width', '7xl')))
            ->subNavigationPosition($this->getSubNavigationPosition(config('filamentum.sub_navigation_position', 'start')))
            // Panel Behavior
            ->when(config('filamentum.unsaved_changes_alerts', false), fn ($panel) => $panel->unsavedChangesAlerts())
            ->when(config('filamentum.database_transactions', false), fn ($panel) => $panel->databaseTransactions())
            ->broadcasting(config('filamentum.broadcasting', false))
            ->when(config('filamentum.strict_authorization', false), fn ($panel) => $panel->strictAuthorization())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->navigationGroups([
                __('Users'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }

    /**
     * Get Color constant from color name.
     */
    protected function getColor(string $colorName): array
    {
        return match (strtolower($colorName)) {
            'slate' => Color::Slate,
            'gray' => Color::Gray,
            'zinc' => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone' => Color::Stone,
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
            default => Color::Amber,
        };
    }

    private function getThemeMode(string $mode): ThemeMode
    {
        return match ($mode) {
            'light' => ThemeMode::Light,
            'dark' => ThemeMode::Dark,
            default => ThemeMode::System,
        };
    }

    private function getWidth(string $width): Width
    {
        return match ($width) {
            'xs' => Width::ExtraSmall,
            'sm' => Width::Small,
            'md' => Width::Medium,
            'lg' => Width::Large,
            'xl' => Width::ExtraLarge,
            '2xl' => Width::TwoExtraLarge,
            '3xl' => Width::ThreeExtraLarge,
            '4xl' => Width::FourExtraLarge,
            '5xl' => Width::FiveExtraLarge,
            '6xl' => Width::SixExtraLarge,
            '7xl' => Width::SevenExtraLarge,
            'full' => Width::Full,
            default => Width::SevenExtraLarge,
        };
    }

    private function getSubNavigationPosition(string $position): SubNavigationPosition
    {
        return match ($position) {
            'top' => SubNavigationPosition::Top,
            'end' => SubNavigationPosition::End,
            default => SubNavigationPosition::Start,
        };
    }
}
