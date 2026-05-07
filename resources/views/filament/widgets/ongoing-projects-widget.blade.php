<x-filament-widgets::widget>
    <div class="eppms-dashboard-card space-y-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-950">Ongoing Projects</h2>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white shadow-sm" style="background-color: #4F46E5;">{{ $count }}</span>
                </div>

                <a href="{{ \App\Filament\Resources\ProjectResource::getUrl('index') }}" class="text-sm font-bold hover:underline" style="color: #3730A3;">View All</a>
            </div>

            @if ($projects->isEmpty())
                <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm font-medium text-blue-700">
                    No ongoing projects found.
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($projects as $project)
                        @php($colour = $this->progressColour($project['progress']))

                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex items-center justify-between gap-3 text-xs font-medium text-gray-500">
                                <span>Creator: {{ $project['creator'] }}</span>
                                <span>{{ $project['created'] }}</span>
                            </div>

                            <h3 class="mb-4 line-clamp-2 min-h-12 text-base font-bold leading-6 text-gray-950">{{ $project['name'] }}</h3>

                            <div class="mb-2 text-right text-sm font-bold text-gray-950">{{ $project['progress'] }}%</div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full" style="width: {{ $project['progress'] }}%; background-color: {{ $colour }};"></div>
                            </div>

                            <a href="{{ $project['url'] }}" class="mt-5 inline-flex rounded-lg px-4 py-2 text-xs font-bold text-white shadow-sm" style="background-color: #4F46E5;">View</a>
                        </div>
                    @endforeach
                </div>
            @endif
    </div>
</x-filament-widgets::widget>
