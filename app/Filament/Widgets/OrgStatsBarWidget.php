<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrgStatsBarWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Office Locations', Office::query()->count())
                ->icon('heroicon-o-map-pin')
                ->extraAttributes(['style' => 'background-color: #ffffff;']),

            Stat::make('Directorates', Directorate::query()->count())
                ->icon('heroicon-o-building-office-2')
                ->extraAttributes(['style' => 'background-color: #ffffff;']),

            Stat::make('Departments', Department::query()->count())
                ->icon('heroicon-o-building-library')
                ->extraAttributes(['style' => 'background-color: #E6F7F5;']),

            Stat::make('Units', Unit::query()->count())
                ->icon('heroicon-o-squares-2x2')
                ->extraAttributes(['style' => 'background-color: #FCE8EC;']),

            Stat::make('Personnels', Personnel::query()->count())
                ->icon('heroicon-o-users')
                ->extraAttributes(['style' => 'background-color: #E8F5EE;']),
        ];
    }

    protected function getColumns(): int
    {
        return 5;
    }
}
