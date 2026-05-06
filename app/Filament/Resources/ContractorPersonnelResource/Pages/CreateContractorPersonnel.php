<?php

namespace App\Filament\Resources\ContractorPersonnelResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ContractorPersonnelResource;
use App\Mail\PersonnelWelcomeMail;
use App\Models\ContractorPersonnel;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CreateContractorPersonnel extends CreateRecord
{
    protected static string $resource = ContractorPersonnelResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $password = Str::password(12);

        $personnel = DB::transaction(function () use ($data, $password): ContractorPersonnel {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $password,
            ]);

            $user->assignRole(RoleAndPermissions::CONTRACTOR_PERSONNEL);

            return ContractorPersonnel::query()->create([
                'user_id' => $user->id,
                'contractor_id' => $data['contractor_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
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
