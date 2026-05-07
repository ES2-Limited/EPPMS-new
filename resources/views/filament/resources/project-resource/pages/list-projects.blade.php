<x-filament-panels::page>
    @php $projects = $this->getProjects(); @endphp

    <div class="space-y-6">
        {{-- Row 1: filters --}}
        <div class="flex flex-wrap items-end justify-end gap-3">
            <div class="flex min-w-[220px] flex-col gap-1">
                <label class="whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">Filter by Directorate</label>
                <select wire:model.live="filterDirectorate"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Directorates</option>
                    @foreach ($this->getDirectorateOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex min-w-[220px] flex-col gap-1">
                <label class="whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">Filter by Office</label>
                <select wire:model.live="filterOffice"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Offices</option>
                    @foreach ($this->getOfficeOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex min-w-[220px] flex-col gap-1">
                <label class="whitespace-nowrap text-xs font-medium text-gray-500 dark:text-gray-400">Filter by Project Type</label>
                <select wire:model.live="filterProjectType"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Types</option>
                    @foreach ($this->getProjectTypeOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Row 2: heading and actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">List of Projects</h1>

            <div class="flex flex-wrap items-center justify-end gap-2">
                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}"
                   class="inline-flex items-center gap-1.5 rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">
                    <x-heroicon-o-document-chart-bar class="h-4 w-4" />
                    Project Report
                </a>

                @if ($this->canCreate())
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('create') }}"
                       class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold text-white transition"
                       style="background-color:#5B5FC7;">
                        <x-heroicon-o-plus class="h-4 w-4" />
                        Add a Project
                    </a>
                @endif
            </div>
        </div>

        {{-- Row 3: project card grid --}}
        @if ($projects->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white py-20 text-gray-400 dark:border-gray-700 dark:bg-gray-800">
                <x-heroicon-o-briefcase class="mb-4 h-16 w-16 opacity-30" />
                <p class="text-lg font-medium">No projects found</p>
                <p class="mt-1 text-sm">Adjust your filters or add a new project.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    @php
                        $progress = $project->get_progress();
                        $progressColour = $this->getProgressColour($progress);
                        $statusLabel = str($project->status)->replace('_', ' ')->title();
                        $hasMilestones = $project->hasMilestones();
                        $hasTasks = $project->hasTasks();
                    @endphp

                    <div wire:key="project-card-{{ $project->getKey() }}" class="flex min-h-[260px] flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <span class="text-xs text-gray-400">Created {{ $project->created_at?->diffForHumans() ?? '-' }}</span>
                            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}"
                               class="inline-flex items-center rounded-md px-3 py-1 text-xs font-semibold text-white"
                               style="background-color:#5B5FC7;">
                                View
                            </a>
                        </div>

                        <div class="mb-3 flex items-start justify-between gap-4">
                            <h2 class="min-w-0 flex-1 text-base font-bold leading-snug text-gray-900 dark:text-gray-100">{{ $project->name }}</h2>
                            <span class="shrink-0 text-xl font-bold" style="color:{{ $progressColour }};">{{ $progress }}%</span>
                        </div>

                        <div class="mb-3 flex items-center justify-between gap-4 text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Status</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $statusLabel }}</span>
                        </div>

                        <div class="mb-4 h-5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="flex h-5 items-center justify-center rounded-full text-[11px] font-bold text-white transition-all"
                                 style="width: {{ max($progress, 8) }}%; background-color: {{ $progressColour }};">
                                {{ $progress }}%
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <span class="font-bold text-gray-700 dark:text-gray-300">Milestone</span>
                                @if ($hasMilestones)
                                    <span class="font-bold text-green-600">Added</span>
                                @else
                                    <span class="font-bold text-red-600">Not Added</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <span class="font-bold text-gray-700 dark:text-gray-300">Task</span>
                                @if ($hasTasks)
                                    <span class="font-bold text-green-600">Added</span>
                                @else
                                    <span class="font-bold text-red-600">Not Added</span>
                                @endif
                            </div>
                        </div>

                        @if ($this->canEdit())
                            <div class="mt-auto pt-5">
                                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('edit', ['record' => $project]) }}"
                                   class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold text-white"
                                   style="background-color:#5B5FC7;">
                                    Edit
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Row 4: pagination --}}
            <div class="flex flex-col items-center gap-4 pt-2 md:grid md:grid-cols-3">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $projects->firstItem() }} - {{ $projects->lastItem() }} of {{ $projects->total() }} projects
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2">
                    @for ($page = 1; $page <= $projects->lastPage(); $page++)
                        @if ($page === $projects->currentPage())
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md px-3 text-sm font-semibold text-white" style="background-color:#5B5FC7;">{{ $page }}</span>
                        @else
                            <a href="{{ $projects->url($page) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-gray-300 bg-white px-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">{{ $page }}</a>
                        @endif
                    @endfor
                </div>

                <div></div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
