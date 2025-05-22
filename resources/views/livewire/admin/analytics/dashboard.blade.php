@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Analytics Dashboard</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System Overview</h5>
                <button wire:click="refreshData" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- KPI Cards -->
            <div class="row mb-4">
                <!-- Papers Uploaded -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Papers Uploaded</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPapersUploaded }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Downloads -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Downloads</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDownloadsAllTime }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-download fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Active Users</div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $activeUsersToday }}</div>
                                        </div>
                                        <div class="col">
                                            <div class="text-xs text-muted">Today / {{ $activeUsersThisWeek }} This Week</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Storage Used -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Storage Used</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $storageUsed }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-database fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Papers by Department Pie Chart -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Papers by Department</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="papersByDepartmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download Trends Line Chart -->
                <div class="col-xl-8 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Download Trends</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                                     aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Aggregation Period:</div>
                                    <button class="dropdown-item" wire:click="updateDownloadTrendAggregation('daily')" 
                                            :class="{ 'active': downloadTrendAggregation === 'daily' }">Daily</button>
                                    <button class="dropdown-item" wire:click="updateDownloadTrendAggregation('weekly')" 
                                            :class="{ 'active': downloadTrendAggregation === 'weekly' }">Weekly</button>
                                    <button class="dropdown-item" wire:click="updateDownloadTrendAggregation('monthly')" 
                                            :class="{ 'active': downloadTrendAggregation === 'monthly' }">Monthly</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="downloadTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Charts Row -->
            <div class="row">
                <!-- Active User Trend Area Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Active User Trend (Last 30 Days)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="activeUserTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Activity Trend Line Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">System Activity Trend (Last 30 Days)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="systemActivityTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Lists Row -->
            <div class="row">
                <!-- Recently Added Papers -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recently Added Papers</h6>
                        </div>
                        <div class="card-body">
                            @if($recentlyAddedPapers && count($recentlyAddedPapers) > 0)
                                <div class="list-group">
                                    @foreach($recentlyAddedPapers as $paper)
                                        <a href="{{ route('admin.papers.show', $paper->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $paper->title }}</h6>
                                                <small>{{ $paper->created_at->diffForHumans() }}</small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No recently added papers found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent High Impact Audit Event -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent High Impact Event</h6>
                        </div>
                        <div class="card-body">
                            @if($recentHighImpactAuditEvent)
                                <div class="alert alert-{{ $recentHighImpactAuditEvent->level === 'critical' ? 'danger' : 'warning' }}">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $recentHighImpactAuditEvent->description }}</strong>
                                        <small>{{ $recentHighImpactAuditEvent->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($recentHighImpactAuditEvent->user_id)
                                        <div class="mt-2">
                                            <small>User ID: {{ $recentHighImpactAuditEvent->user_id }}</small>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">No high impact audit events found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:load', function() {
        // Papers by Department Pie Chart
        const papersByDepartmentCtx = document.getElementById('papersByDepartmentChart').getContext('2d');
        const papersByDepartmentChart = new Chart(papersByDepartmentCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(@json($papersByDepartmentData)),
                datasets: [{
                    data: Object.values(@json($papersByDepartmentData)),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#858796', '#3a3b45', '#2e59d9', '#17a673'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617',
                        '#42444e', '#6b6d7d', '#2a2b32', '#2449b3', '#11865e'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
            }
        });

        // Download Trends Line Chart
        const downloadTrendsCtx = document.getElementById('downloadTrendsChart').getContext('2d');
        const downloadTrendsChart = new Chart(downloadTrendsCtx, {
            type: 'line',
            data: {
                labels: @json($downloadTrendsData['labels']),
                datasets: [{
                    label: "Downloads",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($downloadTrendsData['values']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return 'Downloads: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        // Active User Trend Area Chart
        const activeUserTrendCtx = document.getElementById('activeUserTrendChart').getContext('2d');
        const activeUserTrendChart = new Chart(activeUserTrendCtx, {
            type: 'line',
            data: {
                labels: @json($activeUserTrendData['labels']),
                datasets: [{
                    label: "Active Users",
                    lineTension: 0.3,
                    backgroundColor: "rgba(28, 200, 138, 0.2)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointBorderColor: "rgba(28, 200, 138, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: 'origin',
                    data: @json($activeUserTrendData['values']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return 'Active Users: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        // System Activity Trend Line Chart
        const systemActivityTrendCtx = document.getElementById('systemActivityTrendChart').getContext('2d');
        const systemActivityTrendChart = new Chart(systemActivityTrendCtx, {
            type: 'line',
            data: {
                labels: @json($systemActivityTrendData['labels']),
                datasets: @json($systemActivityTrendData['datasets']).map(dataset => ({
                    ...dataset,
                    lineTension: 0.3,
                    backgroundColor: "rgba(54, 185, 204, 0.2)",
                    borderColor: "rgba(54, 185, 204, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(54, 185, 204, 1)",
                    pointBorderColor: "rgba(54, 185, 204, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                    pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: 'origin',
                })),
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        // Livewire event listeners for chart updates
        Livewire.on('download-trends-updated', (data) => {
            downloadTrendsChart.data.labels = data.labels;
            downloadTrendsChart.data.datasets[0].data = data.values;
            downloadTrendsChart.update();
        });

        Livewire.on('dashboard-refreshed', () => {
            // Update all charts when dashboard is refreshed
            papersByDepartmentChart.data.datasets[0].data = Object.values(@json($papersByDepartmentData));
            papersByDepartmentChart.update();
            
            downloadTrendsChart.data.datasets[0].data = @json($downloadTrendsData['values']);
            downloadTrendsChart.update();
            
            activeUserTrendChart.data.datasets[0].data = @json($activeUserTrendData['values']);
            activeUserTrendChart.update();
            
            systemActivityTrendChart.data.datasets = @json($systemActivityTrendData['datasets']).map(dataset => ({
                ...dataset,
                lineTension: 0.3,
                backgroundColor: "rgba(54, 185, 204, 0.2)",
                borderColor: "rgba(54, 185, 204, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(54, 185, 204, 1)",
                pointBorderColor: "rgba(54, 185, 204, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: 'origin',
            }));
            systemActivityTrendChart.update();
        });
    });
</script>
@endpush