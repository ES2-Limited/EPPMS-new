<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use Filament\Resources\Pages\ViewRecord;

class PrintOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected static string $view = 'filament.resources.organization-resource.pages.print-organization';

    protected static ?string $title = 'Organization Details';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
