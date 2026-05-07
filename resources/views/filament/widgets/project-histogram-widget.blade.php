<x-filament-widgets::widget>
    @php($chartId = 'project-progress-histogram-'.$this->getId())

    <div class="eppms-dashboard-card space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-2xl font-bold text-gray-950">Projects</h2>

                <div class="flex flex-wrap gap-3">
                    <div>
                        <label class="mb-1 block text-[13px] font-medium text-gray-500" for="{{ $chartId }}-directorate">Filter by Directorate</label>
                        <select id="{{ $chartId }}-directorate" wire:model.live="directorateId" class="min-w-48 rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="all">All Directorates</option>
                            @foreach ($directorates as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[13px] font-medium text-gray-500" for="{{ $chartId }}-office">Filter by Office</label>
                        <select id="{{ $chartId }}-office" wire:model.live="officeId" class="min-w-40 rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="all">All Offices</option>
                            @foreach ($offices as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs font-bold text-gray-900">
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-red-900" style="background-color: #B91C1C;"></span>Less than 25% - Red</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-orange-900" style="background-color: #C2410C;"></span>26% - 50% - Orange</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-yellow-900" style="background-color: #A16207;"></span>51% - 70% - Yellow</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-blue-900" style="background-color: #1D4ED8;"></span>71% - 89% - Blue</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm border border-green-900" style="background-color: #15803D;"></span>90% - 100% - Green</span>
            </div>

            <div class="relative min-h-[400px] w-full rounded-xl border border-gray-200 bg-white p-4">
                @if ($projects->isEmpty())
                    <div class="flex min-h-[400px] items-center justify-center text-sm font-medium text-gray-500">
                        No projects with progress
                    </div>
                @else
                    <canvas id="{{ $chartId }}" class="min-h-[400px] w-full"></canvas>
                @endif
            </div>
    </div>

    @if ($projects->isNotEmpty())
        <script>
            (() => {
                const chartId = @json($chartId);
                const labels = @json($labels);
                const values = @json($values);
                const colours = @json($colours);

                window.eppmsProjectCharts = window.eppmsProjectCharts || {};

                const drawChart = () => {
                    const canvas = document.getElementById(chartId);

                    if (!canvas || !window.Chart) {
                        return;
                    }

                    if (window.eppmsProjectCharts[chartId]) {
                        window.eppmsProjectCharts[chartId].destroy();
                    }

                    window.eppmsProjectCharts[chartId] = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Progress %',
                                data: values,
                                backgroundColor: colours,
                                borderRadius: 6,
                                maxBarThickness: 48,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: { color: '#111827', font: { weight: '700' }, stepSize: 10, callback: value => `${value}%` },
                                    grid: { color: '#CBD5E1' },
                                },
                                x: {
                                    ticks: { color: '#111827', font: { weight: '700' }, maxRotation: 45, minRotation: 45 },
                                    grid: { color: '#E5E7EB' },
                                },
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: item => `${item.parsed.y}% complete` } },
                            },
                        },
                    });
                };

                const ensureChartJs = () => {
                    if (window.Chart) {
                        drawChart();

                        return;
                    }

                    if (document.getElementById('chart-js-cdn')) {
                        document.getElementById('chart-js-cdn').addEventListener('load', drawChart, { once: true });

                        return;
                    }

                    const script = document.createElement('script');
                    script.id = 'chart-js-cdn';
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.addEventListener('load', drawChart, { once: true });
                    document.head.appendChild(script);
                };

                ensureChartJs();
                document.addEventListener('livewire:navigated', ensureChartJs);

                if (window.Livewire) {
                    window.Livewire.hook('morph.updated', ({ el }) => {
                        if (el.querySelector && el.querySelector(`#${chartId}`)) {
                            setTimeout(ensureChartJs, 0);
                        }
                    });
                }
            })();
        </script>
    @endif
</x-filament-widgets::widget>
