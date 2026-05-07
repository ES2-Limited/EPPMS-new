@php
    $organisation = app_organisation();
    $project = $this->record->loadMissing(['contractor.user', 'consultant.user', 'milestones.tasks']);
    $awardLetterUrl = $project->award_letter ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->award_letter) : null;
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
                <h1 class="text-2xl font-bold">Project Completion Report</h1>
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
            <h2 class="mb-3 text-lg font-semibold">Project Summary</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div><span class="text-sm text-gray-500">Project</span><div class="font-medium">{{ $project->name }}</div></div>
                <div><span class="text-sm text-gray-500">Status</span><div class="font-medium">{{ str($project->status)->replace('_', ' ')->title() }}</div></div>
                <div><span class="text-sm text-gray-500">Progress</span><div class="font-medium">{{ $project->get_progress() }}%</div></div>
                <div><span class="text-sm text-gray-500">Contractor</span><div class="font-medium">{{ $project->contractor?->user?->name ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Consultant</span><div class="font-medium">{{ $project->consultant?->user?->name ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Award Date</span><div class="font-medium">{{ $project->award_date?->format('M d, Y') ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Cost</span><div class="font-medium">NGN {{ number_format((float) $project->cost, 2) }}</div></div>
                <div><span class="text-sm text-gray-500">Total Paid</span><div class="font-medium">NGN {{ number_format((float) $project->total_paid, 2) }}</div></div>
                <div><span class="text-sm text-gray-500">Total Left</span><div class="font-medium">NGN {{ number_format((float) $project->total_left, 2) }}</div></div>
                <div><span class="text-sm text-gray-500">Duration</span><div class="font-medium">{{ $project->duration ? $project->duration.' '.$project->duration_period : 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Time Left</span><div class="font-medium">{{ $project->time_left ?? 'N/A' }}</div></div>
                <div><span class="text-sm text-gray-500">Award Letter</span><div class="font-medium">@if ($awardLetterUrl)<a href="{{ $awardLetterUrl }}" target="_blank" class="text-primary-600">Open file</a>@else N/A @endif</div></div>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold">Milestones and Tasks</h2>

            @forelse ($project->milestones as $milestone)
                <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="font-semibold">{{ $milestone->name }}</div>
                        <div class="text-sm text-gray-500">Amount: NGN {{ number_format((float) $milestone->amount, 2) }}</div>
                    </div>

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
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700">No milestones recorded.</div>
            @endforelse
        </section>
    </div>
</x-filament-panels::page>
