<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\Admin\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MenuController;

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SecurityController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::resource('users', UserController::class);
    Route::post('roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('menus', MenuController::class);
    Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit_logs.index');

    // Security Module
    Route::prefix('security')->name('security.')->middleware('role:Super Admin')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');

        // Login History
        Route::get('login-history', [SecurityController::class, 'loginHistory'])->name('login-history');
        Route::get('login-history/{user}', [SecurityController::class, 'userLoginHistory'])->name('login-history.user');

        // Two-Factor Authentication
        Route::get('2fa', [SecurityController::class, 'twoFactor'])->name('2fa');
        Route::get('2fa/setup/{user}', [SecurityController::class, 'twoFactorSetup'])->name('2fa-setup');
        Route::post('2fa/enable/{user}', [SecurityController::class, 'twoFactorEnable'])->name('2fa-enable');
        Route::delete('2fa/disable/{user}', [SecurityController::class, 'twoFactorDisable'])->name('2fa-disable');
        Route::post('2fa/reset/{user}', [SecurityController::class, 'twoFactorReset'])->name('2fa-reset');

        // Security Settings
        Route::get('settings', [SecurityController::class, 'settings'])->name('settings');
        Route::post('settings', [SecurityController::class, 'updateSettings'])->name('settings.update');

        // IP Restrictions
        Route::get('ip-restrictions', [SecurityController::class, 'ipRestrictions'])->name('ip-restrictions');
        Route::post('ip-restrictions', [SecurityController::class, 'ipRestrictionStore'])->name('ip-restrictions.store');
        Route::put('ip-restrictions/{ipRestriction}', [SecurityController::class, 'ipRestrictionUpdate'])->name('ip-restrictions.update');
        Route::delete('ip-restrictions/{ipRestriction}', [SecurityController::class, 'ipRestrictionDestroy'])->name('ip-restrictions.destroy');
        Route::post('ip-restrictions/{ipRestriction}/toggle', [SecurityController::class, 'ipRestrictionToggle'])->name('ip-restrictions.toggle');

        // Activity Log
        Route::get('activity-log', [SecurityController::class, 'activityLog'])->name('activity-log');

        // Sessions
        Route::get('sessions', [SecurityController::class, 'sessions'])->name('sessions');
        Route::delete('sessions/{sessionId}', [SecurityController::class, 'destroySession'])->name('sessions.destroy');
        Route::post('sessions/flush', [SecurityController::class, 'flushSessions'])->name('sessions.flush');
    });
});
