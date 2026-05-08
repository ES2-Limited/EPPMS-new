<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return MilestoneResource::getUrl('view', ['record' => $this->record->milestone]);
    }

    public function getBreadcrumbs(): array
    {
        $milestone = $this->record->milestone;
        $project = $milestone?->project;

        $breadcrumbs = [];

        if ($project) {
            $breadcrumbs[ProjectResource::getUrl('view', ['record' => $project])] = $project->name;
            $breadcrumbs[MilestoneResource::getUrl('index', ['project_id' => $project->id])] = 'Milestones';
        }

        if ($milestone) {
            $breadcrumbs[MilestoneResource::getUrl('view', ['record' => $milestone])] = $milestone->name;
        }

        $breadcrumbs[] = 'Edit: '.$this->record->name;

        return $breadcrumbs;
    }
}
