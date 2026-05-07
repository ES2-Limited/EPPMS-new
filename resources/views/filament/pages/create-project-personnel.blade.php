<x-filament-panels::page>
    @php($project = $this->projectRecord)

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ url('/admin/project/personnels/'.$project->ulid) }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" style="background-color: #5B5FC7;">Go back</a>
        </div>

        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-start">
                <button type="submit" class="rounded-lg px-5 py-2 text-sm font-semibold text-white" style="background-color: #5B5FC7;">Save</button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
