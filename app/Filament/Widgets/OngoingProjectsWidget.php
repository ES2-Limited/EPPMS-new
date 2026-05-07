<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Support\ProjectAccess;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class OngoingProjectsWidget extends Widget
{
    protected static string $view = 'filament.widgets.ongoing-projects-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $query = $this->scopedProjectsQuery()->where('status', 'in_progress');

        return [
            'count' => (clone $query)->count(),
            'projects' => $query
                ->with('creator')
                ->latest('id')
                ->limit(3)
                ->get()
                ->map(fn (Project $project): array => [
                    'name' => $project->name,
                    'creator' => $project->creator?->name ?? 'Unknown',
                    'created' => $project->created_at?->diffForHumans() ?? '-',
                    'progress' => $project->get_progress(),
                    'url' => ProjectResource::getUrl('view', ['record' => $project]),
                ]),
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

    protected function scopedProjectsQuery(): Builder
    {
        $query = Project::query();

        return auth()->user()
            ? ProjectAccess::scopeProjects($query, auth()->user())
            : $query->whereRaw('1 = 0');
    }
}
