<div class="space-y-4">
    <form wire:submit="uploadImages" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        {{ $this->imageUploadForm }}

        <x-filament::button type="submit" class="mt-4">
            Upload Images
        </x-filament::button>
    </form>

    @php($images = $this->record->images()->with('uploader')->latest()->get())

    @if ($images->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            No milestone images uploaded yet.
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($images as $image)
                <a href="{{ $image->url }}" target="_blank" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <img src="{{ $image->url }}" alt="{{ $image->original_name ?: $image->name }}" class="h-48 w-full object-cover transition group-hover:scale-105">
                    <div class="space-y-1 p-3 text-sm text-gray-600 dark:text-gray-300">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $image->original_name ?: 'Milestone image' }}</div>
                        <div>Uploaded by {{ $image->uploader?->name ?? 'Unknown' }}</div>
                        <div>{{ $image->created_at?->format('M d, Y H:i') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
