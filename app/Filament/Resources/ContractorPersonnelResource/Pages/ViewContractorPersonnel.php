<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Filament\Resources\ContractorPersonnelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContractorPersonnel extends ViewRecord
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
