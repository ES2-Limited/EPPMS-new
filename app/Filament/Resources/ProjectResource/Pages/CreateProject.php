<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ProjectResource;
use App\Mail\ProjectCreatedMail;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        try {
            $projectUrl = ProjectResource::getUrl('view', ['record' => $this->record]);

            User::role([RoleAndPermissions::ADMIN, RoleAndPermissions::ORGANIZATION_ADMIN])
                ->each(fn (User $user) => Mail::to($user)->queue(new ProjectCreatedMail($this->record, $projectUrl)));
        } catch (Throwable $exception) {
            Log::warning('Project created email dispatch failed.', [
                'project_id' => $this->record->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
