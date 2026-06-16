<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $adminId = $this->route('admin')?->id;
        $passwordRequired = $this->isMethod('POST') ? 'required' : 'nullable';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($adminId)],
            'password' => [$passwordRequired, 'string', 'min:8'],
        ];
    }
}
