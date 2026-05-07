<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Unit;
use Filament\Widgets\Widget;

class OrgStatsBarWidget extends Widget
{
    protected static string $view = 'filament.widgets.org-stats-bar-widget';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'officeCount' => Office::query()->count(),
            'directorateCount' => Directorate::query()->count(),
            'departmentCount' => Department::query()->count(),
            'unitCount' => Unit::query()->count(),
            'personnelCount' => Personnel::query()->count(),
        ];
    }
}
