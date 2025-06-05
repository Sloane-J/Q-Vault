<div>
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Recently Uploaded Papers Card -->
                <div class="lg:col-span-1 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-full">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Recently Added</p>
                                <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">New Papers</p>
                        </div>
                    </div>
                    
                    @if(count($recentlyUploadedPapers) > 0)
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($recentlyUploadedPapers as $paper)
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700">
                                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 truncate" title="{{ $paper['course_name'] }}">
                                        {{ $paper['course_name'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $paper['department_name'] }} • {{ $paper['level'] }}
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $paper['uploaded_at_human'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-xs text-gray-500 dark:text-gray-400">No recent papers</p>
                        </div>
                    @endif
                </div>

                <!-- Popular Papers Card -->
                <div class="lg:col-span-3 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Trending Papers This Week</h3>
                    @if(!empty($popularPapersChart['categories']) && count($popularPapersChart['categories']) > 0)
                        <div x-data="{
                            chart: null,
                            initChart() {
                                if (this.chart) {
                                    this.chart.destroy();
                                }
                                
                                const chartData = @js($popularPapersChart);
                                
                                this.chart = new ApexCharts(this.$refs.popularChart, {
                                    chart: { 
                                        type: 'bar', 
                                        height: 280, 
                                        toolbar: { show: false },
                                        background: 'transparent'
                                    },
                                    plotOptions: { 
                                        bar: { 
                                            borderRadius: 4, 
                                            horizontal: false,
                                            distributed: false,
                                            barHeight: '70%'
                                        } 
                                    },
                                    dataLabels: { enabled: true },
                                    colors: ['#3b82f6'],
                                    series: chartData.series,
                                    xaxis: { 
                                        categories: chartData.categories,
                                        labels: {
                                            style: {
                                                colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151'
                                            }
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            style: {
                                                colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151'
                                            }
                                        }
                                    },
                                    grid: {
                                        borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                    },
                                    tooltip: {
                                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                                        custom: function({ series, seriesIndex, dataPointIndex }) {
                                            const value = series[seriesIndex][dataPointIndex];
                                            const category = chartData.categories[dataPointIndex];
                                            return `<div class='px-4 py-2 bg-white dark:bg-neutral-800 shadow-lg rounded-lg border border-gray-200 dark:border-neutral-700'>
                                                <p class='font-semibold dark:text-white'>${category}</p>
                                                <p class='text-sm text-gray-600 dark:text-gray-300'>Downloads: ${value}</p>
                                            </div>`;
                                        }
                                    }
                                });
                                this.chart.render();
                            }
                        }" 
                        x-init="$nextTick(() => initChart())" 
                        wire:key="popular-chart-{{ json_encode($popularPapersChart) }}"
                        class="w-full">
                            <div x-ref="popularChart"></div>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012-2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <p class="text-sm">No trending papers this week</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Download Trends Chart Card -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Download Trends by Course (This Year)</h3>
                @if(!empty($downloadTrendsChart['categories']) && count($downloadTrendsChart['categories']) > 0)
                    <div x-data="{
                        chart: null,
                        initChart() {
                            if (this.chart) {
                                this.chart.destroy();
                            }
                            
                            const chartData = @js($downloadTrendsChart);
                            
                            this.chart = new ApexCharts(this.$refs.trendsChart, {
                                chart: { 
                                    type: 'area', 
                                    height: 350, 
                                    toolbar: { show: true },
                                    background: 'transparent',
                                    zoom: { enabled: true }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                dataLabels: { enabled: false },
                                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'],
                                series: chartData.series,
                                xaxis: { 
                                    categories: chartData.categories,
                                    labels: {
                                        style: {
                                            colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151'
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        style: {
                                            colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151'
                                        }
                                    }
                                },
                                grid: {
                                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'left',
                                    labels: {
                                        colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#374151'
                                    }
                                },
                                tooltip: { 
                                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                                }
                            });
                            this.chart.render();
                        }
                    }" 
                    x-init="$nextTick(() => initChart())" 
                    wire:key="trends-chart-{{ json_encode($downloadTrendsChart) }}"
                    class="w-full">
                        <div x-ref="trendsChart"></div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="text-sm">No trends data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Download History Table -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Your Download History</h2>

                @if($downloads->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                            <thead class="bg-neutral-50 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-2/5">
                                        Paper Title / Department
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                                        Course
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                                        Level
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/5">
                                        Downloaded On
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach($downloads as $download)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-base font-semibold text-blue-600 dark:text-blue-400">
                                                {{ $download->paper->title ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $download->paper->department->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                            {{ $download->paper->course->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                            {{ $download->paper->level->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                            {{ $download->downloaded_at->format('M d, Y H:i A') }}
                                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                ({{ $download->downloaded_at->diffForHumans() }})
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $downloads->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto h-24 w-24 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
                            <svg class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No downloads yet</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Start by browsing and downloading exam papers to build your collection.</p>

                        <a href="{{ route('student.paper-browser') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Papers
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    @endpush
</div>