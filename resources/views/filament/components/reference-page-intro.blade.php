@php
    use App\Models\Contractor;
    use App\Models\Department;
    use App\Models\Directorate;
    use App\Models\Office;
    use App\Models\Personnel;
    use App\Models\Unit;

    $cards = ($stats ?? 'org') === 'firm'
        ? [
            ['label' => 'Contractors', 'count' => Contractor::query()->where('firm_type_id', Contractor::TYPE_CONTRACTOR)->count(), 'color' => '#E6F7F5'],
            ['label' => 'Consultants', 'count' => Contractor::query()->where('firm_type_id', Contractor::TYPE_CONSULTANT)->count(), 'color' => '#ffffff'],
        ]
        : [
            ['label' => 'Office Locations', 'count' => Office::query()->count(), 'color' => '#ffffff'],
            ['label' => 'Directorates', 'count' => Directorate::query()->count(), 'color' => '#ffffff'],
            ['label' => 'Departments', 'count' => Department::query()->count(), 'color' => '#E6F7F5'],
            ['label' => 'Units', 'count' => Unit::query()->count(), 'color' => '#FCE8EC'],
            ['label' => 'Personnels', 'count' => Personnel::query()->count(), 'color' => '#E8F5EE'],
        ];
@endphp

<div class="space-y-4">
    <div class="grid gap-4 {{ ($stats ?? 'org') === 'firm' ? 'md:grid-cols-2' : 'md:grid-cols-5' }}">
        @foreach ($cards as $card)
            <div class="rounded-xl border border-gray-200 px-5 py-4 shadow-sm" style="background-color: {{ $card['color'] }};">
                <div class="text-2xl font-semibold text-gray-950">{{ $card['count'] }}</div>
                <div class="mt-1 text-sm font-medium text-gray-600">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    @if (filled($heading ?? null))
        <h2 class="text-base font-semibold text-gray-950">{{ $heading }}</h2>
    @endif

    @if (filled($subtitle ?? null))
        <p class="text-sm font-medium text-gray-600">{{ $subtitle }}</p>
    @endif
</div>
