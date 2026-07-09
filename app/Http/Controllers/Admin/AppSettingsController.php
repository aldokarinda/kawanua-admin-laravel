<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppSettingService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AppSettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:setting.view', only: ['index', 'update']),
        ];
    }

    protected AppSettingService $appSettingService;
    protected AuditLogService $auditLogService;

    public function __construct(
        AppSettingService $appSettingService,
        AuditLogService $auditLogService
    ) {
        $this->appSettingService = $appSettingService;
        $this->auditLogService = $auditLogService;
    }

    public function index()
    {
        $settings = $this->appSettingService->getAllSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:255',
        ]);

        $oldSettings = [];
        foreach ($validated['settings'] as $key => $val) {
            $oldSettings[$key] = $this->appSettingService->get($key);
        }

        $this->appSettingService->bulkUpdate($validated['settings']);

        // Log the audit event
        $this->auditLogService->log(
            auth()->id(),
            'Updated',
            'App Settings',
            [
                'old' => $oldSettings,
                'new' => $validated['settings'],
            ]
        );

        return redirect()
            ->route('admin.app_settings.index')
            ->with('success', 'Application settings updated successfully.');
    }
}
