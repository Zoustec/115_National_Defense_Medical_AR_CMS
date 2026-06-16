<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\CategoryServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $categoryService) {}

    public function index(Request $request): View
    {
        $categories = $this->categoryService->list($request->all());

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', __('cms.created_successfully', ['resource' => __('cms.category')]));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', __('cms.updated_successfully', ['resource' => __('cms.category')]));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('success', __('cms.deleted_successfully', ['resource' => __('cms.category')]));
    }
}
