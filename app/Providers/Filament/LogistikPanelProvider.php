<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\Logistik\StatsOverview\LogistikSuratJalanStatsOverview;
use App\Filament\Widgets\Logistik\StatsOverview\LogistikSuratKembaliStatsOverview;
use App\Http\Middleware\CheckAdminRoleRedirect;
use App\Http\Middleware\CheckFinanceRoleRedirect;
use App\Http\Middleware\CheckLogistikRoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class LogistikPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('logistik')
            ->path('logistik')
            ->brandName("Logistik Panel")
            ->login()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url('#edit-profile'),
            ])
            ->renderHook(
                'panels::body.end',
                fn (): string => Blade::render('@livewire(\App\Livewire\EditProfileModal::class)')
            )
            ->globalSearch(false)
            ->colors([
                'primary' => Color::hex('#3fbde4'), // Orange color
            ])
            ->discoverResources(in: app_path('Filament/Resources/Logistik'), for: 'App\Filament\Resources\Logistik')
            ->discoverPages(in: app_path('Filament/Pages/Logistik'), for: 'App\Filament\Pages\Logistik')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets/Logistik'), for: 'App\Filament\Widgets\Logistik')
            ->widgets([
                LogistikSuratJalanStatsOverview::class,
                LogistikSuratKembaliStatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckLogistikRoleRedirect::class,
            ]);
    }
}
