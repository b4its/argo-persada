<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\FinanceAkunKeuanganResource;
use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use App\Filament\Resources\Finance\FinanceMutasis\FinanceMutasiResource;
use App\Filament\Resources\Finance\FinancePemesanans\FinancePemesananResource;
use App\Filament\Resources\Finance\FinanceSaldos\FinanceSaldoResource;
use App\Filament\Widgets\Finance\FinanceStatsOverview;
use App\Http\Middleware\CheckAdminRoleRedirect;
use App\Http\Middleware\CheckFinanceRoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
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

class FinancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('finance')
            ->path('finance')
            ->brandName("Finance Panel")
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
                'primary' => Color::Green,
                'royal'     => '#4f46e5', // Biru keunguan mewah
                'emerald'   => '#10b981', // Hijau perhiasan
                'ocean'     => '#0ea5e9', // Biru laut cerah
                'sunshine'  => '#f59e0b', // Kuning hangat
                'crimson'   => '#e11d48', // Merah gelap elegan
                'slate'     => '#475569', // Abu-abu kebiruan profesional
                'night'     => '#1e293b', // Gelap pekat
                'cyan'  => '#3fbde4', // Ungu modern
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        // 1. Dashboard selalu di atas
                        ...Dashboard::getNavigationItems(),
                        ...FinancePemesananResource::getNavigationItems(),
                        ...FinanceAkunKeuanganResource::getNavigationItems(),
                        ...FinanceKasHarianResource::getNavigationItems(),
                    ]);
                    
            })
            ->discoverResources(in: app_path('Filament/Resources/Finance'), for: 'App\Filament\Resources\Finance')
            ->discoverPages(in: app_path('Filament/Pages/Finance'), for: 'App\Filament\Pages\Finance')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets/Finance'), for: 'App\Filament\Widgets\Finance')
            ->widgets([
                FinanceStatsOverview::class,
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
                CheckFinanceRoleRedirect::class,
            ]);
    }
}
