<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\CategoryServiceInterface;
use App\Contracts\ItemServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected ItemServiceInterface $itemService,
        protected CategoryServiceInterface $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $items = $this->itemService->list($request->all());
        $categories = $this->categoryService->getAll();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getAll();

        return view('admin.items.create', compact('categories'));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image'], $data['remove_image']);

        $this->itemService->create($data, $request->file('image'));

        return redirect()->route('admin.items.index')
            ->with('success', __('cms.created_successfully', ['resource' => __('cms.item')]));
    }

    public function edit(Item $item): View
    {
        $categories = $this->categoryService->getAll();

        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $data = $request->validated();
        $removeImage = (bool) ($data['remove_image'] ?? false);
        unset($data['image'], $data['remove_image']);

        $this->itemService->update(
            $item,
            $data,
            $request->file('image'),
            $removeImage,
        );

        return redirect()->route('admin.items.index')
            ->with('success', __('cms.updated_successfully', ['resource' => __('cms.item')]));
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->itemService->delete($item);

        return redirect()->route('admin.items.index')
            ->with('success', __('cms.deleted_successfully', ['resource' => __('cms.item')]));
    }
}
