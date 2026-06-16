<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ImportServiceInterface
{
    /**
     * Stream the blank learning-unit import template (UTF-8 + BOM).
     */
    public function learningUnitTemplate(): StreamedResponse;

    /**
     * Import a single learning unit (with its items & recommend items) from a
     * multi-section CSV file. Always creates a new unit — a duplicate code is
     * rejected (status 'duplicate_code') and never overwrites the existing unit.
     * The new unit starts from the default item/recommend composition; CSV rows
     * are layered on top as additions, deduped against that default.
     *
     * @return array{status: string, unit_code: ?string, items: int, recommends: int, message: ?string}
     *         status is 'success', 'duplicate_code' or 'validation'
     */
    public function importLearningUnit(UploadedFile $file): array;
}
