@php
    $organisation = app_organisation();
    $milestone = $this->record->loadMissing(['project.contractor.user', 'project.consultant.user', 'tasks', 'images']);
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
                <h1 class="text-2xl font-bold">Milestone Report</h1>
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

        <section>
            <h2 class="mb-3 text-lg font-semibold">Milestone Details</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div><span class="text-sm text-gray-500">Milestone</span><div class="font-medium">{{ $milestone->name }}</div></div>
                <div><span class="text-sm text-gray-500">Amount</span><div class="font-medium">NGN {{ number_format((float) $milestone->amount, 2) }}</div></div>
                <div><span class="text-sm text-gray-500">Images</span><div class="font-medium">{{ $milestone->images->count() }}</div></div>
                <div class="md:col-span-3"><span class="text-sm text-gray-500">Description</span><div class="font-medium">{{ $milestone->description ?: 'N/A' }}</div></div>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold">Parent Project</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div><span class="text-sm text-gray-500">Project</span><div class="font-medium">{{ $milestone->project?->name ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Status</span><div class="font-medium">{{ str($milestone->project?->status ?? '')->replace('_', ' ')->title() }}</div></div>
                <div><span class="text-sm text-gray-500">Progress</span><div class="font-medium">{{ $milestone->project?->get_progress() ?? 0 }}%</div></div>
                <div><span class="text-sm text-gray-500">Contractor</span><div class="font-medium">{{ $milestone->project?->contractor?->user?->name ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Consultant</span><div class="font-medium">{{ $milestone->project?->consultant?->user?->name ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Cost</span><div class="font-medium">NGN {{ number_format((float) ($milestone->project?->cost ?? 0), 2) }}</div></div>
            </div>
        </section>

        @if ($milestone->images->isNotEmpty())
            <section>
                <h2 class="mb-3 text-lg font-semibold">Milestone Images</h2>
                <div class="grid gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($milestone->images->take(8) as $image)
                        <img src="{{ $image->url }}" alt="{{ $image->original_name ?: $image->name }}" class="h-28 w-full rounded-lg object-cover">
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <h2 class="mb-3 text-lg font-semibold">Tasks</h2>
            <table class="w-full text-left text-sm">
                <thead class="border-y border-gray-200 text-gray-500 dark:border-gray-700">
                    <tr>
                        <th class="p-3">Task</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Start</th>
                        <th class="p-3">Due</th>
                        <th class="p-3 text-right">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($milestone->tasks as $task)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="p-3">{{ $task->name }}</td>
                            <td class="p-3">{{ str($task->status)->replace('_', ' ')->title() }}</td>
                            <td class="p-3">{{ $task->start_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="p-3">{{ $task->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="p-3 text-right">NGN {{ number_format((float) $task->cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500">No tasks recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
</x-filament-panels::page>
