<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorPersonnelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContractorPersonnel extends EditRecord
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $record->user->assignRole(RoleAndPermissions::CONTRACTOR_PERSONNEL);

        $record->update([
            'contractor_id' => $data['contractor_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'] ?? null,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
