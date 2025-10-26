<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'selected_action',
        'selected_program',
        'registration_decision',
        'status',
        'conversation_data',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conversation_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program name in a readable format.
     */
    public function getProgramNameAttribute()
    {
        $programs = [
            'cdm' => 'Chronic Disease Management',
            'well-baby' => 'Well-Baby Program',
            'geriatric' => 'Geriatric Care',
            'womens-health' => 'Women\'s Health',
        ];

        return $programs[$this->selected_program] ?? $this->selected_program;
    }

    /**
     * Get the action name in a readable format.
     */
    public function getActionNameAttribute()
    {
        $actions = [
            'inquire' => 'Inquire about the program',
            'register' => 'Register to the program',
        ];

        return $actions[$this->selected_action] ?? $this->selected_action;
    }
}
