<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorResource;
use App\Models\Contractor;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContractor extends EditRecord
{
    protected static string $resource = ContractorResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['firm_name'] = $this->record->user?->name;
        $data['email'] = $this->record->user?->email;
        $data['phone'] = $this->record->user?->phone;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['firm_name'] = $this->form->getRawState()['firm_name'] ?? null;
        $data['email'] = $this->form->getRawState()['email'] ?? null;
        $data['phone'] = $this->form->getRawState()['phone'] ?? null;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->user->update([
            'name' => $data['firm_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $record->update(['firm_type_id' => $data['firm_type_id']]);

        $correctRole = ((int) $data['firm_type_id'] === Contractor::TYPE_CONSULTANT) ? RoleAndPermissions::CONSULTANT : RoleAndPermissions::CONTRACTOR;
        $wrongRole = $correctRole === RoleAndPermissions::CONSULTANT ? RoleAndPermissions::CONTRACTOR : RoleAndPermissions::CONSULTANT;

        $record->user->removeRole($wrongRole);
        $record->user->assignRole($correctRole);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }
}
