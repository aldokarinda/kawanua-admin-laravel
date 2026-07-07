<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->get();
        return view('admin.menus.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:menus,slug',
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string',
            'route_name' => 'nullable|string',
            'url' => 'nullable|string',
            'permission_name' => 'nullable|string',
            'order' => 'integer',
        ]);

        Menu::create($request->all());

        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return view('admin.menus.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:menus,slug,'.$menu->id,
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string',
            'route_name' => 'nullable|string',
            'url' => 'nullable|string',
            'permission_name' => 'nullable|string',
            'order' => 'integer',
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }
}
