<x-filament-panels::page>
    @php($organization = $this->record)

    <div class="space-y-6">
        @include('filament.components.organisation-stats-grid', compact('officeCount', 'directorateCount', 'departmentCount', 'unitCount', 'personnelCount'))

        <div style="background:#ffffff; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Registered Organization Information</h2>

            <div style="height:1px; background:#e5e7eb; margin:20px 0;"></div>

            <div style="display:grid; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; width:100%;">
                    <span style="font-size:14px; font-weight:600; color:#6b7280;">Organization Name:</span>
                    <span style="font-size:14px; font-weight:700; color:#111827; text-align:right;">{{ $organization->name ?? '-' }}</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; width:100%;">
                    <span style="font-size:14px; font-weight:600; color:#6b7280;">Organization Email:</span>
                    <span style="font-size:14px; font-weight:700; color:#111827; text-align:right;">{{ $organization->email ?? '-' }}</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; width:100%;">
                    <span style="font-size:14px; font-weight:600; color:#6b7280;">Website:</span>
                    <span style="font-size:14px; font-weight:700; color:#111827; text-align:right;">{{ $organization->website ?? '-' }}</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; width:100%;">
                    <span style="font-size:14px; font-weight:600; color:#6b7280;">Created:</span>
                    <span style="font-size:14px; font-weight:700; color:#111827; text-align:right;">{{ $organization->created_at?->diffForHumans() ?? '-' }}</span>
                </div>
            </div>

            <div style="height:1px; background:#e5e7eb; margin:20px 0;"></div>

            <div style="display:flex; justify-content:center; align-items:center; gap:12px;">
                <a href="{{ \App\Filament\Resources\OrganizationResource::getUrl('edit', ['record' => $organization]) }}" style="display:inline-flex; align-items:center; background:#5B5FC7; color:#ffffff; padding:10px 32px; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none; margin-right:12px;">Edit</a>
                <a href="{{ \App\Filament\Resources\OrganizationResource::getUrl('print', ['record' => $organization]) }}" target="_blank" style="display:inline-flex; align-items:center; background:#0D1B3E; color:#ffffff; padding:10px 32px; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none;">Print</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
