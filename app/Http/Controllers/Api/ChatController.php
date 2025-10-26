<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Store user registration data and create a new session.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->name,
                'phone' => $request->phone,
            ]
        );

        // Create new session
        $session = UserSession::create([
            'user_id' => $user->id,
            'session_id' => Str::uuid(),
            'status' => 'registration',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
            'session_id' => $session->session_id,
        ]);
    }

    /**
     * Update session with action selection.
     */
    public function updateAction(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'action' => 'required|in:inquire,register',
        ]);

        $session = UserSession::where('session_id', $request->session_id)->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $session->update([
            'selected_action' => $request->action,
            'status' => 'action-selection',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update session with program selection.
     */
    public function updateProgram(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'program' => 'required|in:cdm,well-baby,geriatric,womens-health',
        ]);

        $session = UserSession::where('session_id', $request->session_id)->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $session->update([
            'selected_program' => $request->program,
            'status' => 'program-selection',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update session with registration decision.
     */
    public function updateRegistrationDecision(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'decision' => 'required|in:register,explore',
        ]);

        $session = UserSession::where('session_id', $request->session_id)->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $status = $request->decision === 'register' ? 'case-manager' : 'completed';

        $session->update([
            'registration_decision' => $request->decision,
            'status' => $status,
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Complete the session.
     */
    public function completeSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'conversation_data' => 'nullable|array',
        ]);

        $session = UserSession::where('session_id', $request->session_id)->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $session->update([
            'status' => 'completed',
            'conversation_data' => $request->conversation_data,
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get session statistics for the dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_sessions' => UserSession::count(),
            'completed_sessions' => UserSession::where('status', 'completed')->count(),
            'active_sessions' => UserSession::whereIn('status', ['registration', 'action-selection', 'program-selection', 'case-manager'])->count(),
            'program_stats' => UserSession::selectRaw('selected_program, COUNT(*) as count')
                ->whereNotNull('selected_program')
                ->groupBy('selected_program')
                ->get(),
            'action_stats' => UserSession::selectRaw('selected_action, COUNT(*) as count')
                ->whereNotNull('selected_action')
                ->groupBy('selected_action')
                ->get(),
        ];

        return response()->json($stats);
    }
}
