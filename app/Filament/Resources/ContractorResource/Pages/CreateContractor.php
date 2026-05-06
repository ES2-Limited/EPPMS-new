<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorResource;
use App\Mail\PersonnelWelcomeMail;
use App\Models\Contractor;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CreateContractor extends CreateRecord
{
    protected static string $resource = ContractorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['firm_name'] = $this->form->getRawState()['firm_name'] ?? null;
        $data['email'] = $this->form->getRawState()['email'] ?? null;
        $data['phone'] = $this->form->getRawState()['phone'] ?? null;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $password = Str::password(12);
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
