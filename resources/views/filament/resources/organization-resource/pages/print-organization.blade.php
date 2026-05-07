<x-filament-panels::page>
    @php($organization = $this->record)

    <div class="space-y-6 rounded-xl bg-white p-8 shadow-sm print:shadow-none">
        <h1 class="text-2xl font-bold text-gray-950">Organization Details</h1>

        <div class="space-y-3 text-sm">
            <div><strong>Organization Name:</strong> {{ $organization->name ?? '-' }}</div>
            <div><strong>Organization Email:</strong> {{ $organization->email ?? '-' }}</div>
            <div><strong>Website:</strong> {{ $organization->website ?? '-' }}</div>
            <div><strong>Created:</strong> {{ $organization->created_at?->diffForHumans() ?? '-' }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</x-filament-panels::page>
