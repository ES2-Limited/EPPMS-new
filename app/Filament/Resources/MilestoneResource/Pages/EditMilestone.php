<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMilestone extends EditRecord
{
    protected static string $resource = MilestoneResource::class;

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
        return MilestoneResource::getUrl('index', ['project_id' => $this->record->project_id]);
    }

    public function getBreadcrumbs(): array
    {
        $project = $this->record->project;

        $breadcrumbs = [];

        if ($project) {
            $breadcrumbs[ProjectResource::getUrl('view', ['record' => $project])] = $project->name;
            $breadcrumbs[MilestoneResource::getUrl('index', ['project_id' => $project->id])] = 'Milestones';
        }

        $breadcrumbs[] = 'Edit: '.$this->record->name;

        return $breadcrumbs;
    }
}
