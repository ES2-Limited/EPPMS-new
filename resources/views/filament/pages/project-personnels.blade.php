<x-filament-panels::page>
    @php
        $project = $this->projectRecord;
        $canManage = \App\Support\ProjectAccess::canManageProject(auth()->user());
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold text-gray-950">{{ $project->name }} Project Personnels</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background-color: #5B5FC7;">View Project</a>
                @if ($canManage)
                    <a href="{{ route('filament.admin.pages.add-project-personnel', ['project' => $project->ulid]) }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white">Add Project Personnel</a>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Project</th>
                        <th class="px-4 py-3">Project Personnel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->getProjectPersonnels() as $assignment)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950">{{ $assignment->project?->name }}</td>
                            <td class="px-4 py-3">{{ $assignment->personnel?->user?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">No personnels added to this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
