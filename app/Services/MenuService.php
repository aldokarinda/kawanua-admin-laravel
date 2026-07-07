<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    public function getMenusTree()
    {
        return Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
    }

    public function getParentMenus($excludeId = null)
    {
        $query = Menu::whereNull('parent_id');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->get();
    }

    public function createMenu(array $data)
    {
        return Menu::create($data);
    }

    public function updateMenu(Menu $menu, array $data)
    {
        return $menu->update($data);
    }

    public function deleteMenu(Menu $menu)
    {
        return $menu->delete();
    }
}
