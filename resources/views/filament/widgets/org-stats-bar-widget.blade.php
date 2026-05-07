<x-filament-widgets::widget>
    @include('filament.components.organisation-stats-grid', [
        'officeCount' => $officeCount,
        'directorateCount' => $directorateCount,
        'departmentCount' => $departmentCount,
        'unitCount' => $unitCount,
        'personnelCount' => $personnelCount,
    ])
</x-filament-widgets::widget>
