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

class ProjectStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $projects = $this->scopedProjectsQuery();

        return [
            Stat::make('Total Office Locations', (string) intval(Office::query()->count()))
                ->icon('heroicon-o-map-pin')
                ->extraAttributes(['style' => 'background-color: #ffffff; color: #111827; padding: 12px; border: 1px solid #D1D5DB;']),
            Stat::make('Total Directorates', (string) intval(Directorate::query()->count()))
                ->icon('heroicon-o-building-office-2')
                ->extraAttributes(['style' => 'background-color: #ffffff; color: #111827; padding: 12px; border: 1px solid #D1D5DB;']),
            Stat::make('Total Departments', (string) intval(Department::query()->count()))
                ->icon('heroicon-o-building-library')
                ->extraAttributes(['class' => 'stat-departments', 'style' => 'background-color: #E6F7F5; color: #111827; padding: 12px; border: 1px solid #99F6E4;']),
            Stat::make('Total Units', (string) intval(Unit::query()->count()))
                ->icon('heroicon-o-users')
                ->extraAttributes(['class' => 'stat-units', 'style' => 'background-color: #FCE8EC; color: #111827; padding: 12px; border: 1px solid #FDA4AF;']),
            Stat::make('Total Personnels', (string) intval(Personnel::query()->count()))
                ->icon('heroicon-o-user')
                ->extraAttributes(['class' => 'stat-personnels', 'style' => 'background-color: #E8F5EE; color: #111827; padding: 12px; border: 1px solid #86EFAC;']),
            Stat::make('Total Projects', (string) intval((clone $projects)->count()))
                ->icon('heroicon-o-briefcase')
                ->extraAttributes(['style' => 'background-color: #ffffff; color: #111827; padding: 12px; border: 1px solid #D1D5DB;']),
            Stat::make('Projects In Progress', (string) intval((clone $projects)->where('status', 'in_progress')->count()))
                ->icon('heroicon-o-arrow-path')
                ->extraAttributes(['style' => 'background-color: #ffffff; color: #111827; padding: 12px; border: 1px solid #D1D5DB;']),
            Stat::make('Projects Completed', (string) intval((clone $projects)->where('status', 'completed')->count()))
                ->icon('heroicon-o-check-circle')
                ->extraAttributes(['style' => 'background-color: #ffffff; color: #111827; padding: 12px; border: 1px solid #D1D5DB;']),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function scopedProjectsQuery(): Builder
    {
        $query = Project::query();

        return auth()->user()
            ? ProjectAccess::scopeProjects($query, auth()->user())
            : $query->whereRaw('1 = 0');
    }
}
