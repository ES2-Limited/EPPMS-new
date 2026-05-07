<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Support\ProjectAccess;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class MilestoneReport extends ViewRecord
{
    protected static string $resource = MilestoneResource::class;

    protected static string $view = 'filament.resources.milestone-resource.pages.milestone-report';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        abort_unless(auth()->check() && $this->record->project && ProjectAccess::canViewProject(auth()->user(), $this->record->project), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Milestone Report';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
