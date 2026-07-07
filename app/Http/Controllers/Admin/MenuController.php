<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = $this->menuService->getMenusTree();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = $this->menuService->getParentMenus();
        return view('admin.menus.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:menus,slug',
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string',
            'route_name' => 'nullable|string',
            'url' => 'nullable|string',
            'permission_name' => 'nullable|string',
            'order' => 'integer',
        ]);

        $this->menuService->createMenu($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $parents = $this->menuService->getParentMenus($menu->id);
        return view('admin.menus.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:menus,slug,'.$menu->id,
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string',
            'route_name' => 'nullable|string',
            'url' => 'nullable|string',
            'permission_name' => 'nullable|string',
            'order' => 'integer',
        ]);

        $this->menuService->updateMenu($menu, $data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $this->menuService->deleteMenu($menu);
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }
}
