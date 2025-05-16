<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Dashboard'))]); ?>
    <?php if(auth()->user()->isAdmin()): ?>
    <!-- Load ApexCharts from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
           
    <div class="flex flex-col gap-6 mt-6 w-full h-full rounded-xl admin-dashboard-section">
        <!-- Stats Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 w-full">
            <!-- Total Students -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Total Students</h3>
                    <p class="text-3xl font-bold"><?php echo e(\App\Models\User::count()); ?></p>
                </div>
                <div id="studentChart" class="h-16 w-24"></div>
            </div>
           
            <!-- Total Papers -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Total Papers</h3>
                    <p class="text-3xl font-bold"><?php echo e(\App\Models\Paper::count()); ?></p>
                </div>
                <div id="paperChart" class="h-16 w-24"></div>
            </div>
   
            <!-- Departments -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Departments</h3>
                    <p class="text-3xl font-bold"><?php echo e(\App\Models\Department::count()); ?></p>
                </div>
                <div id="departmentChart" class="h-16 w-24"></div>
            </div>
            
            <!-- Courses -->
            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="flex flex-col justify-center">
                    <h3 class="text-lg font-semibold mb-1">Courses</h3>
                    <p class="text-3xl font-bold"><?php echo e(\App\Models\Course::count()); ?></p>
                </div>
                <div id="courseChart" class="h-16 w-24"></div>
            </div>
        </div>
        
        <!-- Main Dashboard Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Main Stats Bar Chart -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">System Statistics</h3>
                <div id="mainStatsChart" class="h-80 w-full"></div>
            </div>
            
            <!-- Additional chart space for future use -->
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <h3 class="text-xl font-semibold mb-4">Download Analytics</h3>
                <div id="downloadsChart" class="h-80 w-full"></div>
            </div>
        </div>
   
        <!-- Student Table Section -->
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('student-table', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3637520716-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>  
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get theme colors based on current mode
            const textColor = document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#374151';
            const gridColor = document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb';
            
            // Chart color options
            const chartColors = {
                students: '#4f46e5', // indigo
                papers: '#0ea5e9',   // sky blue
                departments: '#10b981', // emerald
                courses: '#f59e0b'   // amber
            };

            // Common chart options
            const commonOptions = {
                chart: {
                    height: '100%',
                    width: '100%',
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    fixed: {
                        enabled: false
                    },
                    marker: {
                        show: false
                    }
                },
                grid: {
                    show: false
                }
            };

            // Sample data - in a real app you'd get this from Livewire
            const studentData = [30, 40, 45, 50, 49, 60, 70, 91];
            const paperData = [40, 55, 65, 90, 115, 95, 90, 120];
            const departmentData = [5, 5, 6, 7, 8, 8, 9, 10];
            const courseData = [25, 30, 35, 40, 45, 50, 55, 60];

            // Student Chart (Bar Chart)
            new ApexCharts(document.querySelector("#studentChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'bar'
                },
                series: [{
                    name: 'Students',
                    data: studentData.slice(-4) // Only show last 4 data points for mini charts
                }],
                colors: [chartColors.students],
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 2
                    }
                }
            }).render();

            // Papers Chart (Bar Chart)
            new ApexCharts(document.querySelector("#paperChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'bar'
                },
                series: [{
                    name: 'Papers',
                    data: paperData.slice(-4)
                }],
                colors: [chartColors.papers],
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 2
                    }
                }
            }).render();

            // Department Chart (Bar Chart)
            new ApexCharts(document.querySelector("#departmentChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'bar'
                },
                series: [{
                    name: 'Departments',
                    data: departmentData.slice(-4)
                }],
                colors: [chartColors.departments],
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 2
                    }
                }
            }).render();

            // Course Chart (Bar Chart)
            new ApexCharts(document.querySelector("#courseChart"), {
                ...commonOptions,
                chart: {
                    ...commonOptions.chart,
                    type: 'bar'
                },
                series: [{
                    name: 'Courses',
                    data: courseData.slice(-4)
                }],
                colors: [chartColors.courses],
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 2
                    }
                }
            }).render();
            
            // Main Stats Bar Chart
            new ApexCharts(document.querySelector("#mainStatsChart"), {
                chart: {
                    type: 'bar',
                    height: '100%',
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                series: [{
                    name: 'Count',
                    data: [
                        { x: 'Students', y: <?php echo e(\App\Models\User::count()); ?> },
                        { x: 'Papers', y: <?php echo e(\App\Models\Paper::count()); ?> },
                        { x: 'Departments', y: <?php echo e(\App\Models\Department::count()); ?> },
                        { x: 'Courses', y: <?php echo e(\App\Models\Course::count()); ?> }
                    ]
                }],
                plotOptions: {
                    bar: {
                        distributed: true,
                        borderRadius: 4,
                        columnWidth: '60%',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: [
                    chartColors.students,
                    chartColors.papers,
                    chartColors.departments,
                    chartColors.courses
                ],
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
                xaxis: {
                    categories: ['Students', 'Papers', 'Departments', 'Courses'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
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
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                }
            }).render();
            
            // Downloads Chart (Sample)
            new ApexCharts(document.querySelector("#downloadsChart"), {
                chart: {
                    type: 'bar',
                    height: '100%',
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true
                    }
                },
                series: [{
                    name: 'Downloads',
                    data: [45, 68, 75, 91, 23, 42, 60]
                }],
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
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
                colors: ['#8b5cf6'], // Purple for downloads
                plotOptions: {
                    bar: {
                        borderRadius: 4
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            }).render();
        });
    </script>

    

    <?php elseif(auth()->user()->isStudent()): ?>
        <div class="student-dashboard-section">
            <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Available Papers</h3>
                    <p class="text-2xl font-bold"><?php echo e(\App\Models\Paper::where('student_type_id', auth()->user()->student_type_id)->count()); ?></p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Recent Downloads</h3>
                    <p class="text-2xl font-bold"><?php echo e(\App\Models\Download::where('user_id', auth()->id())->count()); ?></p>
                </div>
                <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl shadow">
                    <h3 class="font-semibold text-lg mb-2">Your Department</h3>
                    <p class="text-lg"><?php echo e(auth()->user()->department->name ?? 'Not Assigned'); ?></p>
                </div>
            </div>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('student.download-history', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3637520716-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
    <?php endif; ?>

   
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?><?php /**PATH /home/user/Q-Vault/resources/views/dashboard.blade.php ENDPATH**/ ?>