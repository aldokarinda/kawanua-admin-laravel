<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppSettingService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Helpers\ImageHelper;

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
            'settings.app_name' => 'required|string|max:255',
            'settings.app_description' => 'nullable|string|max:255',
            'settings.app_theme' => 'required|string|in:indigo,blue,emerald,purple,rose,white',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $settingsData = $validated['settings'];
        $oldSettings = [];
        foreach ($settingsData as $key => $val) {
            $oldSettings[$key] = $this->appSettingService->get($key);
        }
        $oldSettings['app_logo'] = $this->appSettingService->get('app_logo');

        // Handle file upload
        if ($request->hasFile('app_logo')) {
            $logoPath = ImageHelper::resizeAndCropToSquare($request->file('app_logo'), 'logos', 128);
            if ($logoPath) {
                $settingsData['app_logo'] = $logoPath;
            }
        }

        // Bulk update
        $this->appSettingService->bulkUpdate($settingsData);

        // Log the audit event
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'module' => 'App Settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => [
                'old' => $oldSettings,
                'new' => $this->appSettingService->getAllSettings()->pluck('value', 'key')->toArray(),
            ]
        ]);

        return redirect()
            ->route('admin.app_settings.index')
            ->with('success', 'Application settings updated successfully.');
    }
}
