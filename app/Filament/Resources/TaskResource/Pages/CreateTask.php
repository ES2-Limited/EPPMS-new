<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Resources\TaskResource;
use App\Models\Milestone;
use App\Models\Project;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['milestone_id']) && request()->has('milestone_id')) {
            $data['milestone_id'] = (int) request()->query('milestone_id');
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return MilestoneResource::getUrl('view', ['record' => $this->record->milestone_id]);
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        if (request()->has('milestone_id')) {
            $this->form->fill(['milestone_id' => (int) request()->query('milestone_id')]);
        }
    }

    public function getBreadcrumbs(): array
    {
        $milestoneId = request()->query('milestone_id') ?? $this->record?->milestone_id;
        $milestone   = $milestoneId ? Milestone::with('project')->find($milestoneId) : null;
        $project     = $milestone?->project;

        $breadcrumbs = [];

        if ($project) {
            $breadcrumbs[\App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project])] = $project->name;
            $breadcrumbs[MilestoneResource::getUrl('index', ['project_id' => $project->id])] = 'Milestones';
        }

        if ($milestone) {
            $breadcrumbs[MilestoneResource::getUrl('view', ['record' => $milestone])] = $milestone->name;
        }

        $breadcrumbs[] = 'Add Task';

        return $breadcrumbs;
    }
}
