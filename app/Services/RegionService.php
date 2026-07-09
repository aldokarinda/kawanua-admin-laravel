<?php

namespace App\Services;

use App\Models\Region;

class RegionService
{
    /**
     * Get paginated regions.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedRegions(int $perPage = 10)
    {
        return Region::with('parent')->latest()->paginate($perPage);
    }

    /**
     * Create a new region.
     *
     * @param array $data
     * @return \App\Models\Region
     */
    public function createRegion(array $data)
    {
        return Region::create($data);
    }

    /**
     * Update a region.
     *
     * @param \App\Models\Region $region
     * @param array $data
     * @return bool
     */
    public function updateRegion(Region $region, array $data)
    {
        return $region->update($data);
    }

    /**
     * Delete a region.
     *
     * @param \App\Models\Region $region
     * @return bool|null
     */
    public function deleteRegion(Region $region)
    {
        return $region->delete();
    }
}
