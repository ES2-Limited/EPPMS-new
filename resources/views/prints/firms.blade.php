@php($organisation = app_organisation())

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $organisation?->name ?? 'ePPMS' }} Firms</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #ffffff; color: #111827; font-family: Arial, sans-serif; font-size: 13px; }
        .page { max-width: 960px; margin: 0 auto; padding: 32px; }
        .actions { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 20px; }
        .button { border: 0; border-radius: 8px; background: #0D1B3E; color: #ffffff; cursor: pointer; font-weight: 700; padding: 10px 16px; text-decoration: none; }
        .button.print { background: #5B5FC7; }
        .report-header { text-align: center; margin-bottom: 28px; }
        .logo { display: block; height: 80px; max-width: 160px; object-fit: contain; margin: 0 auto 12px; }
        h1 { font-size: 24px; margin: 0 0 8px; font-weight: 800; }
        .muted { color: #6b7280; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #374151; font-size: 12px; text-transform: uppercase; }
        @media print { .no-print { display: none !important; } body { background: #ffffff !important; } .page { max-width: none; padding: 0; } @page { margin: 16mm; } }
    </style>
</head>
<body>
    <main class="page">
        <div class="actions no-print">
            <a class="button" href="{{ \App\Filament\Resources\ContractorResource::getUrl('index') }}">Back to Firms</a>
            <button class="button print" type="button" onclick="window.print()">Print</button>
        </div>

        <header class="report-header">
            @if ($organisation?->logo_url)
                <img class="logo" src="{{ $organisation->logo_url }}" alt="{{ $organisation->name ?? 'ePPMS' }}">
            @endif
            <h1>{{ $organisation?->name ?? 'ePPMS' }} Firms</h1>
            <div class="muted">Print Date: {{ now()->format('M d, Y') }}</div>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Firm Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($firms as $firm)
                    <tr>
                        <td>{{ $firm->user?->name ?? '-' }}</td>
                        <td>{{ $firm->phone ?? $firm->user?->phone ?? '-' }}</td>
                        <td>{{ $firm->user?->email ?? '-' }}</td>
                        <td>{{ $firm->firmTypeLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No firms registered yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
