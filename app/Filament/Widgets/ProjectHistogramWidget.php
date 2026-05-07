<?php

namespace App\Filament\Widgets;

use App\Models\Directorate;
use App\Models\Office;
use App\Models\Project;
use App\Support\ProjectAccess;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class ProjectHistogramWidget extends Widget
{
    protected static string $view = 'filament.widgets.project-histogram-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public string $directorateId = 'all';

    public string $officeId = 'all';

    protected function getViewData(): array
    {
        $projects = $this->filteredProjectsQuery()
            ->with(['milestones.tasks'])
            ->latest('id')
            ->get()
            ->map(fn (Project $project): array => [
                'name' => str($project->name)->limit(28)->toString(),
                'progress' => $project->get_progress(),
            ])
            ->filter(fn (array $project): bool => $project['progress'] > 0)
            ->values();

        return [
            'directorates' => Directorate::query()->orderBy('name')->pluck('name', 'id')->all(),
            'offices' => Office::query()->orderBy('name')->pluck('name', 'id')->all(),
            'projects' => $projects,
            'labels' => $projects->pluck('name')->all(),
            'values' => $projects->pluck('progress')->all(),
            'colours' => $projects->pluck('progress')->map(fn (int $progress): string => $this->progressColour($progress))->all(),
        ];
    }

    public function progressColour(int $progress): string
    {
        return match (true) {
            $progress < 25 => '#B91C1C',
            $progress <= 50 => '#C2410C',
            $progress <= 70 => '#A16207',
            $progress <= 89 => '#1D4ED8',
            default => '#15803D',
        };
    }

    protected function filteredProjectsQuery(): Builder
    {
        $query = Project::query();

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        ProjectAccess::scopeProjects($query, auth()->user());

        if ($this->directorateId !== 'all') {
            $query->where('directorate_id', (int) $this->directorateId);
        }

        if ($this->officeId !== 'all') {
            $query->where('office_id', (int) $this->officeId);
        }

        return $query;
    }
}
