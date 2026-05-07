<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Constants\RoleAndPermissions;
use App\Filament\Resources\ProjectResource;
use App\Models\Directorate;
use App\Models\Office;
use App\Models\Project;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.list-projects';

    protected static ?string $title = '';

    #[Url(as: 'directorate')]
    public ?string $filterDirectorate = null;

    #[Url(as: 'office')]
    public ?string $filterOffice = null;

    #[Url(as: 'type')]
    public ?string $filterProjectType = null;

    public function getProjects(): LengthAwarePaginator
    {
        $query = ProjectResource::getEloquentQuery();

        if ($this->filterDirectorate) {
            $query->where('directorate_id', $this->filterDirectorate);
        }

        if ($this->filterOffice) {
            $query->where('office_id', $this->filterOffice);
        }

        if ($this->filterProjectType) {
            $query->where('project_type', $this->filterProjectType);
        }

        return $query->latest()->paginate(9)->withQueryString();
    }

    public function getDirectorateOptions(): array
    {
        return Directorate::query()->pluck('name', 'id')->all();
    }

    public function getOfficeOptions(): array
    {
        return Office::query()->pluck('name', 'id')->all();
    }

    public function getProjectTypeOptions(): array
    {
        return array_combine(Project::PROJECT_TYPES, Project::PROJECT_TYPES);
    }

    public function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]) ?? false;
    }

    public function canEdit(): bool
    {
        return auth()->user()?->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
        ]) ?? false;
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getProgressColour(int $progress): string
    {
        return match (true) {
            $progress <= 25 => '#DC2626',
            $progress <= 50 => '#EA580C',
            $progress <= 70 => '#CA8A04',
            $progress <= 89 => '#2563EB',
            default => '#16A34A',
        };
    }
}
