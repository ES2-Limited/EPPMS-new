<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\CreateRecord;

class CreateMilestone extends CreateRecord
{
    protected static string $resource = MilestoneResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['project_id']) && request()->has('project_id')) {
            $data['project_id'] = (int) request()->query('project_id');
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $projectId = $this->record->project_id;

        return MilestoneResource::getUrl('index', ['project_id' => $projectId]);
    }

    public function getBreadcrumbs(): array
    {
        $projectId = request()->query('project_id') ?? $this->record?->project_id;
        $project   = $projectId ? Project::find($projectId) : null;

        $breadcrumbs = [];

        if ($project) {
            $breadcrumbs[ProjectResource::getUrl('view', ['record' => $project])] = $project->name;
            $breadcrumbs[MilestoneResource::getUrl('index', ['project_id' => $project->id])] = 'Milestones';
        }

        $breadcrumbs[] = 'Add Milestone';

        return $breadcrumbs;
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        if (request()->has('project_id')) {
            $this->form->fill(['project_id' => (int) request()->query('project_id')]);
        }
    }
}
