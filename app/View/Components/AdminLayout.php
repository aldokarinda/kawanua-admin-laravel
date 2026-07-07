<?php

namespace App\View\Components;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class AdminLayout extends Component
{
    public function render(): View
    {
        $user = Auth::user();

        $menus = Menu::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function($q) {
                $q->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get()
            ->filter(fn (Menu $menu) => $this->canView($menu->permission_name, $user))
            ->map(function (Menu $menu) use ($user) {
                $menu->setRelation(
                    'children',
                    $menu->children
                        ->filter(fn ($item) => $this->canView($item->permission_name, $user))
                        ->values()
                );
                return $menu;
            })
            ->filter(fn (Menu $menu) => $menu->children->isNotEmpty() || $menu->route_name || $menu->url)
            ->values();

        return view('layouts.admin', ['sidebarMenus' => $menus]);
    }

    private function canView(?string $permission, $user): bool
    {
        if (blank($permission)) {
            return true;
        }

        return $user?->can($permission) ?? false;
    }
}
