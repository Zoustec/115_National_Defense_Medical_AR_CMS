<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LearningUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface LearningUnitServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function getItemAssignments(LearningUnit $unit): Collection;

    public function getRecommendAssignments(LearningUnit $unit): Collection;

    public function create(
        array $data,
        array $itemIds,
        array $defaultItemIds,
        array $recommends,
        ?UploadedFile $image = null,
    ): LearningUnit;

    public function update(
        LearningUnit $unit,
        array $data,
        array $itemIds,
        array $defaultItemIds,
        array $recommends,
        ?UploadedFile $image = null,
        bool $removeImage = false,
    ): LearningUnit;

    public function delete(LearningUnit $unit): void;
}
