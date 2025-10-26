@extends('layouts.app')

@section('title', 'Users - Tawuniya PHM Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Users Management</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportUsers()">
            <i class="fas fa-download me-1"></i>
            Export
        </button>
    </div>
</div>

<!-- Users Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Users</h6>
                        <h4 class="mb-0">{{ $users->total() }}</h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Active Users</h6>
                        <h4 class="mb-0">{{ $users->where('sessions_count', '>', 0)->count() }}</h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">New Users Today</h6>
                        <h4 class="mb-0">{{ $users->where('created_at', '>=', today())->count() }}</h4>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>
            All Users
        </h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Sessions</th>
                            <th>Last Activity</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                    {{ $user->phone }}
                                </a>
                            </td>
                            <td>
                                @if($user->email)
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $user->sessions->count() }}</span>
                            </td>
                            <td>
                                @if($user->sessions->count() > 0)
                                    {{ $user->sessions->first()->created_at->diffForHumans() }}
                                @else
                                    <span class="text-muted">No activity</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewUserDetails({{ $user->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($user->sessions->count() > 0)
                                        <a href="{{ route('dashboard.sessions', ['user_id' => $user->id]) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-users fa-4x mb-3"></i>
                <h5>No Users Found</h5>
                <p>Users will appear here once they start using the chat application.</p>
            </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--tawuniya-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
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
function viewUserDetails(userId) {
    // This would typically make an AJAX call to get user details
    // For now, we'll show a placeholder
    document.getElementById('userDetailsContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading user details...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    modal.show();
    
    // Simulate loading
    setTimeout(() => {
        document.getElementById('userDetailsContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                User details functionality will be implemented in the next phase.
            </div>
        `;
    }, 1000);
}

function exportUsers() {
    // This would typically trigger a download
    alert('Export functionality will be implemented in the next phase.');
}
</script>
@endpush
