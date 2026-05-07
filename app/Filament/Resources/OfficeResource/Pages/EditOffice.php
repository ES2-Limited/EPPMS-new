<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditOffice extends EditRecord
{
    protected static string $resource = OfficeResource::class;

    protected static ?string $title = 'Office Registration';

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
