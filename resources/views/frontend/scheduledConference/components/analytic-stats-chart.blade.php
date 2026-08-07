<div class="card bg-base-100 shadow-xl border border-base-200 p-6 my-8">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-base-content">Conference Insights - Engagement Metrics</h2>
            <p class="text-sm text-base-content/70 mt-1">
                A comprehensive overview of scholarly reach and content interaction for the current conference series.
            </p>
        </div>

        {{-- Range Filter Switcher --}}
        <div class="join bg-base-200 p-1 rounded-btn self-start md:self-auto">
            <button wire:click="setRange('6 Months')" class="join-item btn btn-xs {{ $range === '6 Months' ? 'btn-primary' : 'btn-ghost' }}">6 Months</button>
            <button wire:click="setRange('12 Months')" class="join-item btn btn-xs {{ $range === '12 Months' ? 'btn-primary' : 'btn-ghost' }}">12 Months</button>
            <button wire:click="setRange('Overall')" class="join-item btn btn-xs {{ $range === 'Overall' ? 'btn-primary' : 'btn-ghost' }}">Overall</button>
        </div>
    </div>

    {{-- Access Volume Widget (BAR CHART) --}}
    <div class="mb-8 p-4 bg-base-200/50 rounded-box">
        <h3 class="text-lg font-semibold mb-2">Access Volume</h3>
        <p class="text-xs text-base-content/60 mb-4">Comparing Abstract Views against full PDF Galley Downloads.</p>
        <div class="h-64 relative"
            x-data="{
                chart: null,
                initChart() {
                    if (typeof Chart === 'undefined') return;
                    const ctx = $refs.canvas.getContext('2d');
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @js($months),
                            datasets: [
                                {
                                    label: 'Abstract Views',
                                    data: @js($abstractViewsData),
                                    backgroundColor: '#3b82f6',
                                    borderRadius: 4
                                },
                                {
                                    label: 'PDF Galley Downloads',
                                    data: @js($galleyDownloadsData),
                                    backgroundColor: '#10b981',
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0 }
                                }
                            }
                        }
                    });
                }
            }"
            x-init="initChart()"
            x-effect="initChart()"
        >
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>

    {{-- 3 Information Compliance Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-base-200">
        <div class="p-4 bg-base-200/30 rounded-box border border-base-200">
            <div class="flex items-center gap-2 text-primary font-semibold mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                Data Privacy First
            </div>
            <p class="text-xs text-base-content/70">
                Metrics are aggregated completely anonymously. No identifiable personal information is tracked or stored in this analytics dataset.
            </p>
        </div>

        <div class="p-4 bg-base-200/30 rounded-box border border-base-200">
            <div class="flex items-center gap-2 text-success font-semibold mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 001.946.806 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                COUNTER Compliant
            </div>
            <p class="text-xs text-base-content/70">
                We employ rigorous filtering against automated bots and crawlers to ensure statistics reflect genuine scholarly engagement.
            </p>
        </div>

        <div class="p-4 bg-base-200/30 rounded-box border border-base-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 text-info font-semibold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export Metrics
                </div>
                <p class="text-xs text-base-content/70">
                    Download detailed institutional usage reports in CSV format for deeper analysis.
                </p>
            </div>
            <button wire:click="downloadCsv" class="btn btn-outline btn-primary btn-xs mt-3 w-full flex items-center justify-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download CSV
            </button>
        </div>
    </div>
</div>
