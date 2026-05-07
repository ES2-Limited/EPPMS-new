<x-filament-panels::page>
@php
    $milestone = $this->record;
    $project   = $milestone->project;
    $comments  = $milestone->chatMessages()->with('sender')->oldest()->get();
    $images    = $milestone->images()->latest()->get();
    $canManage = auth()->user()?->hasAnyRole([
        \App\Constants\RoleAndPermissions::ADMIN,
        \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN,
    ]);
    $taskUrl   = \App\Filament\Resources\TaskResource::getUrl('create').
                 '?milestone_id='.$milestone->id;
@endphp

{{-- Back link --}}
<div class="mb-4">
    @if($project)
        <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('index', ['project_id' => $project->id]) }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            ← {{ $project->name }}
        </a>
    @endif
</div>

<div class="space-y-6">

    {{-- Milestone detail card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ $milestone->name }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Amount</span>
                <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format((float)$milestone->amount, 2) }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Start Date</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $milestone->start_date?->format('d M Y') ?? '—' }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">End Date</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $milestone->end_date?->format('d M Y') ?? '—' }}</span>
            </div>
            @if($project)
            <div class="sm:col-span-2 lg:col-span-3">
                <span class="block text-xs text-gray-400 mb-0.5">Project</span>
                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}"
                   class="font-semibold text-primary-600 hover:text-primary-700 hover:underline">
                    {{ $project->name }}
                </a>
            </div>
            @endif
            @if($milestone->description)
            <div class="sm:col-span-2 lg:col-span-3">
                <span class="block text-xs text-gray-400 mb-1">Description</span>
                <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $milestone->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Task list --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Tasks</h3>
            @if($canManage)
                <a href="{{ $taskUrl }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition"
                   style="background-color:#5B5FC7;">
                    <x-heroicon-o-plus class="w-3.5 h-3.5" />
                    Add Task
                </a>
            @endif
        </div>

        @php $tasks = $milestone->tasks()->whereNull('deleted_at')->get(); @endphp

        @if($tasks->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400">No tasks yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Start Date</th>
                            <th class="px-4 py-3 text-left">Due Date</th>
                            <th class="px-4 py-3 text-right">Cost</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($tasks as $task)
                            @php
                                $statusClass = $task->status === 'done'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-yellow-100 text-yellow-800';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $task->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $task->start_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $task->due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-medium">₦{{ number_format((float)$task->cost, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <a href="{{ \App\Filament\Resources\TaskResource::getUrl('view', ['record' => $task]) }}"
                                           class="px-2 py-1 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition">
                                            View
                                        </a>
                                        @if($canManage)
                                            <a href="{{ \App\Filament\Resources\TaskResource::getUrl('edit', ['record' => $task]) }}"
                                               class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Edit
                                            </a>
                                        @endif
                                        @if($task->status !== 'done' && auth()->user()?->hasAnyRole([
                                            \App\Constants\RoleAndPermissions::ADMIN,
                                            \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN,
                                            \App\Constants\RoleAndPermissions::ORGANIZATION_PERSONNEL,
                                        ]))
                                            <button wire:click="markTaskDone({{ $task->id }})"
                                                    class="px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200 transition">
                                                Mark Done
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Image upload + gallery --}}
    @include('filament.resources.image-gallery.milestone')

    {{-- Comments --}}
    @include('filament.resources.comments.milestone')

    {{-- Relation managers (TasksRelationManager) --}}
    @php
        $relationManagers = $this->getRelationManagers();
    @endphp
    @if(count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$this->activeRelationManager ?? array_key_first($relationManagers)"
            :content-tab-label="$this->getContentTabLabel()"
            :content-tab-icon="$this->getContentTabIcon()"
            :content-tab-position="$this->getContentTabPosition()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif

</div>
</x-filament-panels::page>
