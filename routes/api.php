<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Kawanua Admin Panel
|--------------------------------------------------------------------------
|
| Auth routes use Refresh Token Rotation via Sanctum personal access tokens.
| Access tokens expire in 15 minutes, Refresh tokens in 7 days.
| Using a revoked refresh token triggers full breach containment (revoke all).
|
*/

Route::prefix('auth')->group(function () {
    // Public — no token needed
    Route::post('login',   [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Protected — requires valid access token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});
