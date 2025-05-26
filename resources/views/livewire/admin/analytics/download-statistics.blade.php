<x-layouts.app-layout>
    <div class="p-4 sm:p-6 bg-white rounded-lg shadow-md"
         x-data="downloadStats()"
         x-init="initCharts()"
         @stats-updated.window="updateAllCharts">
        
        <!-- Header and Date Filter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Download Statistics</h2>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <select wire:model="filterPeriod" class="block w-full md:w-48 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="7_days">Last 7 Days</option>
                    <option value="30_days">Last 30 Days</option>
                    <option value="90_days">Last 90 Days</option>
                    <option value="all_time">All Time</option>
                </select>
                
                @if($startDate)
                    <div class="text-sm text-gray-600 flex items-center">
                        {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-blue-800">Downloads in Selected Period</h3>
                <p class="text-3xl font-bold text-blue-600">
                    @isset($totalDownloadsPeriod)
                        {{ number_format($totalDownloadsPeriod) }}
                    @else
                        Loading...
                    @endisset
                </p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-green-800">Total Downloads (All Time)</h3>
                <p class="text-3xl font-bold text-green-600">
                    @isset($totalDownloadsAllTime)
                        {{ number_format($totalDownloadsAllTime) }}
                    @else
                        Loading...
                    @endisset
                </p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="space-y-8">
            <!-- Top Papers Chart -->
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Top Papers by Downloads</h3>
                <div class="h-80">
                    <canvas x-ref="topPapersChart" 
                            x-effect="renderChart('topPapersChart', 'bar', 
                                    $wire.downloadsPerPaper.map(p => p.paper_title), 
                                    $wire.downloadsPerPaper.map(p => p.downloads),
                                    {paper_ids: $wire.downloadsPerPaper.map(p => p.paper_id)})"></canvas>
                </div>
            </div>

            <!-- Downloads by Department Chart -->
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Downloads by Department</h3>
                <div class="h-80">
                    <canvas x-ref="departmentChart"
                            x-effect="renderChart('departmentChart', 'doughnut',
                                    $wire.downloadsByDepartment.map(d => d.department_name),
                                    $wire.downloadsByDepartment.map(d => d.downloads))"></canvas>
                </div>
            </div>

            <!-- Other Charts in Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Downloads by Exam Type -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Downloads by Exam Type</h3>
                    <div class="h-64">
                        <canvas x-ref="examTypeChart"
                                x-effect="renderChart('examTypeChart', 'pie',
                                        $wire.downloadsByExamType.map(e => e.exam_type),
                                        $wire.downloadsByExamType.map(e => e.downloads))"></canvas>
                    </div>
                </div>

                <!-- Downloads by Student Type -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Downloads by Student Type</h3>
                    <div class="h-64">
                        <canvas x-ref="studentTypeChart"
                                x-effect="renderChart('studentTypeChart', 'polarArea',
                                        $wire.downloadsByStudentType.map(s => s.student_type_name),
                                        $wire.downloadsByStudentType.map(s => s.downloads))"></canvas>
                    </div>
                </div>

                <!-- Downloads by Level -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Downloads by Level</h3>
                    <div class="h-64">
                        <canvas x-ref="levelChart"
                                x-effect="renderChart('levelChart', 'bar',
                                        $wire.downloadsByLevel.map(l => l.level_name),
                                        $wire.downloadsByLevel.map(l => l.downloads))"></canvas>
                    </div>
                </div>

                <!-- Yearly Comparison -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Yearly Downloads Comparison</h3>
                    <div class="h-64">
                        <canvas x-ref="yearlyComparisonChart"
                                x-effect="renderChart('yearlyComparisonChart', 'line',
                                        $wire.yearlyComparison.map(y => y.year),
                                        $wire.yearlyComparison.map(y => y.downloads))"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function downloadStats() {
                return {
                    charts: {},
                    initCharts() {
                        // Initialize all charts when component loads
                        this.$watch('$wire.downloadsPerPaper', () => this.updateAllCharts());
                    },
                    renderChart(ref, type, labels, data, extra = {}) {
                        if (!labels.length || !data.length) return;
                        
                        const ctx = this.$refs[ref].getContext('2d');
                        
                        // Destroy previous chart if exists
                        if (this.charts[ref]) {
                            this.charts[ref].destroy();
                        }
                        
                        // Common chart config
                        const config = {
                            type: type,
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Downloads',
                                    data: data,
                                    backgroundColor: this.getChartColors(type, labels.length),
                                    borderColor: 'rgba(79, 70, 229, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: this.getChartOptions(type, ref, extra)
                        };
                        
                        // Special dataset config for polarArea
                        if (type === 'polarArea') {
                            config.data.datasets[0].borderWidth = 0;
                        }
                        
                        // Special dataset config for line
                        if (type === 'line') {
                            config.data.datasets[0].fill = true;
                            config.data.datasets[0].tension = 0.1;
                            config.data.datasets[0].backgroundColor = 'rgba(79, 70, 229, 0.2)';
                        }
                        
                        this.charts[ref] = new Chart(ctx, config);
                    },
                    getChartColors(type, count) {
                        const baseColors = [
                            'rgba(79, 70, 229, 0.7)',
                            'rgba(99, 102, 241, 0.7)',
                            'rgba(129, 140, 248, 0.7)',
                            'rgba(167, 139, 250, 0.7)',
                            'rgba(196, 181, 253, 0.7)',
                        ];
                        
                        if (type === 'bar') return baseColors[0];
                        if (type === 'line') return 'rgba(79, 70, 229, 0.2)';
                        
                        // For charts that need multiple colors
                        return Array.from({length: count}, (_, i) => baseColors[i % baseColors.length]);
                    },
                    getChartOptions(type, ref, extra) {
                        const commonOptions = {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        };
                        
                        if (ref === 'topPapersChart') {
                            commonOptions.plugins = {
                                tooltip: {
                                    callbacks: {
                                        afterLabel: (context) => {
                                            return 'Paper ID: ' + extra.paper_ids[context.dataIndex];
                                        }
                                    }
                                }
                            };
                        }
                        
                        if (type === 'doughnut' || type === 'pie') {
                            commonOptions.plugins = {
                                legend: { position: 'right' }
                            };
                        }
                        
                        return commonOptions;
                    },
                    updateAllCharts() {
                        if (!this.$wire.downloadsPerPaper) return;
                        
                        // Update all charts when data changes
                        this.renderChart('topPapersChart', 'bar', 
                            this.$wire.downloadsPerPaper.map(p => p.paper_title), 
                            this.$wire.downloadsPerPaper.map(p => p.downloads),
                            {paper_ids: this.$wire.downloadsPerPaper.map(p => p.paper_id)});
                            
                        this.renderChart('departmentChart', 'doughnut',
                            this.$wire.downloadsByDepartment.map(d => d.department_name),
                            this.$wire.downloadsByDepartment.map(d => d.downloads));
                            
                        this.renderChart('examTypeChart', 'pie',
                            this.$wire.downloadsByExamType.map(e => e.exam_type),
                            this.$wire.downloadsByExamType.map(e => e.downloads));
                            
                        this.renderChart('studentTypeChart', 'polarArea',
                            this.$wire.downloadsByStudentType.map(s => s.student_type_name),
                            this.$wire.downloadsByStudentType.map(s => s.downloads));
                            
                        this.renderChart('levelChart', 'bar',
                            this.$wire.downloadsByLevel.map(l => l.level_name),
                            this.$wire.downloadsByLevel.map(l => l.downloads));
                            
                        this.renderChart('yearlyComparisonChart', 'line',
                            this.$wire.yearlyComparison.map(y => y.year),
                            this.$wire.yearlyComparison.map(y => y.downloads));
                    }
                };
            }
        </script>
    @endpush
</x-layouts.app-layout>