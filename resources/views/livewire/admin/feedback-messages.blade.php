<div class="flex flex-col p-6 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-md space-y-6">
    <!-- Header with potential action buttons -->
     <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Feedback Messages</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Overview of user feedback and messages</p>
        </div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
       
        <div class="flex items-center gap-2">
            <!-- Optional action buttons could go here -->
        </div>
    </div>

    <!-- Charts Section - Improved spacing and consistency -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Messages by Category</h3>
                <!-- Optional chart controls could go here -->
            </div>
            <div id="categoryChart" class="h-60" wire:ignore></div>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Messages by Status</h3>
                <!-- Optional chart controls could go here -->
            </div>
            <div id="statusChart" class="h-60" wire:ignore></div>
        </div>
    </div>
    
    <!-- Messages Table - Enhanced styling -->
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm overflow-hidden">
        <!-- Table Header with potential filters -->
        <div class="px-5 py-3 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recent Messages</h3>
            <!-- Optional filters/search could go here -->
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">Message</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-neutral-600 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($messages as $message)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors duration-150">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100 max-w-[200px] truncate">
                                        {{ $message->message }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-500/10 text-green-800 dark:text-green-200">
                                    {{ ucfirst($message->category) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($message->status === 'new') border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-500/10 text-red-800 dark:text-red-200
                                    @elseif($message->status === 'read') border border-pink-200 dark:border-pink-700 bg-pink-50 dark:bg-pink-500/10 text-pink-800 dark:text-pink-200
                                    @else border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-500/10 text-green-800 dark:text-green-200 @endif">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $this->getUserName($message->user_info) }}</span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $this->getIpAddress($message->user_info) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $message->created_at->format('M d, Y H:i') }}
                            </td>
                           <td class="px-6 py-4 text-sm text-right" x-data="{ open: false }">
    <div class="relative inline-block text-left">
        <button type="button" @click="open = !open"
                :class="{
                    'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300': '{{ $message->status }}' === 'new',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300': '{{ $message->status }}' === 'read',
                    'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300': '{{ $message->status }}' === 'resolved',
                }"
                class="inline-flex items-center justify-center px-3 py-1.5 rounded-full text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2
                       focus:ring-offset-white dark:focus:ring-offset-neutral-900 transition-colors duration-200 ease-in-out">
            {{ ucfirst($message->status) }}
            <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white dark:bg-neutral-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                <a href="#" wire:click.prevent="updateStatus({{ $message->id }}, 'new')" @click="open = false"
                   class="block px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700" role="menuitem">New</a>
                <a href="#" wire:click.prevent="updateStatus({{ $message->id }}, 'read')" @click="open = false"
                   class="block px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700" role="menuitem">Read</a>
                <a href="#" wire:click.prevent="updateStatus({{ $message->id }}, 'resolved')" @click="open = false"
                   class="block px-4 py-2 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700" role="menuitem">Resolved</a>
            </div>
        </div>
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm">No feedback messages found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - Improved styling -->
        <div class="px-5 py-3 bg-neutral-50 dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700">
            {{ $messages->links() }}
        </div>
    </div>
</div>

<!-- Apexcharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get theme colors (similar logic to your Chart.js)
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e5e5' : '#525252'; // neutral-200 / neutral-600
        const gridColor = isDark ? '#525252' : '#e5e5e5'; // neutral-600 / neutral-200

        // Common colors for both charts
        // These arrays are already designed to provide multiple colors
        const primaryColors = [
            'rgba(34, 197, 94, 0.8)',   // Green
            'rgba(236, 72, 153, 0.8)',  // Pink
            'rgba(239, 68, 68, 0.8)',   // Red
            'rgba(168, 85, 247, 0.8)',  // Purple
            'rgba(59, 130, 246, 0.8)',  // Blue
            'rgba(255, 159, 64, 0.8)',  // Orange
            'rgba(75, 192, 192, 0.8)',  // Teal
            'rgba(153, 102, 255, 0.8)', // Violet
            'rgba(201, 203, 207, 0.8)'  // Grey
        ];
        const primaryBorderColors = [
            'rgb(34, 197, 94)',
            'rgb(236, 72, 153)',
            'rgb(239, 68, 68)',
            'rgb(168, 85, 247)',
            'rgb(59, 130, 246)',
            'rgb(255, 159, 64)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
        ];

        // Category Chart Options
        const categoryOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                },
                foreColor: textColor, // Apply text color to all chart text
            },
            series: [{
                name: 'Messages',
                data: @json($categoryData->pluck('value'))
            }],
            xaxis: {
                categories: @json($categoryData->pluck('name')),
                labels: {
                    style: {
                        colors: textColor,
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: textColor,
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                grid: {
                    borderColor: gridColor,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded',
                    distributed: true, 
                },
            },
            dataLabels: {
                enabled: false
            },
            colors: primaryColors, // This array will now be used for individual bars
            stroke: {
                show: true,
                width: 2,
                colors: primaryBorderColors 
            },
            legend: {
                show: false,
                labels: {
                    colors: textColor,
                }
            },
            grid: {
                borderColor: gridColor,
                xaxis: {
                    lines: {
                        show: false // No vertical grid lines by default in ApexCharts bar
                    }
                }
            },
            responsive: [{
                breakpoint: 768, // Adjust breakpoint as needed
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        };

        // Render Category Chart
        const categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
        categoryChart.render();

        // Status Chart Options
        // Ensure you have enough distinct colors in these arrays as well for each status
        const statusColors = [
            'rgba(239, 68, 68, 0.8)',    // Red for new
            'rgba(236, 72, 153, 0.8)',   // Pink for read
            'rgba(34, 197, 94, 0.8)',    // Green for resolved
            'rgba(59, 130, 246, 0.8)',   // Blue (add more if you expect more than 3 statuses)
            'rgba(168, 85, 247, 0.8)'    // Purple
        ];
        const statusBorderColors = [
            'rgb(239, 68, 68)',
            'rgb(236, 72, 153)',
            'rgb(34, 197, 94)',
            'rgb(59, 130, 246)',
            'rgb(168, 85, 247)'
        ];

        const statusOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                },
                foreColor: textColor, // Apply text color to all chart text
            },
            series: [{
                name: 'Messages',
                data: @json($statusData->pluck('value'))
            }],
            xaxis: {
                categories: @json($statusData->pluck('name')),
                labels: {
                    style: {
                        colors: textColor,
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: textColor,
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                grid: {
                    borderColor: gridColor,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded',
                    distributed: true, // This allows individual bar colors
                },
            },
            dataLabels: {
                enabled: false
            },
            colors: statusColors, // This array will now be used for individual bars
            stroke: {
                show: true,
                width: 2,
                colors: statusBorderColors // This array will be used for individual bar borders
            },
            legend: {
                show: false, // Hide legend if each bar has its own color
                labels: {
                    colors: textColor,
                }
            },
            grid: {
                borderColor: gridColor,
                xaxis: {
                    lines: {
                        show: false
                    }
                }
            },
            responsive: [{
                breakpoint: 768, // Adjust breakpoint as needed
                options: {
                    chart: {
                        height: 300
                    }
                }
            }]
        };

        // Render Status Chart
        const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();
    });

    // Listen for status updates and re-render charts
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('status-updated', () => {
            // A simple page reload as in your original code will work if the data is fresh on reload.
            location.reload();
        });
    });
</script>