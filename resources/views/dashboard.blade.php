<x-layouts.app :title="__('Dashboard')">
    @if(auth()->user()->isAdmin())
    <!-- Load ApexCharts from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
           
    <div class="flex flex-col gap-6 mt-6 w-full h-full rounded-xl admin-dashboard-section">
        <!-- Stats Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 w-full">
            <!-- Total Students -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Total Students</h3>
                    <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
                </div>
                <div id="studentChart" class="h-16 w-24"></div>
            </div>
           
            <!-- Total Papers -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Total Papers</h3>
                    <p class="text-3xl font-bold">{{ \App\Models\Paper::count() }}</p>
                </div>
                <div id="paperChart" class="h-16 w-24"></div>
            </div>
   
            <!-- Departments -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Departments</h3>
                    <p class="text-3xl font-bold">{{ \App\Models\Department::count() }}</p>
                </div>
                <div id="departmentChart" class="h-16 w-24"></div>
            </div>
            
            <!-- Courses -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Courses</h3>
                    <p class="text-3xl font-bold">{{ \App\Models\Course::count() }}</p>
                </div>
                <div id="courseChart" class="h-16 w-24"></div>
            </div>
        </div>
        
        <!-- Main Dashboard Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Papers by Department Pie Chart -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">Papers by Department</h3>
                <div id="papersByDepartmentChart" class="h-80 w-full"></div>
            </div>
            
            <!-- Courses by Department Pie Chart -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">Courses by Department</h3>
                <div id="coursesByDepartmentChart" class="h-80 w-full"></div>
            </div>
        </div>

        <!-- Additional Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Department Distribution Pie Chart -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">Department Distribution</h3>
                <div id="departmentDistributionChart" class="h-80 w-full"></div>
            </div>
            
            <!-- Users Over The Week Column Chart -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">Users Registered This Week</h3>
                <div id="usersOverWeekChart" class="h-80 w-full"></div>
            </div>
        </div>
   
        <!-- Student Table Section -->
        <livewire:student-table/>  
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
                
                // Generate last 7 days data for area charts
                for($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $studentsData->push([
                        'date' => $date->format('M j'),
                        'count' => \App\Models\User::whereDate('created_at', '<=', $date)->count()
                    ]);
                    $papersData->push([
                        'date' => $date->format('M j'),
                        'count' => \App\Models\Paper::whereDate('created_at', '<=', $date)->count()
                    ]);
                    $departmentsData->push([
                        'date' => $date->format('M j'),
                        'count' => \App\Models\Department::whereDate('created_at', '<=', $date)->count()
                    ]);
                    $coursesData->push([
                        'date' => $date->format('M j'),
                        'count' => \App\Models\Course::whereDate('created_at', '<=', $date)->count()
                    ]);
                }

                // Papers by Department
                $papersByDept = \App\Models\Department::withCount('papers')->get();
                $papersByDeptData = $papersByDept->map(function($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->papers_count
                    ];
                });

                // Courses by Department
                $coursesByDept = \App\Models\Department::withCount('courses')->get();
                $coursesByDeptData = $coursesByDept->map(function($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->courses_count
                    ];
                });

                // Department distribution (equal distribution for demo)
                $deptDistribution = \App\Models\Department::all()->map(function($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => 1
                    ];
                });

                // Users registered this week
                $usersThisWeek = collect();
                for($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $usersThisWeek->push([
                        'day' => $date->format('D'),
                        'count' => \App\Models\User::whereDate('created_at', $date)->count()
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

            // Create small area charts
            createAreaChart('studentChart', {!! $studentsData->toJson() !!}, '#3b82f6');
            createAreaChart('paperChart', {!! $papersData->toJson() !!}, '#10b981');
            createAreaChart('departmentChart', {!! $departmentsData->toJson() !!}, '#f59e0b');
            createAreaChart('courseChart', {!! $coursesData->toJson() !!}, '#8b5cf6');

            // Papers by Department Pie Chart
            const papersByDeptOptions = {
                chart: {
                    type: 'pie',
                    height: 320
                },
                series: {!! $papersByDeptData->pluck('count')->toJson() !!},
                labels: {!! $papersByDeptData->pluck('name')->toJson() !!},
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '45%'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#fff']
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            
            const papersByDeptChart = new ApexCharts(document.querySelector('#papersByDepartmentChart'), papersByDeptOptions);
            papersByDeptChart.render();

            // Courses by Department Pie Chart
            const coursesByDeptOptions = {
                chart: {
                    type: 'pie',
                    height: 320
                },
                series: {!! $coursesByDeptData->pluck('count')->toJson() !!},
                labels: {!! $coursesByDeptData->pluck('name')->toJson() !!},
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '45%'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#fff']
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            
            const coursesByDeptChart = new ApexCharts(document.querySelector('#coursesByDepartmentChart'), coursesByDeptOptions);
            coursesByDeptChart.render();

            // Department Distribution Pie Chart
            const deptDistributionOptions = {
                chart: {
                    type: 'pie',
                    height: 320
                },
                series: {!! $deptDistribution->pluck('count')->toJson() !!},
                labels: {!! $deptDistribution->pluck('name')->toJson() !!},
                colors: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '45%'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#fff']
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            
            const deptDistributionChart = new ApexCharts(document.querySelector('#departmentDistributionChart'), deptDistributionOptions);
            deptDistributionChart.render();

            // Users Over The Week Column Chart
            const usersWeekOptions = {
                chart: {
                    type: 'column',
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
                colors: ['#3b82f6'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '60%'
                    }
                },
                dataLabels: {
                    enabled: false
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
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Available Papers</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Paper::where('student_type_id', auth()->user()->student_type_id)->count() }}</p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Recent Downloads</h3>
                    <p class="text-2xl font-bold">{{ \App\Models\Download::where('user_id', auth()->id())->count() }}</p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Your Department</h3>
                    <p class="text-lg">{{ auth()->user()->department->name ?? 'Not Assigned' }}</p>
                </div>
            </div>
            <livewire:student.download-history />
        </div>
    @endif
</x-layouts.app>