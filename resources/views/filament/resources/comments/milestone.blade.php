@php($comments = $this->record->chatMessages()->with('sender')->oldest()->get())

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <h2 class="mb-4 text-base font-semibold text-gray-950 dark:text-white">Comments</h2>

    <form wire:submit="postComment" class="mb-6 space-y-4">
        {{ $this->commentForm }}

        <x-filament::button type="submit">
            Post Comment
        </x-filament::button>
    </form>

    @if ($comments->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            No comments yet.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($comments as $comment)
                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                    <div class="mb-2 flex flex-wrap items-center gap-2 text-sm">
                        <span class="font-medium text-gray-950 dark:text-white">{{ $comment->sender?->name ?? 'Unknown' }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $comment->created_at?->diffForHumans() }}</span>
                    </div>
                    <div class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $comment->message }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
