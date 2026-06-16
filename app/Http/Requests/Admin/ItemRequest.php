<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $itemId = $this->route('item')?->id;

        return [
            'model' => [
                'required', 'string', 'max:255',
                Rule::unique('items', 'model')->ignore($itemId)->whereNull('deleted_at'),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
            'remove_image' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:5000'],
            'unit' => ['required', 'integer', 'min:1', 'max:99'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'model.unique' => __('cms.duplicate', ['attribute' => __('cms.model')]),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => (bool) $this->input('status', false),
        ]);
    }
}
