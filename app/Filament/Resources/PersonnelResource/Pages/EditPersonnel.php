<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\PersonnelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;

class EditPersonnel extends EditRecord
{
    protected static string $resource = PersonnelResource::class;

    protected static ?string $title = 'Personnel Registration';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $nameParts = explode(' ', (string) $this->record->user?->name, 2);

        $data['first_name'] = $data['first_name'] ?: ($nameParts[0] ?? null);
        $data['last_name'] = $data['last_name'] ?: ($nameParts[1] ?? null);
        $data['email'] = $this->record->user?->email;
        $data['phone'] = $data['phone'] ?: $this->record->user?->phone;
        $data['role'] = $this->record->user?->roles
            ->pluck('name')
            ->first(fn (string $role): bool => in_array($role, RoleAndPermissions::SYSTEM_ROLES, true));

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = [
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $userData['password'] = $data['password'];
        }

        $record->user->update($userData);

        $record->user->roles
            ->pluck('name')
            ->filter(fn (string $role): bool => in_array($role, RoleAndPermissions::SYSTEM_ROLES, true) && $role !== $data['role'])
            ->each(fn (string $role) => $record->user->removeRole($role));

        if (! $record->user->hasRole($data['role'])) {
            $record->user->assignRole($data['role']);
        }

        $record->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'other_name' => $data['other_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'designation' => $data['designation'] ?? null,
            'directorate_id' => $data['directorate_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'office_id' => $data['office_id'] ?? null,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('goBack')->label('Go back')->url(static::getResource()::getUrl())->color('primary'), Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }

    protected function getFormActions(): array
    {
        return [$this->getSaveFormAction()->label('Save')];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }
}
