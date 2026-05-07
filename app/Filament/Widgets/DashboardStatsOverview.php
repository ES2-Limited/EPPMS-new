<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Project;
use App\Models\Unit;
use App\Support\ProjectAccess;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $projects = $this->scopedProjectsQuery();

        return [
            Stat::make('Total Office Locations', Office::query()->count())
                ->icon('heroicon-o-map-pin')
                ->extraAttributes(['style' => 'background-color: #ffffff;']),
            Stat::make('Total Directorates', Directorate::query()->count())
                ->icon('heroicon-o-building-office-2')
                ->extraAttributes(['style' => 'background-color: #ffffff;']),
            Stat::make('Total Departments', Department::query()->count())
                ->icon('heroicon-o-building-library')
                ->extraAttributes(['style' => 'background-color: #E6F7F5;']),
            Stat::make('Total Units', Unit::query()->count())
                ->icon('heroicon-o-squares-2x2')
                ->extraAttributes(['style' => 'background-color: #FCE8EC;']),
            Stat::make('Total Personnels', Personnel::query()->count())
                ->icon('heroicon-o-users')
                ->extraAttributes(['style' => 'background-color: #E8F5EE;']),
            Stat::make('Total Projects', (clone $projects)->count())->icon('heroicon-o-briefcase')->color('primary'),
            Stat::make('Projects In Progress', (clone $projects)->where('status', 'in_progress')->count())->icon('heroicon-o-arrow-path')->color('warning'),
            Stat::make('Projects Completed', (clone $projects)->where('status', 'completed')->count())->icon('heroicon-o-check-circle')->color('success'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function scopedProjectsQuery(): Builder
    {
        $query = Project::query();

        return auth()->user() ? ProjectAccess::scopeProjects($query, auth()->user()) : $query->whereRaw('1 = 0');
    }
}
