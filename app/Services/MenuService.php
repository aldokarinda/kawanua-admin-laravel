<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    /**
     * Get active menu tree for sidebar (cached safely as array, hydrated on load).
     */
    public function getActiveMenus()
    {
        $raw = cache()->remember('sidebar_menus_active_v2', 600, function () {
            return Menu::with(['children' => function($q) {
                    $q->where('is_active', true)->orderBy('order');
                }])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->toArray();
        });

        if (!is_array($raw)) {
            cache()->forget('sidebar_menus_active_v2');
            return Menu::with(['children' => function($q) {
                    $q->where('is_active', true)->orderBy('order');
                }])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }

        return Menu::hydrate($raw)->map(function ($menu) use ($raw) {
            $item = collect($raw)->firstWhere('id', $menu->id);
            unset($menu->children);
            unset($menu['children']);
            if (isset($item['children'])) {
                $childrenData = $item['children'];
                $hydratedChildren = Menu::hydrate($childrenData)->map(function ($child) {
                    unset($child->children);
                    unset($child['children']);
                    return $child;
                });
                $menu->setRelation('children', $hydratedChildren);
            } else {
                $menu->setRelation('children', collect());
            }
            return $menu;
        });
    }

    /**
     * Full menu tree for the admin Menu Builder page (cached safely as array, hydrated on load).
     */
    public function getMenusTree()
    {
        $raw = cache()->remember('menus_tree_v2', 120, function () {
            return Menu::with('children')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get()
                ->toArray();
        });

        if (!is_array($raw)) {
            cache()->forget('menus_tree_v2');
            return Menu::with('children')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
        }

        return Menu::hydrate($raw)->map(function ($menu) use ($raw) {
            $item = collect($raw)->firstWhere('id', $menu->id);
            unset($menu->children);
            unset($menu['children']);
            if (isset($item['children'])) {
                $childrenData = $item['children'];
                $hydratedChildren = Menu::hydrate($childrenData)->map(function ($child) {
                    unset($child->children);
                    unset($child['children']);
                    return $child;
                });
                $menu->setRelation('children', $hydratedChildren);
            } else {
                $menu->setRelation('children', collect());
            }
            return $menu;
        });
    }

    public function getParentMenus($excludeId = null)
    {
        $query = Menu::whereNull('parent_id');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->get();
    }

    public function createMenu(array $data): Menu
    {
        $menu = Menu::create($data);
        $this->clearMenuCache();
        return $menu;
    }

    public function updateMenu(Menu $menu, array $data): Menu
    {
        $menu->update($data);
        $this->clearMenuCache();
        return $menu;
    }

    public function deleteMenu(Menu $menu): bool
    {
        $result = $menu->delete();
        $this->clearMenuCache();
        return $result;
    }

    /**
     * Clear menu cache after modifications.
     */
    protected function clearMenuCache(): void
    {
        cache()->forget('sidebar_menus_active_v2');
        cache()->forget('menus_tree_v2');
    }
}
