<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorResource;
use App\Models\Contractor;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;

class EditContractor extends EditRecord
{
    protected static string $resource = ContractorResource::class;

    protected static ?string $title = 'Firm Registration';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['firm_name'] = $this->record->user?->name;
        $data['email'] = $this->record->user?->email;
        $data['phone'] = $this->record->phone ?: $this->record->user?->phone;

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
        $userData = [
            'name' => $data['firm_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $userData['password'] = $data['password'];
        }

        $record->user->update($userData);

        $record->update([
            'firm_type_id' => $data['firm_type_id'],
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'logo' => $data['logo'] ?? null,
        ]);

        $correctRole = ((int) $data['firm_type_id'] === Contractor::TYPE_CONSULTANT) ? RoleAndPermissions::CONSULTANT : RoleAndPermissions::CONTRACTOR;
        $wrongRole = $correctRole === RoleAndPermissions::CONSULTANT ? RoleAndPermissions::CONTRACTOR : RoleAndPermissions::CONSULTANT;

        $record->user->removeRole($wrongRole);
        $record->user->assignRole($correctRole);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('goBack')->label('Go back')->url(static::getResource()::getUrl())->color('primary'), Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Save'),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->extraAttributes(['style' => 'background-color: #FCE8EC; color: #9F1239;']),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }
}
