<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    /**
     * Get paginated tags.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedTags(int $perPage = 10)
    {
        return Tag::latest()->paginate($perPage);
    }

    /**
     * Create a new tag.
     *
     * @param array $data
     * @return \App\Models\Tag
     */
    public function createTag(array $data)
    {
        return Tag::create($data);
    }

    /**
     * Update a tag.
     *
     * @param \App\Models\Tag $tag
     * @param array $data
     * @return bool
     */
    public function updateTag(Tag $tag, array $data)
    {
        return $tag->update($data);
    }

    /**
     * Delete a tag.
     *
     * @param \App\Models\Tag $tag
     * @return bool|null
     */
    public function deleteTag(Tag $tag)
    {
        return $tag->delete();
    }
}
