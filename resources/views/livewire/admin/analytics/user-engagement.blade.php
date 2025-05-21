blade
<div>
    {{-- Period Selection --}}
    <div class="mb-4">
        <label for="period" class="block text-sm font-medium text-gray-700">Select Period:</label>
        <select wire:model.live="period" id="period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            @foreach ($periods as $p)
                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Engagement Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-analytics.stat-card title="Total Sessions" :value="$stats['total_sessions'] ?? 0" />
        <x-analytics.stat-card title="Active Users" :value="$stats['active_users'] ?? 0" />
        <x-analytics.stat-card title="Average Session Duration" :value="$this->formatDuration($stats['average_session_duration'] ?? null)" />
        <x-analytics.stat-card title="Most Active Users" :value="implode(', ', array_map(fn($user) => $user->name ?? 'N/A', $stats['most_active_users'] ?? []))" /> {{-- Assuming most_active_users is an array of user objects with a 'name' property --}}
    </div>

    {{-- Daily Active Users Chart (Placeholder) --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Daily Active Users</h3>
        {{-- You would integrate a charting library here (e.g., Chart.js, ApexCharts) --}}
        <div class="bg-white p-4 shadow rounded-lg">
            {{-- Example of how you might pass data to a chart component/JS --}}
            <canvas id="dailyActiveUsersChart" data-chart-data="{{ json_encode($dailyActiveUsersChart) }}"></canvas>
        </div>
    </div>

    {{-- Most Visited Pages --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Most Visited Pages</h3>
        <div class="bg-white p-4 shadow rounded-lg">
            @if (empty($mostVisitedPages))
                <p>No page visit data available for this period.</p>
            @else
                <ul>
                    @foreach ($mostVisitedPages as $page => $count)
                        <li>{{ $page }} ({{ $count }} visits)</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Most Common Actions --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Most Common Actions</h3>
        <div class="bg-white p-4 shadow rounded-lg">
            @if (empty($mostCommonActions))
                <p>No action data available for this period.</p>
            @else
                <ul>
                    @foreach ($mostCommonActions as $action => $count)
                        <li>{{ $action }} ({{ $count }} times)</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', function () {
        renderDailyActiveUsersChart();
    });

    function renderDailyActiveUsersChart() {
        const ctx = document.getElementById('dailyActiveUsersChart');
        const chartData = JSON.parse(ctx.dataset.chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => item.date),
                datasets: [{
                    label: 'Daily Active Users',
                    data: chartData.map(item => item.count),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Users'
                        }
                    },
                    x: {
                         title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
