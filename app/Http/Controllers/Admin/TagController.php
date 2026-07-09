<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TagService;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TagController extends Controller implements HasMiddleware
{
    public function __construct(protected TagService $tagService)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:tag.view', only: ['index']),
            new Middleware('permission:tag.create', only: ['create', 'store']),
            new Middleware('permission:tag.edit', only: ['edit', 'update']),
            new Middleware('permission:tag.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $tags = $this->tagService->getPaginatedTags();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(StoreTagRequest $request)
    {
        $this->tagService->createTag($request->validated());
        return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $this->tagService->updateTag($tag, $request->validated());
        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $this->tagService->deleteTag($tag);
        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully.');
    }
}
