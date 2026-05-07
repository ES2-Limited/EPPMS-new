<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected static ?string $title = 'Unit Registration';

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('goBack')->label('Go back')->url(static::getResource()::getUrl())->color('primary'), Actions\ViewAction::make(), Actions\DeleteAction::make(), Actions\RestoreAction::make(), Actions\ForceDeleteAction::make()];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()->label('Go back'),
            $this->getSaveFormAction()->label('Save'),
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
