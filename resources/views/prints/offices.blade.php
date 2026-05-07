@php($organisation = app_organisation())

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Office Location Report</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #ffffff; color: #111827; font-family: Arial, sans-serif; font-size: 13px; }
        .page { max-width: 1120px; margin: 0 auto; padding: 32px; }
        .actions { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
        .button { border: 0; border-radius: 8px; background: #5B5FC7; color: #ffffff; cursor: pointer; font-weight: 700; padding: 10px 16px; text-decoration: none; }
        .button.secondary { background: #0D1B3E; }
        .header { align-items: center; border-bottom: 2px solid #e5e7eb; display: flex; justify-content: space-between; gap: 20px; padding-bottom: 20px; }
        .brand { align-items: center; display: flex; gap: 14px; }
        .logo { height: 64px; width: 64px; object-fit: contain; }
        h1 { font-size: 24px; margin: 0 0 6px; }
        .muted { color: #6b7280; }
        table { border-collapse: collapse; margin-top: 24px; width: 100%; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #374151; font-size: 12px; text-transform: uppercase; }
        @media print { .no-print { display: none !important; } body { background: #ffffff !important; } .page { max-width: none; padding: 0; } @page { margin: 16mm; } }
    </style>
</head>
<body>
    <main class="page">
        <div class="actions no-print">
            <a class="button secondary" href="{{ \App\Filament\Resources\OfficeResource::getUrl('index') }}">Go back</a>
            <button class="button" type="button" onclick="window.print()">Print Report</button>
        </div>

        <header class="header">
            <div>
                <h1>Office Location Report</h1>
                <div class="muted">Generated {{ now()->format('M d, Y H:i') }}</div>
            </div>
            <div class="brand">
                @if ($organisation?->logo_url)
                    <img class="logo" src="{{ $organisation->logo_url }}" alt="{{ $organisation->name ?? 'ePPMS' }}">
                @endif
                <div>
                    <strong>{{ $organisation?->name ?? 'ePPMS' }}</strong><br>
                    <span class="muted">{{ $organisation?->email }}</span>
                </div>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Office Type</th>
                    <th>State</th>
                    <th>L.G.A.</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($offices as $office)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $office->name ?? '-' }}</td>
                        <td>{{ $office->type ?? '-' }}</td>
                        <td>{{ $office->state ?? '-' }}</td>
                        <td>{{ $office->lga ?? '-' }}</td>
                        <td>{{ $office->address ?? '-' }}</td>
                        <td>{{ $office->email ?? '-' }}</td>
                        <td>{{ $office->phone ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">No offices recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
