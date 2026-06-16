<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\LearningUnitServiceInterface;
use App\Models\LearningUnit;
use App\Models\LearningUnitItem;
use App\Models\LearningUnitRecommendItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LearningUnitService implements LearningUnitServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = LearningUnit::query();

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where('name', 'like', "%{$keyword}%");
        }

        return $query->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? Pagination::PER_PAGE);
    }

    public function getItemAssignments(LearningUnit $unit): Collection
    {
        return LearningUnitItem::where('learning_unit_id', $unit->id)
            ->get()
            ->keyBy('item_id');
    }

    public function getRecommendAssignments(LearningUnit $unit): Collection
    {
        return LearningUnitRecommendItem::where('learning_unit_id', $unit->id)
            ->get()
            ->keyBy('recommend_item_id');
    }

    public function create(
        array $data,
        array $itemIds,
        array $defaultItemIds,
        array $recommends,
        ?UploadedFile $image = null,
    ): LearningUnit {
        return DB::transaction(function () use ($data, $itemIds, $defaultItemIds, $recommends, $image) {
            $unit = LearningUnit::create($data);

            if ($image !== null) {
                $unit->update(['image' => $this->storeImage($image, $unit->id)]);
            }

            $this->syncItems($unit->id, $itemIds, $defaultItemIds);
            $this->syncRecommends($unit->id, $recommends);

            return $unit->fresh();
        });
    }

    public function update(
        LearningUnit $unit,
        array $data,
        array $itemIds,
        array $defaultItemIds,
        array $recommends,
        ?UploadedFile $image = null,
        bool $removeImage = false,
    ): LearningUnit {
        return DB::transaction(function () use ($unit, $data, $itemIds, $defaultItemIds, $recommends, $image, $removeImage) {
            if ($image !== null) {
                $this->deleteImage($unit->image);
                $data['image'] = $this->storeImage($image, $unit->id);
            } elseif ($removeImage) {
                $this->deleteImage($unit->image);
                $data['image'] = null;
            }

            $unit->update($data);

            LearningUnitItem::where('learning_unit_id', $unit->id)->delete();
            LearningUnitRecommendItem::where('learning_unit_id', $unit->id)->delete();

            $this->syncItems($unit->id, $itemIds, $defaultItemIds);
            $this->syncRecommends($unit->id, $recommends);

            return $unit;
        });
    }

    public function delete(LearningUnit $unit): void
    {
        $this->deleteImage($unit->image);
        $unit->delete();
    }

    private function syncItems(int $unitId, array $itemIds, array $defaultItemIds): void
    {
        $defaultIds = array_map('intval', $defaultItemIds);

        foreach ($itemIds as $itemId) {
            LearningUnitItem::create([
                'learning_unit_id' => $unitId,
                'item_id' => (int) $itemId,
                'is_default' => in_array((int) $itemId, $defaultIds, true),
            ]);
        }
    }

    private function syncRecommends(int $unitId, array $recommends): void
    {
        foreach ($recommends as $recommendId => $cfg) {
            if (empty($cfg['enabled'])) {
                continue;
            }

            $weight = $cfg['weight'] ?? null;
            $weight = ($weight !== null && $weight !== '') ? (float) $weight : null;

            LearningUnitRecommendItem::create([
                'learning_unit_id' => $unitId,
                'recommend_item_id' => (int) $recommendId,
                'column' => (int) ($cfg['column'] ?? LearningUnitRecommendItem::COLUMN_STAPLE),
                'weight' => $weight,
                'unit_text' => $cfg['unit_text'] ?? null,
            ]);
        }
    }

    private function storeImage(UploadedFile $file, int $unitId): string
    {
        // Use getPathname() (string) instead of the UploadedFile object so the
        // filesystem adapter calls fopen() on the path directly, avoiding the
        // realpath() call that returns false for PHP temp uploads on Windows.
        return Storage::putFileAs(
            "learning-units/{$unitId}",
            $file->getPathname(),
            $file->hashName(),
        );
    }

    private function deleteImage(?string $path): void
    {
        if ($path !== null && $path !== '' && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
