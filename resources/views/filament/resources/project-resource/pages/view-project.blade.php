<x-filament-panels::page>
@php
    $project = $this->record;
    $progress = $project->get_progress();
    $user = auth()->user();
    $isOrganizationRole = $user?->hasAnyRole(\App\Constants\RoleAndPermissions::SYSTEM_ROLES) ?? false;
    $canManagePersonnel = $user?->hasAnyRole([\App\Constants\RoleAndPermissions::ADMIN, \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN]) ?? false;
    $images = $project->recentTaskImages(5);
    $comments = $project->chats()->with('sender')->oldest()->get();
    $milestones = $project->milestones()->whereNull('deleted_at')->with('tasks')->get();
    $milestoneCount = $milestones->count();
    $completedMilestones = $milestones->filter(function ($milestone) {
        $tasks = $milestone->tasks->whereNull('deleted_at');

        return $tasks->count() > 0 && $tasks->where('status', 'done')->count() === $tasks->count();
    })->count();
    $personnelCount = $project->projectPersonnel()->whereNull('deleted_at')->count();
    $statusClasses = [
        'pending' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'done' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-700',
    ];
    $statusLabels = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'done' => 'Done',
        'completed' => 'Completed',
    ];
    $endDate = $project->end_date;
    $expired = $endDate?->isPast();
    $timeLeft = 'Expired';

    if ($endDate && ! $expired) {
        $diff = now()->diff($endDate);
        $months = ($diff->y * 12) + $diff->m;
        $timeLeft = $months.' months, '.$diff->d.' days left';
    }

    $detailRows = [
        ['icon' => 'heroicon-o-signal', 'label' => 'Status', 'value' => $statusLabels[$project->status] ?? str($project->status)->headline()->toString(), 'badge' => true],
        ['icon' => 'heroicon-o-flag', 'label' => 'Priority', 'value' => $project->priority ?: 'None'],
        ['icon' => 'heroicon-o-banknotes', 'label' => 'Total Paid', 'value' => number_format((float) $project->total_paid, 2)],
        ['icon' => 'heroicon-o-wallet', 'label' => 'Total Left', 'value' => number_format((float) $project->total_left, 2)],
        ['icon' => 'heroicon-o-map-pin', 'label' => 'Location', 'value' => $project->office?->name ?? '-'],
        ['icon' => 'heroicon-o-building-office-2', 'label' => 'Directorate', 'value' => $project->directorate?->name ?? '-'],
        ['icon' => 'heroicon-o-building-office', 'label' => 'Contractor', 'value' => $project->contractor?->user?->name ?? '-'],
        ['icon' => 'heroicon-o-calendar-days', 'label' => 'Award date', 'value' => $project->award_date?->format('Y-m-d') ?? '-'],
        ['icon' => 'heroicon-o-clock', 'label' => 'Duration', 'value' => $project->duration ? $project->duration.' - '.$project->duration_period : '-'],
        ['icon' => 'heroicon-o-currency-dollar', 'label' => 'Cost', 'value' => number_format((float) $project->cost, 2)],
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-950">&larr; Back</a>
        <h4 class="text-xl font-semibold text-gray-950">{{ $project->name }}</h4>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-3">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-950">Project details</h2>

                @if ($isOrganizationRole)
                    <div class="flex flex-wrap justify-end gap-2">
                        @if ($progress === 100)
                            <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('report', ['record' => $project]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background-color: #5B5FC7;">Project Report</a>
                        @endif
                        <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white">All Projects</a>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="space-y-4">
                    @foreach ($detailRows as $row)
                        <div class="flex items-center justify-between gap-5 border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                            <div class="flex items-center gap-3 text-sm font-medium text-gray-600">
                                <x-dynamic-component :component="$row['icon']" class="h-5 w-5 text-gray-400" />
                                <span>{{ $row['label'] }}</span>
                            </div>
                            <div class="text-right text-sm font-semibold text-gray-950">
                                @if ($row['badge'] ?? false)
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $row['value'] }}</span>
                                @else
                                    {{ $row['value'] }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl p-5" style="background-color: #F7F7F7;">
                <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800">
                    <x-heroicon-o-document-text class="h-5 w-5 text-gray-500" />
                    <span>Description</span>
                </div>
                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">{{ $project->description ?: '-' }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800">
                    <x-heroicon-o-paper-clip class="h-5 w-5 text-gray-500" />
                    <span>Attachments ({{ $project->award_letter ? 1 : 0 }})</span>
                </div>

                @if ($project->award_letter)
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($project->award_letter) }}" download class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <x-heroicon-o-document class="h-7 w-7 text-gray-500" />
                        <span>Award Letter</span>
                    </a>
                @else
                    <p class="text-sm text-gray-500">No attachments uploaded.</p>
                @endif
            </div>
        </div>

        <div class="space-y-5 border-gray-200 lg:col-span-2 lg:border-l lg:pl-6">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Time left</span>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $expired ? 'bg-rose-100 text-rose-700' : 'bg-pink-100 text-pink-700' }}">{{ $timeLeft }}</span>
            </div>

            <div>
                <h2 class="mb-3 text-lg font-semibold text-gray-950">Project gallery</h2>
                @if ($images->isEmpty())
                    <p class="rounded-xl border border-dashed border-gray-200 bg-white p-4 text-sm text-gray-500">No task images yet.</p>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($images as $image)
                            <a href="{{ $image->url }}" target="_blank">
                                <img src="{{ $image->url }}" alt="{{ $image->original_name ?? 'Project image' }}" class="h-32 w-full rounded-xl object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm" x-data="{ tab: 'comments' }">
                <div class="grid grid-cols-3 border-b border-gray-200 text-sm font-semibold">
                    <button type="button" class="px-3 py-3" :class="tab === 'milestones' ? 'text-[#5B5FC7]' : 'text-gray-500'" @click="tab = 'milestones'">Milestones</button>
                    <button type="button" class="px-3 py-3" :class="tab === 'comments' ? 'text-[#5B5FC7]' : 'text-gray-500'" @click="tab = 'comments'">Comments</button>
                    <button type="button" class="px-3 py-3" :class="tab === 'personnels' ? 'text-[#5B5FC7]' : 'text-gray-500'" @click="tab = 'personnels'">Personnels</button>
                </div>

                <div x-show="tab === 'milestones'" class="space-y-4 p-4">
                    <p class="text-sm font-medium text-gray-700">Milestones Completed: {{ $completedMilestones }}</p>
                    <p class="text-sm font-medium text-gray-700">Milestones Left: {{ max(0, $milestoneCount - $completedMilestones) }}</p>
                    <a href="{{ url('/admin/project/milestones/'.$project->ulid) }}" class="inline-flex rounded-lg border border-green-600 px-4 py-2 text-sm font-semibold text-green-700">View Project Milestones</a>
                </div>

                <div x-show="tab === 'comments'" class="space-y-4 p-4">
                    <div class="max-h-72 space-y-3 overflow-y-auto">
                        @forelse ($comments as $comment)
                            <div class="flex gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-700">{{ str($comment->sender?->name ?? 'U')->substr(0, 1)->upper() }}</div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-gray-950">{{ $comment->sender?->name ?? 'Unknown' }}</span>
                                        <span class="text-xs text-gray-400">{{ $comment->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $comment->message }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No comments yet.</p>
                        @endforelse
                    </div>

                    <form wire:submit="postComment" class="space-y-3">
                        <textarea wire:model="commentData.message" rows="3" placeholder="Enter your comment here" class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        <div class="flex items-center justify-between gap-3">
                            <x-heroicon-o-paper-clip class="h-5 w-5 text-gray-500" />
                            <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-[#5B5FC7]" style="background-color: #ECEBFF;">Publish</button>
                        </div>
                    </form>
                </div>

                <div x-show="tab === 'personnels'" class="space-y-4 p-4">
                    <p class="text-sm font-medium text-gray-700">Project Personnels: {{ $personnelCount }}</p>
                    @if ($canManagePersonnel)
                        <a href="{{ url('/admin/project/personnels/'.$project->ulid) }}" class="inline-flex rounded-lg border border-[#5B5FC7] px-4 py-2 text-sm font-semibold text-[#5B5FC7]">View Project Personnels</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>
