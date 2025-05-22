<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">User Engagement Analytics</h2>
                <div class="flex space-x-2">
                    @foreach ($periods as $p)
                        <button 
                            wire:click="updatePeriod('{{ $p }}')" 
                            class="px-3 py-1 text-sm rounded-md {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            {{ ucfirst($p) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Active Users</div>
                    <div class="text-2xl font-bold">{{ $stats['active_users'] ?? 0 }}</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Total Sessions</div>
                    <div class="text-2xl font-bold">{{ $stats['total_sessions'] ?? 0 }}</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Avg. Session Duration</div>
                    <div class="text-2xl font-bold">{{ $formatDuration($stats['average_session_duration'] ?? null) }}</div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Sessions per User</div>
                    <div class="text-2xl font-bold">
                        @if(($stats['active_users'] ?? 0) > 0)
                            {{ number_format(($stats['total_sessions'] ?? 0) / ($stats['active_users'] ?? 1), 1) }}
                        @else
                            0
                        @endif
                    </div>
                </div>
            </div>

            <!-- Active Users Chart -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Daily Active Users</h3>
                    <div class="flex space-x-2">
                        <button 
                            wire:click="updateChartDays(7)" 
                            class="px-3 py-1 text-sm rounded-md {{ $chartDays === 7 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            7 Days
                        </button>
                        <button 
                            wire:click="updateChartDays(30)" 
                            class="px-3 py-1 text-sm rounded-md {{ $chartDays === 30 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            30 Days
                        </button>
                        <button 
                            wire:click="updateChartDays(90)" 
                            class="px-3 py-1 text-sm rounded-md {{ $chartDays === 90 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            90 Days
                        </button>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-gray-200" style="height: 300px;">
                    <div 
                        x-data="{
                            chartData: @entangle('dailyActiveUsersChart'),
                            init() {
                                const labels = this.chartData.map(item => this.formatDate(item.date));
                                const data = this.chartData.map(item => item.count);
                                
                                const ctx = this.$refs.canvas.getContext('2d');
                                
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Active Users',
                                            data: data,
                                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                                            borderColor: 'rgba(99, 102, 241, 1)',
                                            tension: 0.4,
                                            pointRadius: 3,
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                precision: 0
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                displayColors: false
                                            }
                                        }
                                    }
                                });
                            },
                            formatDate(dateString) {
                                const date = new Date(dateString);
                                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            }
                        }"
                        wire:ignore
                    >
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Most Visited Pages -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Most Visited Pages</h3>
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visits</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($mostVisitedPages as $page => $visits)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $page }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $visits }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-sm text-gray-500 text-center">No data available</td>
                                    </tr>