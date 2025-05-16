<div class="flex flex-col p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Student Management</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="flex flex-col justify-center items-start p-4 rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-500/10 shadow-sm">
                <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-1">Total Students</h3>
                <p class="text-2xl font-bold">{{ $students->total() }}</p>
            </div>
            <div class="flex flex-col justify-center items-start p-4 rounded-xl border border-pink-200 dark:border-pink-700 bg-pink-50 dark:bg-pink-500/10 shadow-sm">
                <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-1">Active Today</h3>
                <p class="text-2xl font-bold">{{ $activeTodayCount }}</p>
            </div>
            <div class="flex flex-col justify-center items-start p-4 rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-500/10 shadow-sm">
                <h3 class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-1">New This Week</h3>
                <p class="text-2xl font-bold">{{ $newThisWeekCount }}</p>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Student Activity Chart -->
        <div class="flex flex-col p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <h3 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">Student Activity (Last 7 Days)</h3>
            <div id="studentActivityChart" class="h-64"></div>
        </div>
        
        <!-- Registration Trend Chart -->
        <div class="flex flex-col p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <h3 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">New Registrations (Last 30 Days)</h3>
            <div id="registrationTrendChart" class="h-64"></div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search students..."
                class="pl-10 w-full rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <div class="flex space-x-2">
            <select
                wire:model.live="department"
                class="rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="perPage"
                class="rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                        <div class="flex items-center">
                            Name
                            @if($sortField === 'name')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                </svg>
                            @else
                                <svg class="w-3 h-3 ml-1 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('email')">
                        <div class="flex items-center">
                            Email
                            @if($sortField === 'email')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                </svg>
                            @else
                                <svg class="w-3 h-3 ml-1 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Department</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Last Activity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Downloads</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($students as $student)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-300 font-medium">{{ $student->initials() }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $student->name }}</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">Joined {{ $student->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $student->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $student->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $this->getLastActivity($student->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-24 h-8">
                                <div id="downloadSparkline-{{ $student->id }}" class="h-full"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button wire:click="viewStudent({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" aria-label="View student details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button wire:click="editStudent({{ $student->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" aria-label="Edit student">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $student->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete student">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            No students found matching your search.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $students->links() }}
    </div>
    
    <!-- Department Distribution Pie Chart -->
    <div class="mt-6 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
        <h3 class="text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">Student Distribution by Department</h3>
        <div id="departmentDistributionChart" class="h-64"></div>
    </div>

    <!-- Charts initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get theme colors based on current mode
            const textColor = document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#374151';
            const gridColor = document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb';
            
            // Chart color palette
            const chartColors = {
                green: '#10b981',
                pink: '#ec4899',
                red: '#ef4444',
                blue: '#3b82f6',
                indigo: '#6366f1',
                purple: '#8b5cf6',
                orange: '#f97316',
                yellow: '#eab308',
                cyan: '#06b6d4',
                lime: '#84cc16'
            };

            // Common chart options
            const commonOptions = {
                chart: {
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                xaxis: {
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
                }
            };

            // Student Activity Chart (Bar)
            new ApexCharts(document.querySelector("#studentActivityChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'bar',
                    height: '100%'
                },
                series: [{
                    name: 'Active Students',
                    data: [45, 52, 38, 24, 33, 26, 21]
                }],
                colors: [chartColors.green],
                xaxis: {
                    ...commonOptions.xaxis,
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '60%'
                    }
                }
            }).render();

            // Registration Trend Chart (Line)
            new ApexCharts(document.querySelector("#registrationTrendChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'line',
                    height: '100%'
                },
                series: [{
                    name: 'New Registrations',
                    data: [10, 41, 35, 51, 49, 62, 69, 91, 148, 35, 51, 49, 62, 69, 91, 148, 35, 51, 49, 62, 69, 91, 48, 35, 51, 49, 62, 69, 91, 78]
                }],
                colors: [chartColors.blue],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 4,
                    colors: [chartColors.blue],
                    strokeColors: '#fff',
                    strokeWidth: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        gradientToColors: [chartColors.indigo],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 0.7,
                        opacityTo: 0.2
                    }
                }
            }).render();

            // Department Distribution Chart (Pie)
            new ApexCharts(document.querySelector("#departmentDistributionChart"), {
                chart: {
                    type: 'pie',
                    height: '100%',
                    toolbar: {
                        show: false
                    }
                },
                series: [42, 39, 35, 29, 26],
                labels: ['Computer Science', 'Electrical Engineering', 'Civil Engineering', 'Mechanical Engineering', 'Business Administration'],
                colors: [chartColors.blue, chartColors.green, chartColors.purple, chartColors.orange, chartColors.cyan],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                stroke: {
                    width: 1
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            }).render();

            // Initialize download sparklines for each student
            @foreach ($students as $student)
                new ApexCharts(document.querySelector("#downloadSparkline-{{ $student->id }}"), {
                    chart: {
                        type: 'line',
                        height: '100%',
                        width: '100%',
                        sparkline: {
                            enabled: true
                        },
                        animations: {
                            enabled: false
                        }
                    },
                    series: [{
                        data: {{ json_encode($this->getStudentDownloadData($student->id)) }} // Replace with actual data
                    }],
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    colors: ['{{ $this->getDownloadTrendColor($student->id) }}'], // Dynamic color based on trend
                    tooltip: {
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: false
                        },
                        y: {
                            title: {
                                formatter: () => 'Downloads:'
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                }).render();
            @endforeach
        });

        // Handle Livewire updates
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('studentDataUpdated', () => {
                // Re-initialize charts when data is updated
                // This ensures charts reflect the latest data
                setTimeout(() => {
                    // Your chart initialization code here
                    // You may need to destroy existing charts first
                }, 300);
            });
        });
    </script>
</div>