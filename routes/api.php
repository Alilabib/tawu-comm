<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chat API routes
Route::prefix('chat')->group(function () {
    Route::post('/store-user', [ChatController::class, 'storeUser']);
    Route::post('/update-action', [ChatController::class, 'updateAction']);
    Route::post('/update-program', [ChatController::class, 'updateProgram']);
    Route::post('/update-registration-decision', [ChatController::class, 'updateRegistrationDecision']);
    Route::post('/complete-session', [ChatController::class, 'completeSession']);
    Route::get('/stats', [ChatController::class, 'getStats']);
});
