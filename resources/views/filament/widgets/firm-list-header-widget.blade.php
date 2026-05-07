<x-filament-widgets::widget>
    <div style="display:grid; gap:12px; width:100%;">
        @if ($backLabel)
            <div>
                <a href="{{ $backUrl ?? '#' }}" style="color:#5B5FC7; font-size:14px; font-weight:700; text-decoration:none;">&larr; {{ $backLabel }}</a>
            </div>
        @endif

        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; width:100%;">
            <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                @if ($organisation?->logo_url)
                    <img src="{{ $organisation->logo_url }}" alt="{{ $organisation->name ?? 'ePPMS' }}" style="height:40px; width:40px; border-radius:9999px; object-fit:cover; border:1px solid #e5e7eb; background:#ffffff;">
                @else
                    <div style="height:40px; width:40px; border-radius:9999px; background:#0D1B3E; color:#ffffff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800;">EP</div>
                @endif

                <h1 style="font-size:20px; line-height:1.2; color:#111827; font-weight:800; margin:0;">{{ $heading }}</h1>
            </div>

            <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-end; gap:10px;">
                <a href="{{ $addUrl }}" style="display:inline-flex; align-items:center; gap:8px; border-radius:8px; background:#16A34A; color:#ffffff; font-size:14px; font-weight:700; padding:10px 16px; text-decoration:none;">
                    <x-heroicon-o-plus style="height:18px; width:18px;" />
                    <span>{{ $addLabel }}</span>
                </a>

                @if ($printUrl)
                    <a href="{{ $printUrl }}" target="_blank" style="display:inline-flex; align-items:center; gap:8px; border-radius:8px; background:#0D1B3E; color:#ffffff; font-size:14px; font-weight:700; padding:10px 16px; text-decoration:none;">
                        <x-heroicon-o-printer style="height:18px; width:18px;" />
                        <span>Print Report</span>
                    </a>
                @endif
            </div>
        </div>

        <p style="font-size:14px; line-height:1.5; color:#6B7280; margin:0;">{{ $subheading }}</p>
    </div>
</x-filament-widgets::widget>
