<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


// ========== PUBLIC AUTH ROUTES ==========
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Kalau pakai sanctum / token, bisa dibungkus middleware auth:sanctum

Route::prefix('sync')->group(function () {
    Route::get('pull', [SyncController::class, 'pull']);  // GET /api/sync/pull?since=...
    Route::post('push', [SyncController::class, 'push']); // POST /api/sync/push
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
