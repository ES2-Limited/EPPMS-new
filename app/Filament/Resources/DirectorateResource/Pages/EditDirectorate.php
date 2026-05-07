<?php

namespace App\Filament\Resources\DirectorateResource\Pages;

use App\Filament\Resources\DirectorateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditDirectorate extends EditRecord
{
    protected static string $resource = DirectorateResource::class;

    protected static ?string $title = 'Directorate Registration';

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
        return Alignment::End;
    }
}
