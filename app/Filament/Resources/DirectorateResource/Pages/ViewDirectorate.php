<?php

namespace App\Filament\Resources\DirectorateResource\Pages;

use App\Filament\Resources\DirectorateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDirectorate extends ViewRecord
{
    protected static string $resource = DirectorateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
