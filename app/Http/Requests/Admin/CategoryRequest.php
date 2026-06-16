<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'code')->ignore($categoryId)->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('cms.name_required'),
            'code.required' => __('cms.code_required'),
            'code.unique' => __('cms.duplicate', ['attribute' => __('cms.code')]),
        ];
    }
}
