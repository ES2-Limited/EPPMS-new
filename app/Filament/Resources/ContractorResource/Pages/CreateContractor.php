<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorResource;
use App\Mail\PersonnelWelcomeMail;
use App\Models\Contractor;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateContractor extends CreateRecord
{
    protected static string $resource = ContractorResource::class;

    protected static ?string $title = 'Firm Registration';

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('goBack')->label('Go back')->url(static::getResource()::getUrl())->color('primary')];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Save'),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->extraAttributes(['style' => 'background-color: #FCE8EC; color: #9F1239;']),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['firm_name'] = $this->form->getRawState()['firm_name'] ?? null;
        $data['email'] = $this->form->getRawState()['email'] ?? null;
        $data['phone'] = $this->form->getRawState()['phone'] ?? null;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $password = $data['password'];
        $role = ((int) $data['firm_type_id'] === Contractor::TYPE_CONSULTANT) ? RoleAndPermissions::CONSULTANT : RoleAndPermissions::CONTRACTOR;

        $contractor = DB::transaction(function () use ($data, $password, $role): Contractor {
            $user = User::query()->create([
                'name' => $data['firm_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $password,
            ]);

            $user->assignRole($role);

            return Contractor::query()->create([
                'user_id' => $user->id,
                'firm_type_id' => $data['firm_type_id'],
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'logo' => $data['logo'] ?? null,
            ]);
        });

        try {
            Mail::to($contractor->user)->send(new PersonnelWelcomeMail($contractor->user, $password, $contractor->firmTypeLabel()));
        } catch (Throwable $exception) {
            Log::warning('Firm welcome email failed.', ['contractor_id' => $contractor->id, 'email' => $contractor->user->email, 'error' => $exception->getMessage()]);
        }

        return $contractor;
    }
}
