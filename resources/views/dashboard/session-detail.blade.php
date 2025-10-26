@extends('layouts.app')

@section('title', 'Session Details - Tawuniya PHM Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Session Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard.sessions') }}">Sessions</a></li>
                <li class="breadcrumb-item active">{{ substr($session->session_id, 0, 8) }}...</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportSession()">
            <i class="fas fa-download me-1"></i>
            Export
        </button>
        <a href="{{ route('dashboard.sessions') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Sessions
        </a>
    </div>
</div>

<!-- Session Overview -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Session Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Session ID:</strong></td>
                                <td><code>{{ $session->session_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>User:</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($session->user->name, 0, 2)) }}
                                        </div>
                                        {{ $session->user->name }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>
                                    <a href="tel:{{ $session->user->phone }}" class="text-decoration-none">
                                        {{ $session->user->phone }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>
                                    @if($session->user->email)
                                        <a href="mailto:{{ $session->user->email }}" class="text-decoration-none">
                                            {{ $session->user->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Status:</strong></td>
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
                            </tr>
                            <tr>
                                <td><strong>Started:</strong></td>
                                <td>{{ $session->started_at->format('M j, Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Completed:</strong></td>
                                <td>
                                    @if($session->completed_at)
                                        {{ $session->completed_at->format('M j, Y H:i:s') }}
                                    @else
                                        <span class="text-muted">Not completed</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>
                                    @if($session->completed_at)
                                        {{ $session->started_at->diffForHumans($session->completed_at, true) }}
                                    @else
                                        <span class="text-muted">{{ $session->started_at->diffForHumans() }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Session Progress
                </h5>
            </div>
            <div class="card-body">
                <div class="progress-steps">
                    @php
                        $steps = [
                            'registration' => 'Registration',
                            'action-selection' => 'Action Selection',
                            'program-selection' => 'Program Selection',
                            'case-manager' => 'Case Manager',
                            'completed' => 'Completed'
                        ];
                        $currentStepIndex = array_search($session->status, array_keys($steps));
                    @endphp
                    
                    @foreach($steps as $stepKey => $stepName)
                        @php
                            $stepIndex = array_search($stepKey, array_keys($steps));
                            $isCompleted = $stepIndex <= $currentStepIndex;
                            $isCurrent = $stepIndex === $currentStepIndex;
                        @endphp
                        
                        <div class="step {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }}">
                            <div class="step-icon">
                                @if($isCompleted)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $stepIndex + 1 }}
                                @endif
                            </div>
                            <div class="step-label">{{ $stepName }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Choices -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-mouse-pointer me-2"></i>
                    User Choices
                </h5>
            </div>
            <div class="card-body">
                <div class="choice-item">
                    <strong>Selected Action:</strong>
                    @if($session->selected_action)
                        <span class="badge bg-secondary ms-2">{{ $session->action_name }}</span>
                    @else
                        <span class="text-muted ms-2">Not selected</span>
                    @endif
                </div>
                
                <div class="choice-item mt-3">
                    <strong>Selected Program:</strong>
                    @if($session->selected_program)
                        <span class="badge bg-info ms-2">{{ $session->program_name }}</span>
                    @else
                        <span class="text-muted ms-2">Not selected</span>
                    @endif
                </div>
                
                <div class="choice-item mt-3">
                    <strong>Registration Decision:</strong>
                    @if($session->registration_decision)
                        @php
                            $decisionColors = [
                                'register' => 'success',
                                'explore' => 'warning'
                            ];
                            $decisionLabels = [
                                'register' => 'Registered',
                                'explore' => 'Just Exploring'
                            ];
                        @endphp
                        <span class="badge bg-{{ $decisionColors[$session->registration_decision] }} ms-2">
                            {{ $decisionLabels[$session->registration_decision] }}
                        </span>
                    @else
                        <span class="text-muted ms-2">Not decided</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Timeline
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6>Session Started</h6>
                            <small class="text-muted">{{ $session->started_at->format('M j, Y H:i:s') }}</small>
                        </div>
                    </div>
                    
                    @if($session->selected_action)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6>Action Selected</h6>
                            <small class="text-muted">{{ $session->action_name }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($session->selected_program)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6>Program Selected</h6>
                            <small class="text-muted">{{ $session->program_name }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($session->registration_decision)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <h6>Registration Decision</h6>
                            <small class="text-muted">
                                {{ $session->registration_decision === 'register' ? 'Decided to register' : 'Just exploring' }}
                            </small>
                        </div>
                    </div>
                    @endif
                    
                    @if($session->completed_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Session Completed</h6>
                            <small class="text-muted">{{ $session->completed_at->format('M j, Y H:i:s') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conversation Data -->
@if($session->conversation_data)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-comments me-2"></i>
            Conversation Data
        </h5>
    </div>
    <div class="card-body">
        <pre class="bg-light p-3 rounded"><code>{{ json_encode($session->conversation_data, JSON_PRETTY_PRINT) }}</code></pre>
    </div>
</div>
@endif
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

.progress-steps {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
}

.step-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.step.completed .step-icon {
    background-color: var(--tawuniya-primary);
    color: white;
}

.step.current .step-icon {
    background-color: #ffc107;
    color: #000;
}

.step-label {
    font-size: 14px;
    font-weight: 500;
}

.step.current .step-label {
    color: var(--tawuniya-primary);
    font-weight: 600;
}

.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -12px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    margin-left: 15px;
}

.timeline-content h6 {
    margin-bottom: 2px;
    font-size: 14px;
}

.choice-item {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.choice-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('scripts')
<script>
function exportSession() {
    alert('Session export functionality will be implemented in the next phase.');
}
</script>
@endpush
