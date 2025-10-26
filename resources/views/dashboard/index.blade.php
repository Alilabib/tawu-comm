@extends('layouts.app')

@section('title', 'Dashboard Overview - Med PHM')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard Overview</h1>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        {{ now()->format('F j, Y') }}
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Users</h6>
                        <h3 class="mb-0">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Sessions</h6>
                        <h3 class="mb-0">{{ number_format($totalSessions) }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Completed Sessions</h6>
                        <h3 class="mb-0">{{ number_format($completedSessions) }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Active Users (7 days)</h6>
                        <h3 class="mb-0">{{ number_format($activeUsers) }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Program Statistics -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Program Selection Statistics
                </h5>
            </div>
            <div class="card-body">
                @if($programStats->count() > 0)
                    <canvas id="programChart" width="400" height="200"></canvas>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>No program data available yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Statistics -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    User Action Statistics
                </h5>
            </div>
            <div class="card-body">
                @if($actionStats->count() > 0)
                    <canvas id="actionChart" width="400" height="200"></canvas>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>No action data available yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Sessions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-clock me-2"></i>
            Recent Sessions
        </h5>
    </div>
    <div class="card-body">
        @if($recentSessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Program</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                        <tr>
                            <td>
                                <strong>{{ $session->user->name }}</strong>
                            </td>
                            <td>{{ $session->user->phone }}</td>
                            <td>
                                @if($session->selected_program)
                                    <span class="badge bg-info">{{ $session->program_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($session->selected_action)
                                    <span class="badge bg-secondary">{{ $session->action_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'registration' => 'warning',
                                        'action-selection' => 'info',
                                        'program-selection' => 'primary',
                                        'case-manager' => 'success',
                                        'completed' => 'success'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$session->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('-', ' ', $session->status)) }}
                                </span>
                            </td>
                            <td>{{ $session->started_at->format('M j, Y H:i') }}</td>
                            <td>
                                <a href="{{ route('dashboard.session-detail', $session) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('dashboard.sessions') }}" class="btn btn-primary">
                    View All Sessions
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>No sessions available yet</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Program Statistics Chart
@if($programStats->count() > 0)
const programCtx = document.getElementById('programChart').getContext('2d');
new Chart(programCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($programStats->keys()) !!},
        datasets: [{
            data: {!! json_encode($programStats->values()) !!},
            backgroundColor: [
                '#25d366',
                '#128c7e',
                '#075e54',
                '#dcf8c6'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endif

// Action Statistics Chart
@if($actionStats->count() > 0)
const actionCtx = document.getElementById('actionChart').getContext('2d');
new Chart(actionCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($actionStats->keys()) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode($actionStats->values()) !!},
            backgroundColor: '#25d366',
            borderColor: '#128c7e',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
@endif
</script>
@endpush
