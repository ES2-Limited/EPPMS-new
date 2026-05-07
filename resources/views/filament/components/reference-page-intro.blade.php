@php
    use App\Models\Contractor;
    use App\Models\Department;
    use App\Models\Directorate;
    use App\Models\Office;
    use App\Models\Personnel;
    use App\Models\Unit;

    $cards = [
        ['label' => 'Contractors', 'count' => Contractor::query()->where('firm_type_id', Contractor::TYPE_CONTRACTOR)->count(), 'color' => '#E6F7F5'],
        ['label' => 'Consultants', 'count' => Contractor::query()->where('firm_type_id', Contractor::TYPE_CONSULTANT)->count(), 'color' => '#ffffff'],
    ];

    $officeCount = Office::query()->count();
    $directorateCount = Directorate::query()->count();
    $departmentCount = Department::query()->count();
    $unitCount = Unit::query()->count();
    $personnelCount = Personnel::query()->count();
@endphp

<div class="space-y-4">
    @if (($stats ?? 'org') === 'firm')
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
            @foreach ($cards as $card)
                <div style="background:{{ $card['color'] }}; border-radius:12px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    <div style="font-size:13px; color:#6b7280; margin-bottom:4px;">{{ $card['label'] }}</div>
                    <div style="font-size:28px; font-weight:700; color:#111827;">{{ $card['count'] }}</div>
                </div>
            @endforeach
        </div>
    @else
        @include('filament.components.organisation-stats-grid', compact('officeCount', 'directorateCount', 'departmentCount', 'unitCount', 'personnelCount'))
    @endif

    @if (filled($heading ?? null))
        <h2 class="text-base font-semibold text-gray-950">{{ $heading }}</h2>
    @endif

    @if (filled($subtitle ?? null))
        <p class="text-sm font-medium text-gray-600">{{ $subtitle }}</p>
    @endif
</div>
