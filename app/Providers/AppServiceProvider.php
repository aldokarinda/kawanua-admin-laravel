<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Observers\UserObserver;
use App\Observers\RoleObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lazy loading optimization for service bindings
        $this->app->bind(\App\Services\UserService::class, function () {
            return new \App\Services\UserService();
        });

        $this->app->bind(\App\Services\RoleService::class, function () {
            return new \App\Services\RoleService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        
        // Dynamically override APP_NAME from database setting
        if (Schema::hasTable('app_settings')) {
            config(['app.name' => \App\Models\AppSetting::get('app_name', config('app.name'))]);
        }

        // Force HTTPS in Production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Define global password security defaults
        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        // Share sidebar menus to all admin views (cached)
        View::composer(['layouts.admin', 'components.admin-layout'], function ($view) {
            $menuService = app(\App\Services\MenuService::class);
            $view->with('sidebarMenus', $menuService->getActiveMenus());
        });
    }
}
