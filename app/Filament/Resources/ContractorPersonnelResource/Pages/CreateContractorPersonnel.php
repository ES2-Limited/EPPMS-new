<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorPersonnelResource;
use App\Mail\PersonnelWelcomeMail;
use App\Models\ContractorPersonnel;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateContractorPersonnel extends CreateRecord
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected static ?string $title = 'Firm Personnel Registration';

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('back')->label('← Back')->url(static::getResource()::getUrl())->link()->color('primary')];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Create'),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->extraAttributes(['style' => 'background-color: #FCE8EC; color: #9F1239;']),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $password = $data['password'];
        $name = trim($data['first_name'].' '.$data['last_name']);

        $personnel = DB::transaction(function () use ($data, $password, $name): ContractorPersonnel {
            $user = User::query()->create([
                'name' => $name,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $password,
            ]);

            $user->assignRole(RoleAndPermissions::CONTRACTOR_PERSONNEL);

            return ContractorPersonnel::query()->create([
                'user_id' => $user->id,
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
        });

        try {
            Mail::to($personnel->user)->send(new PersonnelWelcomeMail($personnel->user, $password, 'Contractor Personnel'));
        } catch (Throwable $exception) {
            Log::warning('Contractor personnel welcome email failed.', ['contractor_personnel_id' => $personnel->id, 'email' => $personnel->email, 'error' => $exception->getMessage()]);
        }

        return $personnel;
    }
}
