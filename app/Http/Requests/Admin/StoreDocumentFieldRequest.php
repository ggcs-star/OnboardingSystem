<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'field_type' => ['required', Rule::in(['text', 'number', 'image', 'pdf', 'key'])],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'required' => ['nullable', 'boolean'],
        ];
    }
}
