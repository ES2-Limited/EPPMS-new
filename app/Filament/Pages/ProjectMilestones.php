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
    protected static ?string $slug = 'project/milestones/{project}';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.project-milestones';

    public Project $projectRecord;

    public function mount(string $project): void
    {
        abort_unless(auth()->check(), 403);

        $this->projectRecord = Project::query()->where('ulid', $project)->firstOrFail();

        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->projectRecord), 403);
    }

    public function getTitle(): string
    {
        return $this->projectRecord->name.' Project Milestones';
    }

    public function getMilestones(): Collection
    {
        return $this->projectRecord
            ->milestones()
            ->withCount('tasks')
            ->with('tasks')
            ->whereNull('deleted_at')
            ->latest()
            ->get();
    }

    public function deleteMilestone(int $milestoneId): void
    {
        abort_unless(ProjectAccess::canManageProject(auth()->user(), $this->projectRecord), 403);

        Milestone::query()
            ->where('project_id', $this->projectRecord->id)
            ->whereKey($milestoneId)
            ->firstOrFail()
            ->delete();

        Notification::make()->title('Milestone deleted')->success()->send();
    }
}
