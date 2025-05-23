<div>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Download Statistics</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Monitor and analyze paper download patterns</p>
            </div>
            <div class="flex space-x-3">
                <button wire:click="exportData" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Data
                </button>
                <button wire:click="resetFilters" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" wire:model.live="startDate" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" wire:model.live="endDate" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                <select wire:model.live="selectedDepartment" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Departments</option>
                    @if(isset($departments) && $departments->count() > 0)
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Student Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Type</label>
                <select wire:model.live="selectedStudentType" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    @if(isset($studentTypes) && $studentTypes->count() > 0)
                        @foreach($studentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Level Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                <select wire:model.live="selectedLevel" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Levels</option>
                    @if(isset($levels) && $levels->count() > 0)
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Exam Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Type</label>
                <select wire:model.live="selectedExamType" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    @if(isset($examTypes) && is_array($examTypes))
                        @foreach($examTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Exam Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Year</label>
                <select wire:model.live="selectedExamYear" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Years</option>
                    @if(isset($examYears) && is_array($examYears))
                        @foreach($examYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Downloads</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->totalDownloads) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unique Users</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($uniqueUsers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average per Day</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $totalDownloads > 0 ? number_format($totalDownloads / max(1, \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1), 1) : '0' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Downloads per User</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $uniqueUsers > 0 ? number_format($totalDownloads / $uniqueUsers, 1) : '0' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- View Mode Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('viewMode', 'overview')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                Overview
            </button>
            <button wire:click="$set('viewMode', 'department')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'department' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                By Department
            </button>
            <button wire:click="$set('viewMode', 'student_type')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'student_type' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                By Student Type
            </button>
            <button wire:click="$set('viewMode', 'level')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'level' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                By Level
            </button>
            <button wire:click="$set('viewMode', 'exam_type')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'exam_type' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                By Exam Type
            </button>
            <button wire:click="$set('viewMode', 'year')" 
                    class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $viewMode === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                By Year
            </button>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            @switch($viewMode)
                @case('department')
                    Downloads by Department
                    @break
                @case('student_type')
                    Downloads by Student Type
                    @break
                @case('level')
                    Downloads by Level
                    @break
                @case('exam_type')
                    Downloads by Exam Type
                    @break
                @case('year')
                    Downloads by Year
                    @break
                @default
                    Download Trends Over Time
            @endswitch
        </h3>
        
        <div class="relative" style="height: 400px;">
            <canvas id="statisticsChart" width="400" height="200"></canvas>
        </div>
        
        @if(empty($chartData))
            <div class="text-center py-8">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-lg font-medium">No data available</p>
                    <p class="text-sm">Try adjusting your filters to see statistics</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
    let chart = null;

    function updateChart() {
        const ctx = document.getElementById('statisticsChart').getContext('2d');
        const chartData = @json($chartData);
        const viewMode = @this.viewMode;

        // Destroy existing chart if it exists
        if (chart) {
            chart.destroy();
        }

        if (!chartData || chartData.length === 0) {
            return;
        }

        // Determine chart type based on view mode
        let chartType = 'line';
        if (['department', 'student_type', 'level', 'exam_type', 'year'].includes(viewMode)) {
            chartType = 'doughnut';
        }

        let chartConfig = {
            type: chartType,
            data: {
                labels: chartData.map(item => item.label),
                datasets: [{
                    label: 'Downloads',
                    data: chartData.map(item => item.value),
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: chartType === 'doughnut',
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#374151',
                        borderWidth: 1
                    }
                },
                scales: chartType === 'line' ? {
                    x: {
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        }
                    }
                } : {}
            }
        };

        // Customize colors and styling based on chart type
        if (chartType === 'doughnut') {
            chartConfig.data.datasets[0].backgroundColor = chartData.map(item => item.color || '#3B82F6');
            chartConfig.data.datasets[0].borderColor = '#fff';
            chartConfig.data.datasets[0].borderWidth = 2;
        } else {
            chartConfig.data.datasets[0].borderColor = '#3B82F6';
            chartConfig.data.datasets[0].backgroundColor = 'rgba(59, 130, 246, 0.1)';
            chartConfig.data.datasets[0].fill = true;
        }

        chart = new Chart(ctx, chartConfig);
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateChart();
    });

    // Update chart when Livewire updates
    Livewire.on('chartDataUpdated', () => {
        updateChart();
    });

    // Update chart when component updates
    document.addEventListener('livewire:updated', function() {
        updateChart();
    });

    // Handle dark mode changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                setTimeout(updateChart, 100);
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
</script>