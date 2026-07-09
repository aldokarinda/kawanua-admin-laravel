<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Services\RegionService;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RegionController extends Controller implements HasMiddleware
{
    public function __construct(protected RegionService $regionService)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:region.view', only: ['index']),
            new Middleware('permission:region.create', only: ['create', 'store']),
            new Middleware('permission:region.edit', only: ['edit', 'update']),
            new Middleware('permission:region.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $regions = $this->regionService->getPaginatedRegions();
        return view('admin.regions.index', compact('regions'));
    }

    public function create()
    {
        $parents = Region::where('type', 'province')->get();
        return view('admin.regions.create', compact('parents'));
    }

    public function store(StoreRegionRequest $request)
    {
        $this->regionService->createRegion($request->validated());
        return redirect()->route('admin.regions.index')->with('success', 'Region created successfully.');
    }

    public function edit(Region $region)
    {
        $parents = Region::where('type', 'province')->where('id', '!=', $region->id)->get();
        return view('admin.regions.edit', compact('region', 'parents'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $this->regionService->updateRegion($region, $request->validated());
        return redirect()->route('admin.regions.index')->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        $this->regionService->deleteRegion($region);
        return redirect()->route('admin.regions.index')->with('success', 'Region deleted successfully.');
    }
}
