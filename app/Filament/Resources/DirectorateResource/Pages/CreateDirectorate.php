<?php

namespace App\Filament\Resources\DirectorateResource\Pages;

use App\Filament\Resources\DirectorateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;

class CreateDirectorate extends CreateRecord
{
    protected static string $resource = DirectorateResource::class;

    protected static ?string $title = 'Directorate Registration';

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
        return Alignment::End;
    }
}
