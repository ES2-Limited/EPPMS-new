<?php

namespace App\Filament\Pages;

use App\Constants\RoleAndPermissions;
use App\Models\Personnel;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class PersonnelReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Personnel Report';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.personnel-report';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole([
            RoleAndPermissions::ADMIN,
            RoleAndPermissions::ORGANIZATION_ADMIN,
            RoleAndPermissions::MANAGEMENT_ADMIN,
            RoleAndPermissions::AUDITOR,
        ]);
    }

    public function getPersonnel(): Collection
    {
        return Personnel::query()
            ->with(['user.roles', 'office', 'directorate', 'department'])
            ->orderBy('id')
            ->get();
    }
}
