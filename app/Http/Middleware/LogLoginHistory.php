<?php

namespace App\Http\Middleware;

use App\Models\SecuritySetting;
use App\Services\LoginHistoryService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogLoginHistory
{
    protected LoginHistoryService $loginHistoryService;

    public function __construct(LoginHistoryService $loginHistoryService)
    {
        $this->loginHistoryService = $loginHistoryService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log POST requests to login route
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            $user = Auth::user();

            if ($user) {
                // Check if 2FA is enforced
                if (SecuritySetting::get('enforce_2fa', false)) {
                    $twoFactorService = app(\App\Services\TwoFactorAuthService::class);
                    if (!$twoFactorService->isEnabled($user)) {
                        $this->loginHistoryService->logFailure(
                            $user,
                            '2fa_required'
                        );
                    }
                }

                $this->loginHistoryService->logSuccess($user);
            } else {
                // Failed login — find user by email
                $email = $request->input('email');
                if ($email) {
                    $targetUser = \App\Models\User::where('email', $email)->first();
                    if ($targetUser) {
                        // Check rate limiting
                        $maxAttempts = SecuritySetting::get('max_login_attempts', 5);
                        $recentFailures = \App\Models\LoginHistory::failed()
                            ->where('user_id', $targetUser->id)
                            ->where('login_at', '>=', now()->subMinutes(
                                SecuritySetting::get('lockout_duration_minutes', 15)
                            ))
                            ->count();

                        if ($recentFailures >= $maxAttempts) {
                            $this->loginHistoryService->logLockout($targetUser);
                        } else {
                            $this->loginHistoryService->logFailure($targetUser, 'invalid_credentials');
                        }
                    } else {
                        $this->loginHistoryService->logAttempt(
                            new \App\Models\User(['id' => null]),
                            'failed',
                            'user_not_found'
                        );
                    }
                }
            }
        }

        return $response;
    }
}
