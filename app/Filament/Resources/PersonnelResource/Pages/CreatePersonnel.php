<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Filament\Resources\PersonnelResource;
use App\Mail\PersonnelWelcomeMail;
use App\Models\Personnel;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreatePersonnel extends CreateRecord
{
    protected static string $resource = PersonnelResource::class;

    protected static ?string $title = 'Personnel Registration';

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('goBack')->label('Go back')->url(static::getResource()::getUrl())->color('primary')];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()->label('Go back'),
            $this->getCreateFormAction()->label('Save'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $password = $data['password'];
        $role = $data['role'];

        $personnel = DB::transaction(function () use ($data, $password, $role): Personnel {
            $user = User::query()->create([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $password,
            ]);

            $user->assignRole($role);

            return Personnel::query()->create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'other_name' => $data['other_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'designation' => $data['designation'] ?? null,
                'directorate_id' => $data['directorate_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'office_id' => $data['office_id'] ?? null,
            ]);
        });

        try {
            Mail::to($personnel->user)->send(new PersonnelWelcomeMail($personnel->user, $password));
        } catch (Throwable $exception) {
            Log::warning('Personnel welcome email failed.', [
                'personnel_id' => $personnel->id,
                'email' => $personnel->user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        return $personnel;
    }
}
