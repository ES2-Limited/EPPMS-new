@php($images = $this->record->recentTaskImages())

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <h2 class="mb-4 text-base font-semibold text-gray-950 dark:text-white">Project Gallery</h2>

    @if ($images->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            No task images uploaded yet.
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($images as $image)
                <a href="{{ $image->url }}" target="_blank" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <img src="{{ $image->url }}" alt="{{ $image->original_name ?: $image->name }}" class="h-56 w-full object-cover transition group-hover:scale-105">
                    <div class="space-y-1 p-3 text-sm text-gray-600 dark:text-gray-300">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $image->task?->name ?? 'Task image' }}</div>
                        <div>Uploaded {{ $image->created_at?->format('M d, Y H:i') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
