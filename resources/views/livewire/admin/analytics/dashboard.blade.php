<div class="dark:bg-neutral-900">
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
                        Main Analytics
                    </h2>
                    <button wire:click="refreshData" class="inline-flex items-center px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-neutral-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Papers Uploaded -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                    Total Papers Uploaded
                                </p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mt-2">
                                    {{ number_format($totalPapersUploaded) }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Downloads -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">
                                    Total Downloads
                                </p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mt-2">
                                    {{ number_format($totalDownloadsAllTime) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wide">
                                    Active Users
                                </p>
                                <div class="flex items-center mt-2">
                                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                                        {{ $activeUsersToday }}
                                    </p>
                                    
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 ml-2">
                                        Today / {{ $activeUsersThisWeek }} This Week
                                    </p>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Storage Used -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">
                                    Storage Used
                                </p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mt-2">
                                    {{ $storageUsed }}
                                </p>
                            </div>
                            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                <!-- Papers by Department Pie Chart -->
                <div class="lg:col-span-4">
                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-4">
                                Papers by Department
                            </h3>
                            <div class="relative h-80">
                                <canvas id="papersByDepartmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download Trends Line Chart -->
                <div class="lg:col-span-8">
                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                                    Download Trends
                                </h3>
                                <div class="relative">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-neutral-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="toggleDropdown('downloadTrendDropdown')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                    <div id="downloadTrendDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-neutral-800 ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <h6 class="px-4 py-2 text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase">Aggregation Period:</h6>
                                            <button wire:click="updateDownloadTrendAggregation('daily')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 @if($downloadTrendAggregation === 'daily') font-bold @endif">
                                                Daily
                                            </button>
                                            <button wire:click="updateDownloadTrendAggregation('weekly')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 @if($downloadTrendAggregation === 'weekly') font-bold @endif">
                                                Weekly
                                            </button>
                                            <button wire:click="updateDownloadTrendAggregation('monthly')" class="block w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 @if($downloadTrendAggregation === 'monthly') font-bold @endif">
                                                Monthly
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative h-80">
                                <canvas id="downloadTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Active User Trend Area Chart -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-4">
                            Active User Trend (Last 30 Days)
                        </h3>
                        <div class="relative h-80">
                            <canvas id="activeUserTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- System Activity Trend Line Chart -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-4">
                            System Activity Trend (Last 30 Days)
                        </h3>
                        <div class="relative h-80">
                            <canvas id="systemActivityTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Lists Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recently Added Papers -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-4">
                            Recently Added Papers
                        </h3>
                        @if($recentlyAddedPapers && count($recentlyAddedPapers) > 0)
                            <div class="space-y-3">
                                @foreach($recentlyAddedPapers as $paper)
                                    <div class="flex justify-between items-start p-4 rounded-lg bg-neutral-50 dark:bg-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <h6 class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                                {{ Str::limit($paper->title, 50) }}
                                            </h6>
                                        </div>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400 ml-2 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($paper->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-neutral-500 dark:text-neutral-400 text-sm">No recently added papers found.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent High Impact Audit Event -->
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-4">
                            Recent High Impact Event
                        </h3>
                        @if($recentHighImpactAuditEvent)
                            @php
                                $alertType = 'warning';
                                if (isset($recentHighImpactAuditEvent->level)) {
                                    $alertType = $recentHighImpactAuditEvent->level === 'critical' ? 'danger' : 'warning';
                                }
                                $alertColors = [
                                    'danger' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300',
                                    'warning' => 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300'
                                ];
                            @endphp
                            <div class="rounded-lg border p-4 {{ $alertColors[$alertType] ?? $alertColors['warning'] }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <strong class="text-sm font-medium">
                                            {{ $recentHighImpactAuditEvent->description ?? 'No description available' }}
                                        </strong>
                                        @if(isset($recentHighImpactAuditEvent->user_id) && $recentHighImpactAuditEvent->user_id)
                                            <div class="mt-2">
                                                <small class="text-xs opacity-75">User ID: {{ $recentHighImpactAuditEvent->user_id }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-xs opacity-75 ml-2 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($recentHighImpactAuditEvent->created_at)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-neutral-500 dark:text-neutral-400 text-sm">No high impact audit events found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Global chart variables
        let charts = {};
        
        // Theme colors for dark/light mode
        const isDark = document.documentElement.classList.contains('dark') || 
                      window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const colors = {
            text: isDark ? '#f5f5f5' : '#404040',
            grid: isDark ? '#525252' : '#e5e5e5',
            background: isDark ? '#262626' : '#ffffff'
        };

        // Dropdown toggle function
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[id$="Dropdown"]');
            dropdowns.forEach(dropdown => {
                const button = dropdown.previousElementSibling;
                if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        // Helper function to safely initialize charts
        function initializeChart(canvasId, config) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                console.warn(`Canvas element with id '${canvasId}' not found`);
                return null;
            }
            
            try {
                const ctx = canvas.getContext('2d');
                return new Chart(ctx, config);
            } catch (error) {
                console.error(`Error initializing chart '${canvasId}':`, error);
                return null;
            }
        }

        // Initialize charts when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Papers by Department Pie Chart
            const papersByDepartmentData = @json($papersByDepartmentData ?? []);
            charts.papersByDepartment = initializeChart('papersByDepartmentChart', {
                type: 'pie',
                data: {
                    labels: Object.keys(papersByDepartmentData),
                    datasets: [{
                        data: Object.values(papersByDepartmentData),
                        backgroundColor: [
                            '#3B82F6', '#10B981', '#06B6D4', '#F59E0B', '#EF4444',
                            '#8B5CF6', '#EC4899', '#84CC16', '#F97316', '#6366F1'
                        ],
                        borderWidth: 2,
                        borderColor: colors.background,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: colors.text,
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: colors.background,
                            titleColor: colors.text,
                            bodyColor: colors.text,
                            borderColor: colors.grid,
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                }
            });

            // Download Trends Line Chart
            const downloadTrendsData = @json($downloadTrendsData ?? ['labels' => [], 'values' => []]);
            charts.downloadTrends = initializeChart('downloadTrendsChart', {
                type: 'line',
                data: {
                    labels: downloadTrendsData.labels || [],
                    datasets: [{
                        label: "Downloads",
                        data: downloadTrendsData.values || [],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: colors.background,
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: colors.background,
                            titleColor: colors.text,
                            bodyColor: colors.text,
                            borderColor: colors.grid,
                            borderWidth: 1,
                        }
                    }
                }
            });

            // Active User Trend Area Chart
            const activeUserTrendData = @json($activeUserTrendData ?? ['labels' => [], 'values' => []]);
            charts.activeUserTrend = initializeChart('activeUserTrendChart', {
                type: 'line',
                data: {
                    labels: activeUserTrendData.labels || [],
                    datasets: [{
                        label: "Active Users",
                        data: activeUserTrendData.values || [],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: colors.background,
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: colors.background,
                            titleColor: colors.text,
                            bodyColor: colors.text,
                            borderColor: colors.grid,
                            borderWidth: 1,
                        }
                    }
                }
            });

            // System Activity Trend Line Chart
            const systemActivityTrendData = @json($systemActivityTrendData ?? ['labels' => [], 'datasets' => []]);
            const systemActivityDatasets = systemActivityTrendData.datasets || [];
            charts.systemActivityTrend = initializeChart('systemActivityTrendChart', {
                type: 'line',
                data: {
                    labels: systemActivityTrendData.labels || [],
                    datasets: systemActivityDatasets.map(dataset => ({
                        label: dataset.label || 'Daily Activity',
                        data: dataset.data || [],
                        borderColor: '#06B6D4',
                        backgroundColor: 'rgba(6, 182, 212, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#06B6D4',
                        pointBorderColor: colors.background,
                        pointBorderWidth: 2,
                    })),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colors.grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: colors.text,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: colors.background,
                            titleColor: colors.text,
                            bodyColor: colors.text,
                            borderColor: colors.grid,
                            borderWidth: 1,
                        }
                    }
                }
            });
        });

        // Livewire event listeners for chart updates
        document.addEventListener('livewire:init', () => {
            Livewire.on('download-trends-updated', (event) => {
                if (charts.downloadTrends && event.data) {
                    charts.downloadTrends.data.labels = event.data.labels || [];
                    charts.downloadTrends.data.datasets[0].data = event.data.values || [];
                    charts.downloadTrends.update();
                }
            });

            Livewire.on('dashboard-refreshed', () => {
                console.log('Dashboard refreshed - charts will update when new data is emitted');
            });
        });
    </script>
</div>