<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\ExportServiceInterface;
use App\Contracts\ImportServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\LearningUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        protected ExportServiceInterface $exportService,
        protected ImportServiceInterface $importService,
    ) {}

    public function learningUnits(): StreamedResponse
    {
        return $this->exportService->exportLearningUnits([]);
    }

    public function learningUnit(LearningUnit $learningUnit): StreamedResponse
    {
        return $this->exportService->exportLearningUnit($learningUnit);
    }

    public function learningUnitTemplate(): StreamedResponse
    {
        return $this->importService->learningUnitTemplate();
    }

    public function importLearningUnit(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $result = $this->importService->importLearningUnit($request->file('file'));

        $redirect = redirect()->route('admin.learning-units.index');

        // Exactly one toast: green on success, red on a duplicate code or a
        // missing/invalid required field.
        if ($result['status'] === 'success') {
            return $redirect->with('success', __('imports.success_created', [
                'code' => $result['unit_code'],
                'items' => $result['items'],
                'recommends' => $result['recommends'],
            ]));
        }

        return $redirect->with('error', $result['message']);
    }

    public function users(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|in:0,1',
            'role'   => 'nullable|in:0,1',
        ]);

        return $this->exportService->exportUsers($validated);
    }
}
