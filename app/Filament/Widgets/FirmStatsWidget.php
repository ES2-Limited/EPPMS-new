<?php

namespace App\Filament\Widgets;

use App\Models\Contractor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FirmStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Contractors', Contractor::query()->where('firm_type_id', Contractor::TYPE_CONTRACTOR)->count())
                ->icon('heroicon-o-building-office')
                ->extraAttributes(['style' => 'background-color: #E6F7F5;']),

            Stat::make('Consultants', Contractor::query()->where('firm_type_id', Contractor::TYPE_CONSULTANT)->count())
                ->icon('heroicon-o-academic-cap')
                ->extraAttributes(['style' => 'background-color: #ffffff;']),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
