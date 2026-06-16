<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LearningUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $unitId = $this->route('learningUnit')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('learning_units', 'name')->ignore($unitId)->whereNull('deleted_at'),
            ],
            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('learning_units', 'code')->ignore($unitId)->whereNull('deleted_at'),
            ],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
            'remove_image' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:5000'],
            'applicable_objects' => ['nullable', 'string', 'max:1000'],
            'dietary_recommendation_title' => ['nullable', 'string', 'max:255'],
            'dietary_recommendations' => ['nullable', 'string', 'max:20000'],
            'clinical_note_title' => ['nullable', 'string', 'max:255'],
            'clinical_notes' => ['nullable', 'string', 'max:20000'],
            'status' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],

            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:items,id'],

            'default_item_ids' => ['nullable', 'array'],
            'default_item_ids.*' => ['integer', 'exists:items,id'],

            'recommends' => ['nullable', 'array'],
            'recommends.*.enabled' => ['nullable', 'boolean'],
            // Replacement group is the recommend item's own category id.
            'recommends.*.column' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'recommends.*.weight' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'recommends.*.unit_text' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('cms.name_required'),
            'code.required' => __('cms.code_required'),
            'name.unique' => __('cms.duplicate_cancelled'),
            'code.unique' => __('cms.duplicate_cancelled'),
        ];
    }

    public function parsedPayload(): array
    {
        $validated = $this->validated();

        return [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'applicable_objects' => $this->parseTags($validated['applicable_objects'] ?? null),
            'dietary_recommendation_title' => $validated['dietary_recommendation_title'] ?? null,
            'dietary_recommendations' => $validated['dietary_recommendations'] ?? null,
            'clinical_note_title' => $validated['clinical_note_title'] ?? null,
            'clinical_notes' => $validated['clinical_notes'] ?? null,
            'status' => (bool) ($validated['status'] ?? false),
            'is_locked' => (bool) ($validated['is_locked'] ?? false),
        ];
    }

    public function removeImage(): bool
    {
        return (bool) ($this->validated()['remove_image'] ?? false);
    }

    public function itemIds(): array
    {
        return (array) ($this->validated()['item_ids'] ?? []);
    }

    public function defaultItemIds(): array
    {
        return (array) ($this->validated()['default_item_ids'] ?? []);
    }

    public function recommends(): array
    {
        return (array) ($this->validated()['recommends'] ?? []);
    }

    private function parseTags(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn ($t) => trim((string) $t))
            ->filter()
            ->values()
            ->all();
    }
}
