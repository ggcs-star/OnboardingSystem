<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;

class ProjectDocumentService
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    /**
     * Seeds a freshly-created project's `documents` JSON from its product's
     * current groups/fields, everything starting out pending.
     */
    public function initialize(Project $project): void
    {
        $project->load('product.documentGroups.fields');

        $documents = [];

        foreach ($project->product->documentGroups as $group) {
            $fields = [];

            foreach ($group->fields as $field) {
                $fields[$field->field_key] = [
                    'label' => $field->label,
                    'type' => $field->field_type,
                    'required' => $field->required,
                    'value' => null,
                    'file' => null,
                    'status' => 'pending',
                    'remarks' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'submitted_at' => null,
                ];
            }

            $documents[$group->slug] = [
                'label' => $group->name,
                'mandatory' => $group->is_mandatory,
                'fields' => $fields,
            ];
        }

        $project->update(['documents' => $documents]);
    }

    /**
     * Client-side submission of a value or file for one field.
     */
    public function submit(Project $project, string $group, string $field, ?string $value, ?UploadedFile $file): void
    {
        $documents = $project->documents ?? [];

        abort_unless(isset($documents[$group]['fields'][$field]), 404);

        $entry = $documents[$group]['fields'][$field];

        if ($file) {
            $this->fileUploadService->delete($entry['file'] ?? null);
            $entry['file'] = $this->fileUploadService->store($file, 'project-documents');
            $entry['value'] = null;
        } else {
            $entry['value'] = $value;
        }

        $entry['status'] = 'submitted';
        $entry['submitted_at'] = now()->toIso8601String();
        $entry['remarks'] = null;

        $documents[$group]['fields'][$field] = $entry;

        $project->update(['documents' => $documents]);
    }

    /**
     * Admin approve/reject of a submitted field.
     */
    public function review(Project $project, string $group, string $field, string $status, ?string $remarks, int $approvedBy): void
    {
        $documents = $project->documents ?? [];

        abort_unless(isset($documents[$group]['fields'][$field]), 404);

        $entry = $documents[$group]['fields'][$field];
        $entry['status'] = $status;
        $entry['remarks'] = $remarks;
        $entry['approved_by'] = $approvedBy;
        $entry['approved_at'] = now()->toIso8601String();

        $documents[$group]['fields'][$field] = $entry;

        $project->update(['documents' => $documents]);
    }
}
