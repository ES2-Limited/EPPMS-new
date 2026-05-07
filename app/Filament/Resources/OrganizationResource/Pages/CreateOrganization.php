<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use App\Models\Organization;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected static ?string $title = 'Organization Details';

    public function mount(): void
    {
        if ($organization = Organization::query()->first()) {
            $this->redirect(OrganizationResource::getUrl('view', ['record' => $organization]));

            return;
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Create'),
            $this->getCancelFormAction()->label('Cancel'),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return Organization::query()->create($data);
    }

    protected function getRedirectUrl(): string
    {
        return OrganizationResource::getUrl('view', ['record' => $this->record]);
    }
}
