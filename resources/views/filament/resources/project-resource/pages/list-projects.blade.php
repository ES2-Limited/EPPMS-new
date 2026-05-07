<x-filament-panels::page>

    {{-- Filters + Report button --}}
    <div class="flex flex-wrap gap-3 items-end mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        {{-- Directorate filter --}}
        <div class="flex flex-col gap-1 min-w-[180px]">
            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Directorate</label>
            <select wire:model.live="filterDirectorate"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <option value="">All Directorates</option>
                @foreach($this->getDirectorateOptions() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Office filter --}}
        <div class="flex flex-col gap-1 min-w-[180px]">
            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Office</label>
            <select wire:model.live="filterOffice"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <option value="">All Offices</option>
                @foreach($this->getOfficeOptions() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Project Type filter --}}
        <div class="flex flex-col gap-1 min-w-[160px]">
            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Project Type</label>
            <select wire:model.live="filterProjectType"
                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                <option value="">All Types</option>
                @foreach($this->getProjectTypeOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="ml-auto flex gap-2 items-end">
            {{-- Project Portfolio Report button --}}
            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <x-heroicon-o-document-chart-bar class="w-4 h-4" />
                Project Report
            </a>
        </div>
    </div>

    {{-- Card grid --}}
    @php $projects = $this->getProjects(); @endphp

    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <x-heroicon-o-briefcase class="w-16 h-16 mb-4 opacity-30" />
            <p class="text-lg font-medium">No projects found</p>
            <p class="text-sm mt-1">Adjust your filters or add a new project.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
                @php
                    $progress = $project->get_progress();
                    $progressColour = $this->getProgressColour($progress);

                    $statusColours = [
                        'pending'     => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'done'        => 'bg-green-100 text-green-800',
                        'completed'   => 'bg-purple-100 text-purple-800',
                    ];
                    $statusLabel = [
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'done'        => 'Done',
                        'completed'   => 'Completed',
                    ];
                    $badgeClass = $statusColours[$project->status] ?? 'bg-gray-100 text-gray-700';
                    $hasMilestones = $project->hasMilestones();
                    $hasTasks = $project->hasTasks();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden hover:shadow-md transition">

                    {{-- Card header --}}
                    <div class="flex items-center justify-between px-4 pt-3 pb-1">
                        <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                        <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-md text-xs font-semibold text-white"
                           style="background-color:#5B5FC7;">
                            <x-heroicon-o-eye class="w-3 h-3" />
                            View
                        </a>
                    </div>

                    {{-- Project name --}}
                    <div class="px-4 pt-1 pb-2">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">
                            {{ $project->name }}
                        </h3>
                        @if($project->project_type)
                            <span class="inline-block mt-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                                {{ $project->project_type }}
                            </span>
                        @endif
                    </div>

                    {{-- Progress --}}
                    <div class="px-4 pb-3 flex-1 flex flex-col gap-2">

                        {{-- Percentage + Status badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold" style="color: {{ $progressColour }}">
                                {{ $progress }}%
                            </span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                                {{ $statusLabel[$project->status] ?? ucfirst($project->status) }}
                            </span>
                        </div>

                        {{-- Progress bar --}}
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all"
                                 style="width: {{ $progress }}%; background-color: {{ $progressColour }};"></div>
                        </div>

                        {{-- Milestone + Task indicators --}}
                        <div class="flex gap-4 mt-1 text-xs text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                <span class="font-medium">Milestone:</span>
                                @if($hasMilestones)
                                    <span class="font-semibold text-green-600">Added</span>
                                @else
                                    <span class="font-semibold text-red-600">Not Added</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="font-medium">Task:</span>
                                @if($hasTasks)
                                    <span class="font-semibold text-green-600">Added</span>
                                @else
                                    <span class="font-semibold text-red-600">Not Added</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card footer --}}
                    @if($this->canEdit())
                        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('edit', ['record' => $project]) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <x-heroicon-o-pencil class="w-3 h-3" />
                                Edit
                            </a>
                            <span class="text-xs text-gray-400 truncate max-w-[140px]">
                                {{ $project->office?->name ?? '—' }}
                            </span>
                        </div>
                    @else
                        <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400">{{ $project->office?->name ?? '—' }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif

</x-filament-panels::page>
