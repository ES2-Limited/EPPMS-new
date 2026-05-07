<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorPersonnelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;

class EditContractorPersonnel extends EditRecord
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected static ?string $title = 'Firm Personnel Registration';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $nameParts = explode(' ', (string) $this->record->name, 2);

        $data['first_name'] = $data['first_name'] ?: ($nameParts[0] ?? null);
        $data['last_name'] = $data['last_name'] ?: ($nameParts[1] ?? null);
        $data['designation'] = $data['designation'] ?: $this->record->position;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $name = trim($data['first_name'].' '.$data['last_name']);

        $userData = [
            'name' => $name,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $userData['password'] = $data['password'];
        }

        $record->user->update($userData);

        $record->user->assignRole(RoleAndPermissions::CONTRACTOR_PERSONNEL);

        $record->update([
            'contractor_id' => $data['contractor_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'other_name' => $data['other_name'] ?? null,
            'name' => $name,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'designation' => $data['designation'] ?? null,
            'position' => $data['designation'] ?? null,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('back')->label('← Back')->url(static::getResource()::getUrl())->link()->color('primary'), Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
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
