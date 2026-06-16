<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\ItemServiceInterface;
use App\Contracts\LearningUnitServiceInterface;
use App\Contracts\RecommendItemServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LearningUnitRequest;
use App\Models\LearningUnit;
use App\Support\LearningUnitTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningUnitController extends Controller
{
    public function __construct(
        protected LearningUnitServiceInterface $learningUnitService,
        protected ItemServiceInterface $itemService,
        protected RecommendItemServiceInterface $recommendItemService,
    ) {}

    public function index(Request $request): View
    {
        $learningUnits = $this->learningUnitService->list($request->all());

        return view('admin.learning-units.index', compact('learningUnits'));
    }

    public function create(): View
    {
        $items = $this->itemService->getAllWithCategory();
        $recommendItems = $this->recommendItemService->getSwappableWithCategory();

        // Pre-fill the same item / recommend-item set the seeder uses, so a new
        // unit starts from the sample composition (unaffected by edits to the
        // seeded unit). The user can still adjust the selection before saving.
        $selectedItems = LearningUnitTemplate::itemAssignments();
        $selectedRecommends = LearningUnitTemplate::recommendAssignments();

        return view('admin.learning-units.create', compact(
            'items',
            'recommendItems',
            'selectedItems',
            'selectedRecommends',
        ));
    }

    public function store(LearningUnitRequest $request): RedirectResponse
    {
        $this->learningUnitService->create(
            $request->parsedPayload(),
            $request->itemIds(),
            $request->defaultItemIds(),
            $request->recommends(),
            $request->file('image'),
        );

        return redirect()->route('admin.learning-units.index')
            ->with('success', __('cms.created_successfully', ['resource' => __('cms.learning_unit')]));
    }

    public function edit(LearningUnit $learningUnit): View
    {
        $items = $this->itemService->getAllWithCategory();
        $recommendItems = $this->recommendItemService->getSwappableWithCategory();
        $selectedItems = $this->learningUnitService->getItemAssignments($learningUnit);
        $selectedRecommends = $this->learningUnitService->getRecommendAssignments($learningUnit);

        return view('admin.learning-units.edit', compact(
            'learningUnit',
            'items',
            'recommendItems',
            'selectedItems',
            'selectedRecommends',
        ));
    }

    public function update(LearningUnitRequest $request, LearningUnit $learningUnit): RedirectResponse
    {
        $this->learningUnitService->update(
            $learningUnit,
            $request->parsedPayload(),
            $request->itemIds(),
            $request->defaultItemIds(),
            $request->recommends(),
            $request->file('image'),
            $request->removeImage(),
        );

        return redirect()->route('admin.learning-units.index')
            ->with('success', __('cms.updated_successfully', ['resource' => __('cms.learning_unit')]));
    }

    public function destroy(LearningUnit $learningUnit): RedirectResponse
    {
        $this->learningUnitService->delete($learningUnit);

        return redirect()->route('admin.learning-units.index')
            ->with('success', __('cms.deleted_successfully', ['resource' => __('cms.learning_unit')]));
    }
}
