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
            'default_plan_duration_months' => ['nullable', 'integer', 'min:1'],
            'default_renewal_amount' => ['nullable', 'numeric', 'min:0'],
            'reminder_before_days' => ['nullable', 'integer', 'min:0'],
            'auto_renew_reminder' => ['nullable', 'boolean'],
        ];
    }
}
