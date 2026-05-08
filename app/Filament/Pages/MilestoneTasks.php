<?php

namespace App\Filament\Pages;

use App\Models\Milestone;
use App\Models\Task;
use App\Support\ProjectAccess;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class MilestoneTasks extends Page
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.milestone-tasks';

    public Milestone $milestone;

    public function mount(string $milestone): void
    {
        abort_unless(auth()->check(), 403);

        $this->milestone = Milestone::query()
            ->with('project')
            ->where('ulid', $milestone)
            ->orWhere('id', $milestone)
            ->firstOrFail();

        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->milestone->project), 403);
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTasks(): Collection
    {
        return $this->milestone
            ->tasks()
            ->whereNull('deleted_at')
            ->latest()
            ->get();
    }

    public function markTaskDone(int $taskId): void
    {
        $task = Task::query()
            ->where('milestone_id', $this->milestone->id)
            ->whereKey($taskId)
            ->firstOrFail();

        abort_unless(Task::canBeMarkedDoneBy(auth()->user(), $task), 403);

        $task->mark_as_done();

        Notification::make()->title('Task marked as done')->success()->send();
    }
}
