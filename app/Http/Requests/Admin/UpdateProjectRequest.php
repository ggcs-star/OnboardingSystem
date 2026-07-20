<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'expected_live_date' => ['nullable', 'date'],
            'actual_live_date' => ['nullable', 'date'],
        ];
    }
}
