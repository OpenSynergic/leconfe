<div>
    @if($isEnabled ?? true)
        @once
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endonce
        <section class="downloads-chart mt-6 mb-8">
            <h2 class="pb-1 mb-4 text-base font-medium border-b border-b-slate-200">
                Download Statistics
            </h2>
            <div class="h-64 sm:h-72 w-full relative"
                x-data="{
                    chart: null,
                    initChart() {
                        const render = () => {
                            if (typeof Chart === 'undefined') {
                                setTimeout(render, 50);
                                return;
                            }
                            const ctx = $refs.canvas.getContext('2d');
                            if (this.chart) {
                                this.chart.destroy();
                            }
                            this.chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: @js($labels),
                                    datasets: [
                                        {
                                            label: 'Downloads',
                                            data: @js($values),
                                            backgroundColor: '#55a6cb',
                                            hoverBackgroundColor: '#3b8db3',
                                            borderRadius: 2,
                                            barPercentage: 0.5,
                                            categoryPercentage: 0.8
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Downloads: ' + context.parsed.y;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0,
                                                font: { size: 11 },
                                                color: '#64748b'
                                            },
                                            grid: {
                                                color: '#e2e8f0',
                                                drawBorder: false
                                            }
                                        },
                                        x: {
                                            ticks: {
                                                font: { size: 11 },
                                                color: '#64748b'
                                            },
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                            });
                        };
                        render();
                    }
                }"
                x-init="initChart()"
            >
                <canvas x-ref="canvas"></canvas>
            </div>
        </section>
    @endif
</div>
