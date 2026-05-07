<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    protected static ?string $title = 'Organization Details';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Update'),
            $this->getCancelFormAction()->label('Cancel'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return OrganizationResource::getUrl('view', ['record' => $this->record]);
    }
}
