<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MenuController extends Controller implements HasMiddleware
{
    protected $menuService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:menu.view', only: ['index']),
            new Middleware('permission:menu.create', only: ['create', 'store']),
            new Middleware('permission:menu.edit', only: ['edit', 'update']),
            new Middleware('permission:menu.delete', only: ['destroy']),
        ];
    }

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

    public function store(StoreMenuRequest $request)
    {
        $this->menuService->createMenu($request->validated());

        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $parents = $this->menuService->getParentMenus($menu->id);
        return view('admin.menus.edit', compact('menu', 'parents'));
    }

    public function update(StoreMenuRequest $request, Menu $menu)
    {
        $this->menuService->updateMenu($menu, $request->validated());

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $this->menuService->deleteMenu($menu);
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }
}
