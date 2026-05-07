<x-filament-panels::page>
@php
    $task      = $this->record;
    $milestone = $task->milestone;
    $project   = $milestone?->project;
    $user      = auth()->user();
    $canManage = $user?->hasAnyRole([
        \App\Constants\RoleAndPermissions::ADMIN,
        \App\Constants\RoleAndPermissions::ORGANIZATION_ADMIN,
    ]);
    $comments  = $task->chatMessages()->with('sender')->oldest()->get();
    $images    = $task->images()->latest()->get();
@endphp

{{-- Back link --}}
<div class="mb-4">
    @if($milestone)
        <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('view', ['record' => $milestone]) }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            ← {{ $milestone->name }}
        </a>
    @endif
</div>

<div class="space-y-6">

    {{-- Task header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $task->name }}</h2>
                @php
                    $statusClass = $task->status === 'done'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800';
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                    {{ ucfirst($task->status) }}
                </span>
            </div>

            {{-- Mark as Done button --}}
            @if($this->canMarkDone())
                <button wire:click="markAsDone"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition">
                    <x-heroicon-o-check-circle class="w-4 h-4" />
                    Mark as Done
                </button>
            @endif
        </div>

        {{-- Task details grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-5 text-sm">
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Start Date</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $task->start_date?->format('d M Y') ?? '—' }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Due Date</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $task->due_date?->format('d M Y') ?? '—' }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Created</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $task->created_at?->format('d M Y') ?? '—' }}</span>
            </div>
            @if($milestone)
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Milestone</span>
                <a href="{{ \App\Filament\Resources\MilestoneResource::getUrl('view', ['record' => $milestone]) }}"
                   class="font-medium text-primary-600 hover:underline">
                    {{ $milestone->name }}
                </a>
            </div>
            @endif
            @if($project)
            <div>
                <span class="block text-xs text-gray-400 mb-0.5">Project</span>
                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}"
                   class="font-medium text-primary-600 hover:underline">
                    {{ $project->name }}
                </a>
            </div>
            @endif
            @if($canManage)
            <div class="flex items-end">
                <a href="{{ \App\Filament\Resources\TaskResource::getUrl('edit', ['record' => $task]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition"
                   style="background-color:#5B5FC7;">
                    <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                    Edit Task
                </a>
            </div>
            @endif
        </div>

        @if($task->description)
        <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
            <span class="block text-xs text-gray-400 mb-1">Description</span>
            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $task->description }}</p>
        </div>
        @endif
    </div>

    {{-- Image Upload --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Upload Images</h3>
        <form wire:submit="uploadImages" class="space-y-3">
            {{ $this->imageUploadForm }}
            <x-filament::button type="submit" size="sm">Upload</x-filament::button>
        </form>
    </div>

    {{-- Image gallery --}}
    @if($images->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Image Gallery</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($images as $image)
                @php $imgUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($image->name); @endphp
                <a href="{{ $imgUrl }}" target="_blank"
                   class="group overflow-hidden rounded-lg border border-gray-100 dark:border-gray-700">
                    <img src="{{ $imgUrl }}"
                         alt="{{ $image->original_name ?? 'image' }}"
                         class="h-28 w-full object-cover group-hover:scale-105 transition">
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Comments --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Comments</h3>

        <div class="space-y-3 mb-4 max-h-72 overflow-y-auto">
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

        <form wire:submit="postComment" class="space-y-2">
            {{ $this->commentForm }}
            <x-filament::button type="submit" size="sm">Publish</x-filament::button>
        </form>
    </div>

</div>
</x-filament-panels::page>
