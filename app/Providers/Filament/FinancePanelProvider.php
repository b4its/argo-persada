<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Finance\FinanceAkunKeuangans\FinanceAkunKeuanganResource;
use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use App\Filament\Resources\Finance\FinanceMutasis\FinanceMutasiResource;
use App\Filament\Resources\Finance\FinancePemesanans\FinancePemesananResource;
use App\Filament\Resources\Finance\FinanceSaldos\FinanceSaldoResource;
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
                AccountWidget::class,
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
            ]);
    }
}
