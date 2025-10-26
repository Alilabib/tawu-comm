@extends('layouts.app')

@section('title', 'Chat Sessions - Tawuniya PHM Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Chat Sessions</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="toggleFilters()">
            <i class="fas fa-filter me-1"></i>
            Filters
        </button>
        <button class="btn btn-outline-primary" onclick="exportSessions()">
            <i class="fas fa-download me-1"></i>
            Export
        </button>
    </div>
</div>

<!-- Filters Panel -->
<div class="card mb-4" id="filtersPanel" style="display: none;">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filter Sessions
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.sessions') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Program</label>
                    <select name="program" class="form-select">
                        <option value="">All Programs</option>
                        <option value="cdm" {{ request('program') == 'cdm' ? 'selected' : '' }}>
                            Chronic Disease Management
                        </option>
                        <option value="well-baby" {{ request('program') == 'well-baby' ? 'selected' : '' }}>
                            Well-Baby Program
                        </option>
                        <option value="geriatric" {{ request('program') == 'geriatric' ? 'selected' : '' }}>
                            Geriatric Care
                        </option>
                        <option value="womens-health" {{ request('program') == 'womens-health' ? 'selected' : '' }}>
                            Women's Health
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="inquire" {{ request('action') == 'inquire' ? 'selected' : '' }}>
                            Inquire about program
                        </option>
                        <option value="register" {{ request('action') == 'register' ? 'selected' : '' }}>
                            Register to program
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="registration" {{ request('status') == 'registration' ? 'selected' : '' }}>
                            Registration
                        </option>
                        <option value="action-selection" {{ request('status') == 'action-selection' ? 'selected' : '' }}>
                            Action Selection
                        </option>
                        <option value="program-selection" {{ request('status') == 'program-selection' ? 'selected' : '' }}>
                            Program Selection
                        </option>
                        <option value="case-manager" {{ request('status') == 'case-manager' ? 'selected' : '' }}>
                            Case Manager
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Completed
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('dashboard.sessions') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sessions Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Sessions</h6>
                        <h4 class="mb-0">{{ $sessions->total() }}</h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Completed</h6>
                        <h4 class="mb-0">{{ $sessions->where('status', 'completed')->count() }}</h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">In Progress</h6>
                        <h4 class="mb-0">{{ $sessions->whereNotIn('status', ['completed'])->count() }}</h4>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Today's Sessions</h6>
                        <h4 class="mb-0">{{ $sessions->where('created_at', '>=', today())->count() }}</h4>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sessions Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            All Sessions
        </h5>
    </div>
    <div class="card-body">
        @if($sessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Program</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>
                                <code>{{ substr($session->session_id, 0, 8) }}...</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ strtoupper(substr($session->user->name, 0, 2)) }}
                                    </div>
                                    <strong>{{ $session->user->name }}</strong>
                                </div>
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
                                @if($session->completed_at)
                                    {{ $session->started_at->diffInMinutes($session->completed_at) }}m
                                @else
                                    <span class="text-muted">Ongoing</span>
                                @endif
                            </td>
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sessions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-comments fa-4x mb-3"></i>
                <h5>No Sessions Found</h5>
                <p>Chat sessions will appear here once users start interacting with the application.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: var(--tawuniya-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}

.stats-card {
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}

function exportSessions() {
    alert('Export functionality will be implemented in the next phase.');
}
</script>
@endpush
