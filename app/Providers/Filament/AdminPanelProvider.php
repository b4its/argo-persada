<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Admin\AdminDashboard;
use App\Filament\Resources\Admin\AdminAkunKeuangans\AdminAkunKeuanganResource;
use App\Filament\Resources\Admin\AdminCompanyInternals\AdminCompanyInternalResource;
use App\Filament\Resources\Admin\AdminKaryawans\AdminKaryawanResource;
use App\Filament\Resources\Admin\AdminKasHarians\AdminKasHarianResource;
use App\Filament\Resources\Admin\AdminPemesanans\AdminPemesananResource;
use App\Filament\Resources\Finance\FinanceAkunKeuangans\FinanceAkunKeuanganResource;
use App\Filament\Resources\Finance\FinanceKasHarians\FinanceKasHarianResource;
use App\Filament\Widgets\Admin\AdminStatsOverview;
use App\Filament\Widgets\Admin\AdminTaskTables;
use App\Filament\Widgets\Admin\StatsOverview\AdminAkunStatsOverview;
use App\Filament\Widgets\Admin\StatsOverview\AdminKasHarianStatsOverview;
use App\Filament\Widgets\Admin\StatsOverview\AdminPesananStatsOverview;
use App\Http\Middleware\CheckAdminRoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\MenuItem;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class AdminPanelProvider extends PanelProvider
{
    
    public function panel(Panel $panel): Panel
    {
        $brandNames = 'Admin Panel';
        $logoPath = asset('images/logo.webp');
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName($brandNames)
            // ->brandLogo(fn() => view('filament.components.brand-logo', ['logoPath' => $logoPath, 'brandNames' => $brandNames]))
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
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('filament.hooks.halaman-utama-button'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE,
                function (): string {
                    $page = Livewire::current();
                    if (!$page instanceof AdminDashboard) {
                        return '';
                    }
                    return view('filament.pages.admin.partials.date-filter')->render();
                },
                scopes: ['admin-dashboard'],
            )
            ->globalSearch(false)
            ->colors([
                'primary' => Color::hex("#ce6aec"),
                'royal'     => '#4f46e5', // Biru keunguan mewah
                'emerald'   => '#10b981', // Hijau perhiasan
                'ocean'     => '#0ea5e9', // Biru laut cerah
                'sunshine'  => '#f59e0b', // Kuning hangat
                'crimson'   => '#e11d48', // Merah gelap elegan
                'slate'     => '#475569', // Abu-abu kebiruan profesional
                'night'     => '#1e293b', // Gelap pekat
                'cyan'  => '#3fbde4', // Ungu modern
                ])
                // 'lavender'  => '#8b5cf6', // Ungu modern
            ->discoverResources(in: app_path('Filament/Resources/Admin'), for: 'App\Filament\Resources\Admin')
            ->discoverPages(in: app_path('Filament/Pages/Admin'), for: 'App\Filament\Pages\Admin')
            ->pages([
                AdminDashboard::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        // 1. Dashboard selalu di atas
                        ...AdminDashboard::getNavigationItems(),
                        ...AdminCompanyInternalResource::getNavigationItems(),
                        ...AdminKaryawanResource::getNavigationItems(),
                        ...AdminPemesananResource::getNavigationItems(),
                        ...AdminAkunKeuanganResource::getNavigationItems(),
                        ...AdminKasHarianResource::getNavigationItems(),
                    ]);
                    
            })
            ->widgets([
                AdminKasHarianStatsOverview::class,
                AdminAkunStatsOverview::class,
                AdminPesananStatsOverview::class,
                AdminTaskTables::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets/Admin'), for: 'App\Filament\Widgets\Admin')

            ->viteTheme('resources/css/filament/admin/theme.css')
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
                CheckAdminRoleRedirect::class
            ]);
            
    }
}