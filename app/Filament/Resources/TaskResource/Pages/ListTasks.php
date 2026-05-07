<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    #[Url(as: 'milestone_id')]
    public ?int $milestoneId = null;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->milestoneId) {
            $query->where('milestone_id', $this->milestoneId);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
