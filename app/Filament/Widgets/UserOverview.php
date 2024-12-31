<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayRegister = User::whereDate('created_at', '=', today())->count();
        $userCount = User::count();

        return [
            Stat::make('Users', $userCount)
                ->description($todayRegister . ' Today\'s Registrations')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
