<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Support\ProjectAccess;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ProjectReport extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-report';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        abort_unless(auth()->check() && ProjectAccess::canViewProject(auth()->user(), $this->record), 403);
        abort_unless($this->record->get_progress() === 100, 403, 'Project report is only available when the project is 100% complete.');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Project Report';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
