<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Unit;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected static string $view = 'filament.resources.organization-resource.pages.view-organization';

    protected static ?string $title = 'Organization Details';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'officeCount' => Office::query()->count(),
            'directorateCount' => Directorate::query()->count(),
            'departmentCount' => Department::query()->count(),
            'unitCount' => Unit::query()->count(),
            'personnelCount' => Personnel::query()->count(),
        ];
    }
}
