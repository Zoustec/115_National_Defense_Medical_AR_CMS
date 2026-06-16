<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\CategoryServiceInterface;
use App\Contracts\RecommendItemServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecommendItemRequest;
use App\Models\RecommendItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecommendItemController extends Controller
{
    public function __construct(
        protected RecommendItemServiceInterface $recommendItemService,
        protected CategoryServiceInterface $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $recommendItems = $this->recommendItemService->list($request->all());
        $categories = $this->categoryService->getSwappable();

        return view('admin.recommend-items.index', compact('recommendItems', 'categories'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getSwappable();

        return view('admin.recommend-items.create', compact('categories'));
    }

    public function store(RecommendItemRequest $request): RedirectResponse
    {
        $this->recommendItemService->create($request->validated());

        return redirect()->route('admin.recommend-items.index')
            ->with('success', __('cms.created_successfully', ['resource' => __('cms.recommend_item')]));
    }

    public function edit(RecommendItem $recommendItem): View
    {
        $categories = $this->categoryService->getSwappable();

        return view('admin.recommend-items.edit', compact('recommendItem', 'categories'));
    }

    public function update(RecommendItemRequest $request, RecommendItem $recommendItem): RedirectResponse
    {
        $this->recommendItemService->update($recommendItem, $request->validated());

        return redirect()->route('admin.recommend-items.index')
            ->with('success', __('cms.updated_successfully', ['resource' => __('cms.recommend_item')]));
    }

    public function destroy(RecommendItem $recommendItem): RedirectResponse
    {
        $this->recommendItemService->delete($recommendItem);

        return redirect()->route('admin.recommend-items.index')
            ->with('success', __('cms.deleted_successfully', ['resource' => __('cms.recommend_item')]));
    }
}
