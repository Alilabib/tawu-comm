<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and recent sessions.
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalSessions = UserSession::count();
        $completedSessions = UserSession::where('status', 'completed')->count();
        $activeUsers = User::whereHas('sessions', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->count();

        // Program statistics
        $programStats = UserSession::select('selected_program', DB::raw('count(*) as count'))
            ->whereNotNull('selected_program')
            ->groupBy('selected_program')
            ->get()
            ->mapWithKeys(function ($item) {
                $programs = [
                    'cdm' => 'Chronic Disease Management',
                    'well-baby' => 'Well-Baby Program',
                    'geriatric' => 'Geriatric Care',
                    'womens-health' => 'Women\'s Health',
                ];
                return [$programs[$item->selected_program] ?? $item->selected_program => $item->count];
            });

        // Action statistics
        $actionStats = UserSession::select('selected_action', DB::raw('count(*) as count'))
            ->whereNotNull('selected_action')
            ->groupBy('selected_action')
            ->get()
            ->mapWithKeys(function ($item) {
                $actions = [
                    'inquire' => 'Inquire about program',
                    'register' => 'Register to program',
                ];
                return [$actions[$item->selected_action] ?? $item->selected_action => $item->count];
            });

        // Recent sessions
        $recentSessions = UserSession::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalSessions',
            'completedSessions',
            'activeUsers',
            'programStats',
            'actionStats',
            'recentSessions'
        ));
    }

    /**
     * Display all users with their session data.
     */
    public function users()
    {
        $users = User::with(['sessions' => function ($query) {
            $query->latest();
        }])->paginate(20);

        return view('dashboard.users', compact('users'));
    }

    /**
     * Display all sessions with filtering options.
     */
    public function sessions(Request $request)
    {
        $query = UserSession::with('user');

        // Filter by program
        if ($request->filled('program')) {
            $query->where('selected_program', $request->program);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('selected_action', $request->action);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dashboard.sessions', compact('sessions'));
    }

    /**
     * Display detailed view of a specific session.
     */
    public function sessionDetail(UserSession $session)
    {
        $session->load('user');
        return view('dashboard.session-detail', compact('session'));
    }
}
