<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateDocumentValuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fields' => ['nullable', 'array'],
            'fields.*.*' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*.*' => ['nullable', 'file', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $project = $this->route('project');

            foreach ($project->documentEntries() as $entry) {
                if (! $entry->group_mandatory || ! $entry->required) {
                    continue;
                }

                if (in_array($entry->status, ['submitted', 'approved'], true)) {
                    continue;
                }

                $key = "fields.{$entry->group_slug}.{$entry->field_key}";
                $hasValue = filled($this->input($key));
                $hasFile = $this->hasFile("files.{$entry->group_slug}.{$entry->field_key}");

                if (! $hasValue && ! $hasFile) {
                    $validator->errors()->add(
                        $key,
                        "{$entry->label} is mandatory to complete onboarding. Please fill this in before continuing."
                    );
                }
            }
        });
    }
}
