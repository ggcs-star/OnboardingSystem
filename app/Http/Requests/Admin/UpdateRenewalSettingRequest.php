<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRenewalSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reminder_before_days' => ['nullable', 'integer', 'min:0'],
            'auto_renew_reminder' => ['nullable', 'boolean'],
        ];
    }
}
