<div>
    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <select wire:model="timeRange">
            <option value="week">Last Week</option>
            <option value="month">Last Month</option>
            <option value="quarter">Last Quarter</option>
            <option value="year">Last Year</option>
            <option value="">All Time</option>
        </select>
        
        <!-- Additional filters -->
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach($this->summaryStats as $metric => $data)
            <div class="stats-card">
                <h3>{{ str_replace('_', ' ', ucfirst($metric)) }}</h3>
                <div class="text-2xl">{{ number_format($data['current']) }}</div>
                <div class="text-sm">
                    @if(isset($data['change']))
                        <span class="{{ $data['change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $data['change'] }}% from previous period
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="space-y-8">
        <!-- Downloads Trend Chart -->
        <div class="chart-container">
            <h3>Downloads Trend</h3>
            <canvas 
                id="downloadsTrendChart" 
                wire:ignore
                x-data="downloadsTrendChart({{ json_encode($this->trendData) }})"
            ></canvas>
        </div>

        <!-- Downloads by Department -->
        <div class="chart-container">
            <h3>Downloads by Department</h3>
            <canvas 
                id="departmentChart" 
                wire:ignore
                x-data="departmentChart({{ json_encode($this->byDepartment) }})"
            ></canvas>
        </div>

        <!-- Additional charts -->
    </div>
</div>

@push('scripts')
<script>
    // Initialize Alpine.js chart components
    function downloadsTrendChart(data) {
        return {
            init() {
                new Chart(this.$el, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.date),
                        datasets: [{
                            label: 'Downloads',
                            data: data.map(item => item.count),
                            borderColor: '#3b82f6',
                            tension: 0.1
                        }]
                    }
                });
            }
        }
    }
    
    // Additional chart initializers...
</script>
@endpush