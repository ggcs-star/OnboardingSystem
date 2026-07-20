<?php

if (! function_exists('document_field_type_badge')) {
    /**
     * Tailwind classes + label for a document field type pill.
     */
    function document_field_type_badge(string $type): array
    {
        return match ($type) {
            'image' => ['classes' => 'bg-success-light text-success', 'label' => 'image'],
            'pdf' => ['classes' => 'bg-danger-light text-danger', 'label' => 'pdf'],
            'key' => ['classes' => 'bg-warning-light text-warning', 'label' => 'key'],
            'number' => ['classes' => 'bg-primary-light text-primary', 'label' => 'number'],
            default => ['classes' => 'bg-secondary-light text-secondary-dark', 'label' => 'text'],
        };
    }
}

if (! function_exists('status_badge_classes')) {
    /**
     * Tailwind classes for an active/inactive status pill.
     */
    function status_badge_classes(bool $active): string
    {
        return $active
            ? 'bg-success-light text-success'
            : 'bg-secondary-light text-secondary-dark';
    }
}

if (! function_exists('project_stage_badge')) {
    /**
     * Tailwind classes + label for a project's current stage, with a
     * blocked-status override.
     */
    function project_stage_badge(string $stage, string $status = 'active'): array
    {
        if ($status === 'blocked') {
            return ['classes' => 'bg-danger-light text-danger', 'label' => 'Blocked'];
        }

        return match ($stage) {
            'development' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Development'],
            'testing' => ['classes' => 'bg-warning-light text-warning', 'label' => 'Testing'],
            'training' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Training'],
            'live' => ['classes' => 'bg-success-light text-success', 'label' => 'Go Live'],
            default => ['classes' => 'bg-warning-light text-warning', 'label' => 'Documents'],
        };
    }
}

if (! function_exists('renewal_status_badge')) {
    /**
     * Tailwind classes + label for a renewal status (active/expiring/expired/none).
     */
    function renewal_status_badge(string $status): array
    {
        return match ($status) {
            'expiring' => ['classes' => 'bg-warning-light text-warning', 'label' => 'Expiring'],
            'expired' => ['classes' => 'bg-danger-light text-danger', 'label' => 'Expired'],
            'active' => ['classes' => 'bg-success-light text-success', 'label' => 'Active'],
            default => ['classes' => 'bg-secondary-light text-secondary-dark', 'label' => '—'],
        };
    }
}

if (! function_exists('document_value_status_badge')) {
    /**
     * Tailwind classes + label for a project document value's review status.
     */
    function document_value_status_badge(string $status): array
    {
        return match ($status) {
            'submitted' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Submitted'],
            'approved' => ['classes' => 'bg-success-light text-success', 'label' => 'Approved'],
            'rejected' => ['classes' => 'bg-danger-light text-danger', 'label' => 'Rejected'],
            default => ['classes' => 'bg-warning-light text-warning', 'label' => 'Pending'],
        };
    }
}
