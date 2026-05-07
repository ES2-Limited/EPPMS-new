<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\MilestoneResource;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListMilestones extends ListRecords
{
    protected static string $resource = MilestoneResource::class;

    #[Url(as: 'project_id')]
    public ?int $projectId = null;

    public function getProject(): ?Project
    {
        return $this->projectId ? Project::find($this->projectId) : null;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        return $query;
    }

    public function getHeading(): string
    {
        $project = $this->getProject();

        return $project ? "Milestones — {$project->name}" : 'Milestones';
    }

    protected function getHeaderActions(): array
    {
        $canManage = auth()->user()?->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]) ?? false;

        $actions = [];

        if ($canManage) {
            $actions[] = Actions\CreateAction::make()
                ->label('Add Milestone')
                ->url(fn (): string => MilestoneResource::getUrl('create').
                    ($this->projectId ? '?project_id='.$this->projectId : ''));
        }

        return $actions;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        if ($project = $this->getProject()) {
            $breadcrumbs[ProjectResource::getUrl('view', ['record' => $project])] = $project->name;
        }

        $breadcrumbs[] = 'Milestones';

        return $breadcrumbs;
    }
}
