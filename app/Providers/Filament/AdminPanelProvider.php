<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Admin\AdminDashboard;
use App\Filament\Widgets\Admin\AdminStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
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
            ->brandLogo(fn() => view('filament.components.brand-logo', ['logoPath' => $logoPath, 'brandNames' => $brandNames]))
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
            ->globalSearch(false)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources/Admin'), for: 'App\Filament\Resources\Admin')
            ->discoverPages(in: app_path('Filament/Pages/Admin'), for: 'App\Filament\Pages\Admin')
            ->pages([
                AdminDashboard::class,
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
            ]);
            
    }
}