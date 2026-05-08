<?php

namespace App\Filament\Pages;

use App\Models\Milestone;
use App\Models\Project;
use App\Support\ProjectAccess;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ProjectMilestones extends Page
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.project-milestones';

    public Project $project;

    public function mount(string $project): void
    {
        abort_unless(auth()->check(), 403);

        $this->project = Project::query()
            ->where('ulid', $project)
            ->orWhere('id', $project)
            ->firstOrFail();

        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->project), 403);
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getMilestones(): Collection
    {
        return $this->project
            ->milestones()
            ->withCount('tasks')
            ->with('tasks')
            ->whereNull('deleted_at')
            ->latest()
            ->get();
    }

    public function deleteMilestone(int $milestoneId): void
    {
        abort_unless(ProjectAccess::canManageProject(auth()->user(), $this->project), 403);

        Milestone::query()
            ->where('project_id', $this->project->id)
            ->whereKey($milestoneId)
            ->firstOrFail()
            ->delete();

        Notification::make()->title('Milestone deleted')->success()->send();
    }
}
