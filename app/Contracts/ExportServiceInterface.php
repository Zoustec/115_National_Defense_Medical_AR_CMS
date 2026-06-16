<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LearningUnit;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ExportServiceInterface
{
    public function exportLearningUnits(array $filters): StreamedResponse;

    public function exportLearningUnit(LearningUnit $unit): StreamedResponse;

    public function exportUsers(array $filters): StreamedResponse;
}
