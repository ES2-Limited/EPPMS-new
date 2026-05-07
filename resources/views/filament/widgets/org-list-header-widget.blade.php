<x-filament-widgets::widget>
    <div style="display:flex; align-items:center; justify-content:space-between; gap:20px; width:100%; background:#ffffff; border-radius:12px; padding:20px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="display:flex; align-items:center; gap:16px; min-width:0;">
            @if ($organisation?->logo_url)
                <img src="{{ $organisation->logo_url }}" alt="{{ $organisation->name ?? 'ePPMS' }}" style="height:56px; width:56px; object-fit:contain; border-radius:10px; background:#f8fafc; padding:6px; border:1px solid #e5e7eb;">
            @else
                <div style="height:56px; width:56px; border-radius:10px; background:#0D1B3E; color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:16px;">EP</div>
            @endif

            <div style="min-width:0;">
                <div style="font-size:13px; font-weight:700; color:#5B5FC7; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px;">{{ $organisation?->name ?? 'ePPMS' }}</div>
                <h2 style="font-size:22px; line-height:1.2; font-weight:800; color:#0D1B3E; margin:0;">{{ $heading }}</h2>
                <p style="font-size:14px; line-height:1.5; color:#64748b; margin:6px 0 0;">{{ $subheading }}</p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
