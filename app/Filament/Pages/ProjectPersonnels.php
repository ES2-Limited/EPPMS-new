<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\ProjectPersonnel;
use App\Support\ProjectAccess;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ProjectPersonnels extends Page
{
    protected static bool $isDiscovered = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.project-personnels';

    public Project $projectRecord;

    public function mount(string $project): void
    {
        abort_unless(auth()->check(), 403);

        $this->projectRecord = Project::query()
            ->where('ulid', $project)
            ->orWhere('id', $project)
            ->firstOrFail();

        abort_unless(ProjectAccess::canViewProject(auth()->user(), $this->projectRecord), 403);
    }

    public function getTitle(): string
    {
        return $this->projectRecord->name.' Project Personnels';
    }

    public function getProjectPersonnels(): Collection
    {
        return ProjectPersonnel::query()
            ->with(['project', 'personnel.user'])
            ->where('project_id', $this->projectRecord->id)
            ->whereNull('deleted_at')
            ->latest()
            ->get();
    }
}
