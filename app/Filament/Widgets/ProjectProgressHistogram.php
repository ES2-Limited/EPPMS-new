<?php

namespace App\Filament\Widgets;

use App\Models\Directorate;
use App\Models\Office;
use App\Models\Project;
use App\Support\ProjectAccess;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class ProjectProgressHistogram extends ChartWidget
{
    protected static ?string $heading = 'Project Progress';

    protected static ?string $description = 'Completion percentage for scoped projects with progress.';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '360px';

    protected function getData(): array
    {
        $projects = $this->scopedProjectsQuery()
            ->with(['milestones.tasks'])
            ->whereHas('milestones.tasks', fn (Builder $tasks): Builder => $tasks->where('status', 'done'))
            ->latest('id')
            ->limit(30)
            ->get()
            ->map(fn (Project $project): array => [
                'name' => str($project->name)->limit(30)->toString(),
                'progress' => $project->get_progress(),
            ])
            ->filter(fn (array $project): bool => $project['progress'] > 0)
            ->values();

        if ($projects->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Progress %',
                    'data' => [0],
                ]],
                'labels' => ['No projects with progress'],
            ];
        }

        return [
            'datasets' => [[
                'label' => 'Progress %',
                'data' => $projects->pluck('progress')->all(),
            ]],
            'labels' => $projects->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        $filters = ['all' => 'All scoped projects'];

        foreach (Office::query()->orderBy('name')->pluck('name', 'id') as $id => $name) {
            $filters['office:'.$id] = 'Office: '.$name;
        }

        foreach (Directorate::query()->orderBy('name')->pluck('name', 'id') as $id => $name) {
            $filters['directorate:'.$id] = 'Directorate: '.$name;
        }

        return $filters;
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                ],
            ],
        ];
    }

    protected function scopedProjectsQuery(): Builder
    {
        $query = Project::query();

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        ProjectAccess::scopeProjects($query, auth()->user());

        if (str_starts_with((string) $this->filter, 'office:')) {
            $query->where('office_id', (int) substr((string) $this->filter, strlen('office:')));
        }

        if (str_starts_with((string) $this->filter, 'directorate:')) {
            $query->where('directorate_id', (int) substr((string) $this->filter, strlen('directorate:')));
        }

        return $query;
    }
}
