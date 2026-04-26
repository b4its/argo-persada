<?php

namespace App\Filament\Widgets\Admin\StatsOverview;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminAkunStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Akun';
    protected function getStats(): array
    {
        $totalAkunMarketing = User::where('role', 'marketing')->count();
        $totalAkunFinance = User::where('role', 'finance')->count();
        $totalAkunLogistik = User::where('role', 'logistik')->count();
        return [
            //
            Stat::make('Akun Marketing', $totalAkunMarketing)
                ->description('Total Akun Marketing')
                ->descriptionIcon('heroicon-o-user')
                ->color('info'),

            Stat::make('Akun Finance', $totalAkunFinance)
                ->description('Total Akun Finance')
                ->descriptionIcon('heroicon-o-user')
                ->color('emerald'),
                
            Stat::make('Akun Logistik', $totalAkunLogistik)
                ->description('Total Akun Logistik')
                ->descriptionIcon('heroicon-o-user')
                ->color('cyan'),
        ];
    }
}
