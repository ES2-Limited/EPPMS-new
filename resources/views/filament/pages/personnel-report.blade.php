@php
    $organisation = app_organisation();
    $personnel = $this->getPersonnel();
@endphp

<x-filament-panels::page>
    <style>
        @media print {
            .fi-topbar, .fi-sidebar, .fi-header, .fi-breadcrumbs, .no-print { display: none !important; }
            .fi-main { margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
            .report-page { box-shadow: none !important; border: 0 !important; }
        }
    </style>

    <div class="no-print">
        <x-filament::button icon="heroicon-o-printer" onclick="window.print()">
            Print Report
        </x-filament::button>
    </div>

    <div class="report-page space-y-8 rounded-xl border border-gray-200 bg-white p-8 text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-6 dark:border-gray-700">
            <div>
                <h1 class="text-2xl font-bold">Personnel Report</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Generated {{ now()->format('M d, Y H:i') }}</p>
            </div>

            <div class="flex items-center gap-3 text-right">
                @if ($organisation?->logo)
                    <img src="{{ asset('storage/'.$organisation->logo) }}" alt="{{ $organisation->name }}" class="h-14 w-14 rounded object-contain">
                @endif
                <div>
                    <div class="font-semibold">{{ $organisation?->name ?? 'ePPMS' }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $organisation?->email }}</div>
                </div>
            </div>
        </div>

        <table class="w-full text-left text-sm">
            <thead class="border-y border-gray-200 text-gray-500 dark:border-gray-700">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Office</th>
                    <th class="p-3">Directorate</th>
                    <th class="p-3">Department</th>
                    <th class="p-3">Roles</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($personnel as $person)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="p-3">{{ $person->user?->name ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->user?->email ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->user?->phone ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->office?->name ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->directorate?->name ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->department?->name ?? 'N/A' }}</td>
                        <td class="p-3">{{ $person->user?->roles?->pluck('name')->map(fn ($role) => str($role)->replace('_', ' ')->title())->join(', ') ?: 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-3 text-gray-500">No personnel recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
