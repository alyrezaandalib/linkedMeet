<?php

namespace App\Filament\Widgets;

use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $heading = "User Overview";

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
