<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tawuniya PHM Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --tawuniya-primary: #25d366;
            --tawuniya-secondary: #128c7e;
            --tawuniya-dark: #075e54;
            --tawuniya-light: #dcf8c6;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--tawuniya-dark) !important;
        }
        
        .nav-link.active {
            background-color: var(--tawuniya-primary) !important;
            color: white !important;
            border-radius: 5px;
        }
        
        .card-header {
            background-color: var(--tawuniya-primary);
            color: white;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--tawuniya-primary);
            border-color: var(--tawuniya-primary);
        }
        
        .btn-primary:hover {
            background-color: var(--tawuniya-secondary);
            border-color: var(--tawuniya-secondary);
        }
        
        .stats-card {
            border-left: 4px solid var(--tawuniya-primary);
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            background-color: #f8f9fa;
            min-height: calc(100vh - 56px);
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.375rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--tawuniya-light);
            color: var(--tawuniya-dark);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--tawuniya-primary);
            color: white;
        }
        
        .table th {
            background-color: var(--tawuniya-light);
            color: var(--tawuniya-dark);
            font-weight: 600;
        }
        
        .badge-success {
            background-color: var(--tawuniya-primary);
        }
        
        .badge-warning {
            background-color: #ffc107;
        }
        
        .badge-info {
            background-color: #17a2b8;
        }
        
        .badge-secondary {
            background-color: #6c757d;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard.index') }}">
                <i class="fas fa-heartbeat me-2"></i>
                Tawuniya PHM Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.index') }}">
                            <i class="fas fa-chart-dashboard me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.users') }}">
                            <i class="fas fa-users me-1"></i>
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.sessions') }}">
                            <i class="fas fa-comments me-1"></i>
                            Sessions
                        </a>
                    </li>
                </ul>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" 
                       href="{{ route('dashboard.index') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Overview
                    </a>
                    <a class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}" 
                       href="{{ route('dashboard.users') }}">
                        <i class="fas fa-users me-2"></i>
                        Users
                    </a>
                    <a class="nav-link {{ request()->routeIs('dashboard.sessions') ? 'active' : '' }}" 
                       href="{{ route('dashboard.sessions') }}">
                        <i class="fas fa-comments me-2"></i>
                        Chat Sessions
                    </a>
                </nav>
            </div>

            <!-- Content -->
            <div class="col-md-10 p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
