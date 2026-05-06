<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContractor extends ViewRecord
{
    protected static string $resource = ContractorResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['firm_name'] = $this->record->user?->name;
        $data['email'] = $this->record->user?->email;
        $data['phone'] = $this->record->user?->phone;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
