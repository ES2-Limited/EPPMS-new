<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use App\Models\Organization;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    public function mount(): void
    {
        parent::mount();

        $organization = Organization::query()->first();

        $this->redirect($organization
            ? OrganizationResource::getUrl('view', ['record' => $organization])
            : OrganizationResource::getUrl('create'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => ! Organization::query()->exists()),
        ];
    }
}
