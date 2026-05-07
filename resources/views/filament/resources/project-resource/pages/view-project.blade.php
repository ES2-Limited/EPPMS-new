<x-filament-panels::page>
@php
    $project   = $this->record;
    $progress  = $project->get_progress();
    $user      = auth()->user();

    $isOrgRole = $user?->hasAnyRole([
        \App\Constants\RoleAndPermissions::ADMIN,
        \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN,
        \App\Constants\RoleAndPermissions::MANAGEMENT_ADMIN,
        \App\Constants\RoleAndPermissions::DIRECTORATE_ADMIN,
        \App\Constants\RoleAndPermissions::REGIONAL_ADMIN,
        \App\Constants\RoleAndPermissions::DEPARTMENT_ADMIN,
        \App\Constants\RoleAndPermissions::HEAD_OF_UNIT,
        \App\Constants\RoleAndPermissions::ORGANIZATION_PERSONNEL,
        \App\Constants\RoleAndPermissions::AUDITOR,
    ]);

    $canManage = $user?->hasAnyRole([
        \App\Constants\RoleAndPermissions::ADMIN,
        \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN,
    ]);

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

    $milestonesUrl      = \App\Filament\Resources\MilestoneResource::getUrl('index', ['project_id' => $project->id]);
    $personnelUrl       = \App\Filament\Resources\ProjectResource\RelationManagers\ProjectPersonnelRelationManager::class;
    $images             = $project->recentTaskImages(5);
    $comments           = $project->chats()->with('sender')->oldest()->get();
    $milestoneCount     = $project->milestones()->whereNull('deleted_at')->count();
    $doneMilestones     = $project->milestones()->whereNull('deleted_at')->whereHas('tasks', fn($q) => $q->where('status', 'done'))->count();
    $personnelCount     = $project->projectPersonnel()->whereNull('deleted_at')->count();
    $endDate            = $project->end_date;
@endphp

<div class="flex flex-col lg:flex-row gap-6">

    {{-- ==================== LEFT COLUMN (60%) ==================== --}}
    <div class="w-full lg:w-3/5 flex flex-col gap-4">

        {{-- Back button --}}
        <div>
            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Back
            </a>
        </div>

        {{-- Heading + Action buttons --}}
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ $project->name }}
            </h1>

            <div class="flex flex-wrap gap-2 items-center">
                @if($progress === 100 && $isOrgRole)
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('report', ['record' => $project]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition">
                        <x-heroicon-o-document-chart-bar class="w-4 h-4" />
                        Project Report
                    </a>
                @endif
                @if($isOrgRole)
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <x-heroicon-o-briefcase class="w-4 h-4" />
                        All Projects
                    </a>
                @endif
                @if($canManage)
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('edit', ['record' => $project]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                       style="background-color:#5B5FC7;">
                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                        Edit Project
                    </a>
                @endif
            </div>
        </div>

        {{-- Project Details card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700">

                {{-- Status --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700 sm:col-span-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Status</span>
                    <span class="text-sm font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                        {{ $statusLabel[$project->status] ?? ucfirst($project->status) }}
                    </span>
                </div>

                {{-- Priority --}}
                @if($project->priority)
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Priority</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->priority }}</span>
                </div>
                @endif

                {{-- Type --}}
                @if($project->project_type)
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Type</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->project_type }}</span>
                </div>
                @endif

                {{-- Total Paid --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Total Paid</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        ₦{{ number_format((float)$project->total_paid, 2) }}
                    </span>
                </div>

                {{-- Total Left --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Total Left</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        ₦{{ number_format((float)$project->total_left, 2) }}
                    </span>
                </div>

                {{-- Location --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Location</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->office?->name ?? '—' }}</span>
                </div>

                {{-- Directorate --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Directorate</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->directorate?->name ?? '—' }}</span>
                </div>

                {{-- Contractor --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Contractor</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->contractor?->user?->name ?? '—' }}</span>
                </div>

                {{-- Consultant --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Consultant</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->consultant?->user?->name ?? '—' }}</span>
                </div>

                {{-- Award Date --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Award Date</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $project->award_date?->format('d M Y') ?? '—' }}
                    </span>
                </div>

                {{-- Duration --}}
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Duration</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $project->duration ? $project->duration.' - '.($project->duration_period ?? '') : '—' }}
                    </span>
                </div>

                {{-- Cost --}}
                <div class="flex items-start gap-3 px-4 py-3 sm:col-span-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400 min-w-[110px] pt-0.5">Contract Sum</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        ₦{{ number_format((float)$project->cost, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($project->description)
        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 p-4">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $project->description }}</p>
        </div>
        @endif

        {{-- Award Letter --}}
        @if($project->award_letter)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Award Letter</h4>
            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($project->award_letter) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <x-heroicon-o-document-text class="w-5 h-5 text-red-500" />
                View Award Letter
                <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5 opacity-60" />
            </a>
        </div>
        @endif

    </div>

    {{-- ==================== RIGHT COLUMN (40%) ==================== --}}
    <div class="w-full lg:w-2/5 flex flex-col gap-4">

        {{-- Time Left countdown --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Time Remaining</h4>
            @php
                $timeLeft = $project->time_left;
            @endphp
            <div class="text-xl font-bold {{ $timeLeft === 'Expired' ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                {{ $timeLeft ?? '—' }}
            </div>
            @if($project->award_date && $endDate)
                <p class="text-xs text-gray-400 mt-1">
                    {{ $project->award_date->format('d M Y') }} → {{ $endDate->format('d M Y') }}
                </p>
            @endif
        </div>

        {{-- Project Gallery --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Project Gallery</h4>
            @if($images->isEmpty())
                <p class="text-sm text-gray-400">No task images yet.</p>
            @else
                <div class="grid grid-cols-2 gap-2">
                    @foreach($images as $image)
                        <a href="{{ $image->url }}" target="_blank" class="group overflow-hidden rounded-lg border border-gray-100 dark:border-gray-700">
                            <img src="{{ $image->url }}"
                                 alt="{{ $image->original_name ?? 'image' }}"
                                 class="h-28 w-full object-cover group-hover:scale-105 transition">
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tabs: Milestones | Comments | Personnels --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
             x-data="{ tab: 'milestones' }">

            {{-- Tab headers --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button @click="tab = 'milestones'"
                        :class="tab === 'milestones' ? 'border-b-2 border-primary-600 text-primary-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 px-3 py-3 text-sm transition">
                    Milestones
                </button>
                <button @click="tab = 'comments'"
                        :class="tab === 'comments' ? 'border-b-2 border-primary-600 text-primary-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 px-3 py-3 text-sm transition">
                    Comments
                </button>
                <button @click="tab = 'personnels'"
                        :class="tab === 'personnels' ? 'border-b-2 border-primary-600 text-primary-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 px-3 py-3 text-sm transition">
                    Personnels
                </button>
            </div>

            {{-- Milestones tab --}}
            <div x-show="tab === 'milestones'" class="p-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $milestoneCount }}</span> milestone(s) total
                    </span>
                </div>
                <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('index', ['project_id' => $project->id]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition w-full justify-center"
                   style="background-color:#5B5FC7;">
                    <x-heroicon-o-flag class="w-4 h-4" />
                    View Project Milestones
                </a>
            </div>

            {{-- Comments tab --}}
            <div x-show="tab === 'comments'" class="p-4">
                {{-- Comment thread --}}
                <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                    @forelse($comments as $comment)
                        <div class="flex gap-2">
                            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold uppercase">
                                {{ substr($comment->sender?->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $comment->sender?->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $comment->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No comments yet.</p>
                    @endforelse
                </div>
                {{-- Post comment form --}}
                <form wire:submit="postComment" class="space-y-2">
                    {{ $this->commentForm }}
                    <x-filament::button type="submit" size="sm">Publish</x-filament::button>
                </form>
            </div>

            {{-- Personnels tab --}}
            <div x-show="tab === 'personnels'" class="p-4 space-y-3">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-bold text-gray-900 dark:text-white">{{ $personnelCount }}</span> personnel assigned
                </div>
                @if($canManage)
                    <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}#relation-managers"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition w-full justify-center"
                       style="background-color:#5B5FC7;">
                        <x-heroicon-o-users class="w-4 h-4" />
                        View Project Personnels
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Relation Managers (Milestones + Personnel) below --}}
<div id="relation-managers" class="mt-6">
    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
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
