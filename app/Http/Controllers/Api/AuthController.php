<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected ApiTokenService $tokenService)
    {
    }

    // -------------------------------------------------------------------------
    // POST /api/auth/login
    // -------------------------------------------------------------------------

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact an administrator.',
            ], 403);
        }

        $tokens = $this->tokenService->issueTokenPair($user);

        return response()->json([
            'message' => 'Login successful.',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'roles'      => $user->getRoleNames(),
            ],
            ...$tokens,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/auth/refresh
    // -------------------------------------------------------------------------

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $tokens = $this->tokenService->refreshTokenPair($request->input('refresh_token'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 401);
        }

        return response()->json([
            'message' => 'Token refreshed successfully.',
            ...$tokens,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/auth/logout    (requires: auth:sanctum)
    // -------------------------------------------------------------------------

    public function logout(Request $request): JsonResponse
    {
        $this->tokenService->revokeAll($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // -------------------------------------------------------------------------
    // GET /api/auth/me         (requires: auth:sanctum)
    // -------------------------------------------------------------------------

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return response()->json([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'department'  => $user->department,
            'phone'       => $user->phone_number,
            'is_active'   => $user->is_active,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'created_at'  => $user->created_at,
        ]);
    }
}
