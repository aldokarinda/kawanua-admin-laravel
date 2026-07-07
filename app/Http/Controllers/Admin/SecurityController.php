<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\IpRestrictionService;
use App\Services\LoginHistoryService;
use App\Services\SecuritySettingService;
use App\Services\TwoFactorAuthService;
use App\Models\IpRestriction;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    protected LoginHistoryService $loginHistoryService;
    protected TwoFactorAuthService $twoFactorAuthService;
    protected SecuritySettingService $securitySettingService;
    protected IpRestrictionService $ipRestrictionService;
    protected AuditLogService $auditLogService;

    public function __construct(
        LoginHistoryService $loginHistoryService,
        TwoFactorAuthService $twoFactorAuthService,
        SecuritySettingService $securitySettingService,
        IpRestrictionService $ipRestrictionService,
        AuditLogService $auditLogService
    ) {
        $this->loginHistoryService = $loginHistoryService;
        $this->twoFactorAuthService = $twoFactorAuthService;
        $this->securitySettingService = $securitySettingService;
        $this->ipRestrictionService = $ipRestrictionService;
        $this->auditLogService = $auditLogService;
    }

    // ========================================================================
    // Security Dashboard
    // ========================================================================

    public function index()
    {
        $loginStats = $this->loginHistoryService->getStats();
        $ipStats = $this->ipRestrictionService->getStats();
        $recentFailures = $this->loginHistoryService->getRecentFailures(10);

        return view('admin.security.index', compact('loginStats', 'ipStats', 'recentFailures'));
    }

    // ========================================================================
    // Login History
    // ========================================================================

    public function loginHistory(Request $request)
    {
        $logs = $this->loginHistoryService->getPaginatedLogs(
            userId: $request->input('user_id'),
            status: $request->input('status'),
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            perPage: 25
        );

        $stats = $this->loginHistoryService->getStats();
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.security.login-history', compact('logs', 'stats', 'users'));
    }

    public function userLoginHistory(User $user, Request $request)
    {
        $logs = $this->loginHistoryService->getUserLoginHistory($user);
        return view('admin.security.login-history', compact('logs', 'user'))->with('singleUser', true);
    }

    // ========================================================================
    // Two-Factor Authentication
    // ========================================================================

    public function twoFactor()
    {
        $users = User::with('twoFactorAuth')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'enabled_count' => \App\Models\TwoFactorAuth::where('enabled', true)->count(),
            'total_users' => User::count(),
        ];

        return view('admin.security.2fa', compact('users', 'stats'));
    }

    public function twoFactorSetup(User $user)
    {
        $data = $this->twoFactorAuthService->generateSecret($user);
        return view('admin.security.2fa-setup', array_merge($data, ['user' => $user]));
    }

    public function twoFactorEnable(User $user, Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        if ($this->twoFactorAuthService->enable($user, $request->code)) {
            return redirect()
                ->route('admin.security.2fa')
                ->with('success', '2FA enabled successfully for ' . $user->name);
        }

        return redirect()->back()->with('error', 'Invalid verification code.');
    }

    public function twoFactorDisable(User $user)
    {
        $this->twoFactorAuthService->disable($user);
        return redirect()
            ->route('admin.security.2fa')
            ->with('success', '2FA disabled for ' . $user->name);
    }

    public function twoFactorReset(User $user)
    {
        $this->twoFactorAuthService->disable($user);
        return redirect()
            ->route('admin.security.2fa-setup', $user)
            ->with('success', '2FA has been reset. Please set up again.');
    }

    // ========================================================================
    // Security Settings
    // ========================================================================

    public function settings()
    {
        $groupedSettings = $this->securitySettingService->getGroupedSettings();
        return view('admin.security.settings', compact('groupedSettings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        $this->securitySettingService->bulkUpdate($validated['settings']);

        return redirect()
            ->route('admin.security.settings')
            ->with('success', 'Security settings updated successfully.');
    }

    // ========================================================================
    // IP Restrictions
    // ========================================================================

    public function ipRestrictions(Request $request)
    {
        $type = $request->input('type', 'blacklist');
        $restrictions = $this->ipRestrictionService->getPaginatedRestrictions($type);
        $stats = $this->ipRestrictionService->getStats();

        return view('admin.security.ip-restrictions', compact('restrictions', 'stats', 'type'));
    }

    public function ipRestrictionStore(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|string|max:45',
            'type' => 'required|in:whitelist,blacklist',
            'reason' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $this->ipRestrictionService->createRestriction($validated);

        return redirect()
            ->route('admin.security.ip-restrictions', ['type' => $validated['type']])
            ->with('success', 'IP restriction added successfully.');
    }

    public function ipRestrictionUpdate(Request $request, IpRestriction $ipRestriction)
    {
        $validated = $request->validate([
            'ip_address' => 'required|string|max:45',
            'reason' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $this->ipRestrictionService->updateRestriction($ipRestriction, $validated);

        return redirect()
            ->route('admin.security.ip-restrictions', ['type' => $ipRestriction->type])
            ->with('success', 'IP restriction updated successfully.');
    }

    public function ipRestrictionDestroy(IpRestriction $ipRestriction)
    {
        $type = $ipRestriction->type;
        $this->ipRestrictionService->deleteRestriction($ipRestriction);

        return redirect()
            ->route('admin.security.ip-restrictions', ['type' => $type])
            ->with('success', 'IP restriction removed successfully.');
    }

    public function ipRestrictionToggle(IpRestriction $ipRestriction)
    {
        $this->ipRestrictionService->toggleStatus($ipRestriction);

        return redirect()
            ->route('admin.security.ip-restrictions', ['type' => $ipRestriction->type])
            ->with('success', 'IP restriction status toggled.');
    }

    // ========================================================================
    // Activity Log (Enhanced Audit Log)
    // ========================================================================

    public function activityLog(Request $request)
    {
        $module = $request->input('module');
        $action = $request->input('action');
        $logs = $this->auditLogService->getPaginatedLogs($module, $action, 25);

        $modules = \App\Models\AuditLog::select('module')->distinct()->pluck('module');
        $actions = \App\Models\AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.security.activity-log', compact('logs', 'modules', 'actions', 'module', 'action'));
    }

    // ========================================================================
    // Session Management
    // ========================================================================

    public function sessions()
    {
        // Get active sessions from database driver
        $sessions = [];
        if (config('session.driver') === 'database') {
            $sessions = \DB::table('sessions')
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    $session->payload = unserialize(base64_decode($session->payload));
                    return $session;
                });
        }

        return view('admin.security.sessions', compact('sessions'));
    }

    public function destroySession(string $sessionId)
    {
        \DB::table('sessions')->where('id', $sessionId)->delete();
        return redirect()
            ->route('admin.security.sessions')
            ->with('success', 'Session terminated successfully.');
    }

    public function flushSessions()
    {
        \DB::table('sessions')->truncate();
        return redirect()
            ->route('admin.security.sessions')
            ->with('success', 'All sessions have been terminated. Users will need to log in again.');
    }
}
