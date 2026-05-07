<x-filament-panels::page>
    @php
        use App\Filament\Resources\MilestoneResource;
        use App\Filament\Resources\ProjectResource;
        use App\Filament\Resources\TaskResource;
        use App\Support\ProjectAccess;

        $project = $this->projectRecord;
        $canManage = ProjectAccess::canManageProject(auth()->user());
        $statusClass = [
            'pending' => 'bg-gray-100 text-gray-700',
            'in_progress' => 'bg-teal-100 text-teal-700',
            'done' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-700',
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold text-gray-950">{{ $project->name }} Project Milestones</h1>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="window.print()" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white">Print Report</button>
                <a href="{{ ProjectResource::getUrl('view', ['record' => $project]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background-color: #0D1B3E;">Back to Project</a>
                @if ($canManage)
                    <a href="{{ MilestoneResource::getUrl('create').'?project_id='.$project->id }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white">Add Project Milestone</a>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tasks</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->getMilestones() as $milestone)
                        @php
                            $tasks = $milestone->tasks->whereNull('deleted_at');
                            $totalTasks = $tasks->count();
                            $doneTasks = $tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100, 2) : 0;
                            $status = $progress >= 100 ? 'done' : ($progress > 0 ? 'in_progress' : 'pending');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950">{{ $milestone->name }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $milestone->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format($progress, 2) }}%</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass[$status] }}">{{ str($status)->replace('_', ' ')->headline() }}</span>
                                    <a href="{{ MilestoneResource::getUrl('report', ['record' => $milestone]) }}" class="rounded-md border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-700">Print</a>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold">{{ $totalTasks }}</span>
                                    <a href="{{ TaskResource::getUrl('index').'?milestone_id='.$milestone->id }}" class="rounded-md bg-teal-600 px-2 py-1 text-xs font-semibold text-white">View Tasks</a>
                                    @if ($canManage)
                                        <a href="{{ TaskResource::getUrl('create').'?milestone_id='.$milestone->id }}" class="rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white">Add Task</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <details class="relative inline-block">
                                    <summary class="cursor-pointer list-none rounded-md px-2 py-1 text-lg font-bold text-gray-500">...</summary>
                                    <div class="absolute right-0 z-10 mt-2 w-32 rounded-lg border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        <a href="{{ MilestoneResource::getUrl('edit', ['record' => $milestone]) }}" class="block rounded px-3 py-2 text-sm hover:bg-gray-50">Edit</a>
                                        @if ($canManage)
                                            <button type="button" wire:click="deleteMilestone({{ $milestone->id }})" wire:confirm="Delete this milestone?" class="block w-full rounded px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete</button>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No milestones added to this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
