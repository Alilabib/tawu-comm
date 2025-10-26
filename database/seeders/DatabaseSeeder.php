<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'phone' => '+966500000000',
            'email' => 'admin@tawuniya.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Create sample users
        $users = [
            [
                'name' => 'Ahmed Al-Rashid',
                'phone' => '+966501234567',
                'email' => 'ahmed@example.com',
            ],
            [
                'name' => 'Fatima Al-Zahra',
                'phone' => '+966507654321',
                'email' => 'fatima@example.com',
            ],
            [
                'name' => 'Mohammed Al-Saud',
                'phone' => '+966509876543',
                'email' => null,
            ],
            [
                'name' => 'Aisha Al-Qasimi',
                'phone' => '+966502468135',
                'email' => 'aisha@example.com',
            ],
            [
                'name' => 'Omar Al-Farisi',
                'phone' => '+966508642097',
                'email' => null,
            ],
        ];

        $programs = ['cdm', 'well-baby', 'geriatric', 'womens-health'];
        $actions = ['inquire', 'register'];
        $decisions = ['register', 'explore'];
        $statuses = ['registration', 'action-selection', 'program-selection', 'case-manager', 'completed'];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Create 1-3 sessions per user
            $sessionCount = rand(1, 3);
            for ($i = 0; $i < $sessionCount; $i++) {
                $startedAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                $status = $statuses[array_rand($statuses)];
                $completedAt = in_array($status, ['completed']) ? $startedAt->copy()->addMinutes(rand(5, 45)) : null;

                UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => Str::uuid(),
                    'selected_action' => rand(0, 1) ? $actions[array_rand($actions)] : null,
                    'selected_program' => rand(0, 1) ? $programs[array_rand($programs)] : null,
                    'registration_decision' => rand(0, 1) ? $decisions[array_rand($decisions)] : null,
                    'status' => $status,
                    'conversation_data' => [
                        'messages_count' => rand(5, 20),
                        'user_responses' => rand(2, 8),
                        'session_duration' => rand(300, 2700), // 5-45 minutes in seconds
                    ],
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                ]);
            }
        }
    }
}
