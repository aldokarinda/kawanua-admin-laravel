<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get paginated categories.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedCategories(int $perPage = 10)
    {
        return Category::latest()->paginate($perPage);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return \App\Models\Category
     */
    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    /**
     * Update a category.
     *
     * @param \App\Models\Category $category
     * @param array $data
     * @return bool
     */
    public function updateCategory(Category $category, array $data)
    {
        return $category->update($data);
    }

    /**
     * Delete a category.
     *
     * @param \App\Models\Category $category
     * @return bool|null
     */
    public function deleteCategory(Category $category)
    {
        return $category->delete();
    }
}
