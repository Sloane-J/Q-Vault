<x-layouts.app :title="__('Dashboard')">
    @if (auth()->user()->isAdmin())
        <!-- Load ApexCharts from CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <div class="flex flex-col gap-6 mt-6 w-full h-full rounded-xl admin-dashboard-section">
            <!-- Stats Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 w-full">
                <!-- Total Students -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-lg font-semibold mb-1">Total Users</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div id="studentChart" class="h-16 w-24"></div>
                </div>

                <!-- Total Papers -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-lg font-semibold mb-1">Total Papers</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\Paper::count() }}</p>
                    </div>
                    <div id="paperChart" class="h-16 w-24"></div>
                </div>

                <!-- Departments -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-lg font-semibold mb-1">Departments</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\Department::count() }}</p>
                    </div>
                    <div id="departmentChart" class="h-16 w-24"></div>
                </div>

                <!-- Courses -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-lg font-semibold mb-1">Courses</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\Course::count() }}</p>
                    </div>
                    <div id="courseChart" class="h-16 w-24"></div>
                </div>
            </div>

            <!-- Main Dashboard Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Papers by Department Column Chart -->
                <div
                    class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Papers by Department</h3>
                    <div id="papersByDepartmentChart" class="h-80 w-full"></div>
                </div>

                <!-- Courses by Department Column Chart -->
                <div
                    class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Courses by Department</h3>
                    <div id="coursesByDepartmentChart" class="h-80 w-full"></div>
                </div>
            </div>

            <!-- Additional Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Department Distribution Column Chart -->
                <div
                    class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Departments</h3>
                    <div id="departmentDistributionChart" class="h-80 w-full"></div>
                </div>

                <!-- Users Over The Week Column Chart -->
                <div
                    class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Users Registered This Week</h3>
                    <div id="usersOverWeekChart" class="h-80 w-full"></div>
                </div>
            </div>

            <!-- Student Table Section -->
            <livewire:student-table />
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare data from Laravel backend
                @php
                    // Get data for charts
                    $studentsData = collect();
                    $papersData = collect();
                    $departmentsData = collect();
                    $coursesData = collect();

                    // Generate last 7 days data for bar charts
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $studentsData->push([
                            'date' => $date->format('M j'),
                            'count' => \App\Models\User::whereDate('created_at', '<=', $date)->count(),
                        ]);
                        $papersData->push([
                            'date' => $date->format('M j'),
                            'count' => \App\Models\Paper::whereDate('created_at', '<=', $date)->count(),
                        ]);
                        $departmentsData->push([
                            'date' => $date->format('M j'),
                            'count' => \App\Models\Department::whereDate('created_at', '<=', $date)->count(),
                        ]);
                        $coursesData->push([
                            'date' => $date->format('M j'),
                            'count' => \App\Models\Course::whereDate('created_at', '<=', $date)->count(),
                        ]);
                    }

                    // Papers by Department
                    $papersByDept = \App\Models\Department::withCount('papers')->get();
                    $papersByDeptData = $papersByDept->map(function ($dept) {
                        return [
                            'name' => $dept->name,
                            'count' => $dept->papers_count,
                        ];
                    });

                    // Courses by Department
                    $coursesByDept = \App\Models\Department::withCount('courses')->get();
                    $coursesByDeptData = $coursesByDept->map(function ($dept) {
                        return [
                            'name' => $dept->name,
                            'count' => $dept->courses_count,
                        ];
                    });

                    // Department distribution (equal distribution for demo)
                    $deptDistribution = \App\Models\Department::all()->map(function ($dept) {
                        return [
                            'name' => $dept->name,
                            'count' => 1,
                        ];
                    });

                    // Users registered this week
                    $usersThisWeek = collect();
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $usersThisWeek->push([
                            'day' => $date->format('D'),
                            'count' => \App\Models\User::whereDate('created_at', $date)->count(),
                        ]);
                    }
                @endphp

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#e5e7eb' : '#374151';
                const gridColor = isDark ? '#374151' : '#e5e7eb';

                // Area chart configuration for small stats
                function createAreaChart(elementId, data, color) {
                    const options = {
                        chart: {
                            type: 'area',
                            height: 64,
                            width: 96,
                            sparkline: {
                                enabled: true
                            }
                        },
                        series: [{
                            data: data.map(item => item.count)
                        }],
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.3,
                                stops: [0, 90, 100]
                            }
                        },
                        colors: [color],
                        tooltip: {
                            enabled: false
                        }
                    };

                    const chart = new ApexCharts(document.querySelector(`#${elementId}`), options);
                    chart.render();
                }

                // Create small bar charts
                createAreaChart('studentChart', {!! $studentsData->toJson() !!}, '#3b82f6');
                createAreaChart('paperChart', {!! $papersData->toJson() !!}, '#10b981');
                createAreaChart('departmentChart', {!! $departmentsData->toJson() !!}, '#f59e0b');
                createAreaChart('courseChart', {!! $coursesData->toJson() !!}, '#8b5cf6');

                // Papers by Department Column Chart
                const papersByDeptOptions = {
                    chart: {
                        type: 'bar',
                        height: 320,
                        background: 'transparent'
                    },
                    series: [{
                        name: 'Papers',
                        data: {!! $papersByDeptData->pluck('count')->toJson() !!}
                    }],
                    xaxis: {
                        categories: {!! $papersByDeptData->pluck('name')->toJson() !!},
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    colors: ['#3b82f6'],
                    plotOptions: {
                        bar: {
                            borderRadius: 1,
                            columnWidth: '60%',
                            distributed: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: [textColor]
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                };

                const papersByDeptChart = new ApexCharts(document.querySelector('#papersByDepartmentChart'),
                    papersByDeptOptions);
                papersByDeptChart.render();

                // Courses by Department Column Chart
                const coursesByDeptOptions = {
                    chart: {
                        type: 'bar',
                        height: 320,
                        background: 'transparent'
                    },
                    series: [{
                        name: 'Courses',
                        data: {!! $coursesByDeptData->pluck('count')->toJson() !!}
                    }],
                    xaxis: {
                        categories: {!! $coursesByDeptData->pluck('name')->toJson() !!},
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    colors: ['#10b981'],
                    plotOptions: {
                        bar: {
                            borderRadius: 1,
                            columnWidth: '60%',
                            distributed: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: [textColor]
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                };

                const coursesByDeptChart = new ApexCharts(document.querySelector('#coursesByDepartmentChart'),
                    coursesByDeptOptions);
                coursesByDeptChart.render();

                // Department Distribution Column Chart
                const deptDistributionOptions = {
                    chart: {
                        type: 'bar',
                        height: 320,
                        background: 'transparent'
                    },
                    series: [{
                        name: 'Departments',
                        data: {!! $deptDistribution->pluck('count')->toJson() !!}
                    }],
                    xaxis: {
                        categories: {!! $deptDistribution->pluck('name')->toJson() !!},
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    colors: ['#f59e0b'],
                    plotOptions: {
                        bar: {
                            borderRadius: 1,
                            columnWidth: '60%',
                            distributed: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: [textColor]
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                };

                const deptDistributionChart = new ApexCharts(document.querySelector('#departmentDistributionChart'),
                    deptDistributionOptions);
                deptDistributionChart.render();

                // Users Over The Week Column Chart
                const usersWeekOptions = {
                    chart: {
                        type: 'bar',
                        height: 320,
                        background: 'transparent'
                    },
                    series: [{
                        name: 'New Users',
                        data: {!! $usersThisWeek->pluck('count')->toJson() !!}
                    }],
                    xaxis: {
                        categories: {!! $usersThisWeek->pluck('day')->toJson() !!},
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: textColor
                            }
                        }
                    },
                    colors: ['#8b5cf6'],
                    plotOptions: {
                        bar: {
                            borderRadius: 1,
                            columnWidth: '60%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: [textColor]
                        }
                    },
                    grid: {
                        borderColor: gridColor,
                        strokeDashArray: 3
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                };

                const usersWeekChart = new ApexCharts(document.querySelector('#usersOverWeekChart'), usersWeekOptions);
                usersWeekChart.render();

                // Handle window resize for chart responsiveness
                window.addEventListener('resize', function() {
                    // Charts will automatically handle resize with ApexCharts built-in responsiveness
                });
            });
        </script>
   @elseif(auth()->user()->isStudent())
    <div class="student-dashboard-section">
    <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Papers Card -->
        <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <div class="flex flex-col justify-center">
                <h3 class="text-lg font-semibold mb-1 text-gray-700 dark:text-gray-300">Total Papers</h3>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ \App\Models\Paper::where('is_visible', 'public')->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Papers in database</p>
            </div>
            <div id="totalPapersChart" class="h-16 w-24"></div>
        </div>

        <!-- New This Week Card -->
        <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <div class="flex flex-col justify-center">
                <h3 class="text-lg font-semibold mb-1 text-gray-700 dark:text-gray-300">New This Week</h3>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ \App\Models\Paper::where('is_visible', 'public')
                        ->where('created_at', '>=', now()->startOfWeek())
                        ->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Papers added recently</p>
            </div>
            <div id="newPapersChart" class="h-16 w-24"></div>
        </div>

        <!-- Your Downloads Card -->
        <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <div class="flex flex-col justify-center">
                <h3 class="text-lg font-semibold mb-1 text-gray-700 dark:text-gray-300">Your Downloads</h3>
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ \App\Models\Download::where('user_id', auth()->id())->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ \App\Models\Download::where('user_id', auth()->id())
                        ->where('downloaded_at', '>=', now()->startOfMonth())
                        ->count() }} this month
                </p>
            </div>
            <div id="downloadsChart" class="h-16 w-24"></div>
        </div>
    </div>
    
    <livewire:student.download-history />
</div>

<!-- ApexCharts Script -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Total Papers Chart
    const totalPapersOptions = {
        series: [{
            name: 'Papers',
            data: [65, 70, 80, 85, 95, 105, 120] // Sample data - replace with actual
        }],
        chart: {
            type: 'area',
            height: 64,
            width: 96,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100]
            }
        },
        colors: ['#3B82F6'], // Blue color
        tooltip: {
            enabled: false
        }
    };
    
    const totalPapersChart = new ApexCharts(document.querySelector("#totalPapersChart"), totalPapersOptions);
    totalPapersChart.render();

    // New Papers Chart
    const newPapersOptions = {
        series: [{
            name: 'New Papers',
            data: [2, 4, 1, 6, 3, 8, 5] // Sample data - replace with actual
        }],
        chart: {
            type: 'area',
            height: 64,
            width: 96,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100]
            }
        },
        colors: ['#10B981'], // Green color
        tooltip: {
            enabled: false
        }
    };
    
    const newPapersChart = new ApexCharts(document.querySelector("#newPapersChart"), newPapersOptions);
    newPapersChart.render();

    // Downloads Chart
    const downloadsOptions = {
        series: [{
            name: 'Downloads',
            data: [12, 15, 10, 18, 22, 25, 30] // Sample data - replace with actual
        }],
        chart: {
            type: 'area',
            height: 64,
            width: 96,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100]
            }
        },
        colors: ['#8B5CF6'], // Purple color
        tooltip: {
            enabled: false
        }
    };
    
    const downloadsChart = new ApexCharts(document.querySelector("#downloadsChart"), downloadsOptions);
    downloadsChart.render();
});

// Optional: Update charts with real data via Livewire
window.updateCharts = function(totalPapersData, newPapersData, downloadsData) {
    // Update chart data dynamically if needed
    totalPapersChart.updateSeries([{
        name: 'Papers',
        data: totalPapersData
    }]);
    
    newPapersChart.updateSeries([{
        name: 'New Papers',
        data: newPapersData
    }]);
    
    downloadsChart.updateSeries([{
        name: 'Downloads',
        data: downloadsData
    }]);
};
</script>
@endif
</x-layouts.app>
