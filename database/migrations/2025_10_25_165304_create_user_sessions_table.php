<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->enum('selected_action', ['inquire', 'register'])->nullable();
            $table->enum('selected_program', ['cdm', 'well-baby', 'geriatric', 'womens-health'])->nullable();
            $table->enum('registration_decision', ['register', 'explore'])->nullable();
            $table->enum('status', ['registration', 'action-selection', 'program-selection', 'case-manager', 'completed'])->default('registration');
            $table->json('conversation_data')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
