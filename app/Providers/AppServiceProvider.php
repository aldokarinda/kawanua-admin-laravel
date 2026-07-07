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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Schema::defaultStringLength(191);



    }
}
