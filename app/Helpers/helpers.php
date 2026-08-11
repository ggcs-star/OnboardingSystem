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
            return ['classes' => 'bg-danger-light text-danger', 'label' => 'On Hold'];
        }

        if ($status === 'inactive') {
            return ['classes' => 'bg-danger-light text-danger', 'label' => 'Inactive'];
        }

        return match ($stage) {
            'development' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Development'],
            'testing' => ['classes' => 'bg-warning-light text-warning', 'label' => 'Testing'],
            'web_live' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Web Live'],
            'application_live' => ['classes' => 'bg-success-light text-success', 'label' => 'Application Live'],
            default => ['classes' => 'bg-warning-light text-warning', 'label' => 'Documents'],
        };
    }
}

if (! function_exists('project_status_badge')) {
    /**
     * Tailwind classes + label for a project's plain Active/On Hold/Inactive
     * status, independent of pipeline stage.
     */
    function project_status_badge(string $status): array
    {
        return match ($status) {
            'blocked' => ['classes' => 'bg-warning-light text-warning', 'label' => 'On Hold'],
            'inactive' => ['classes' => 'bg-danger-light text-danger', 'label' => 'Inactive'],
            default => ['classes' => 'bg-success-light text-success', 'label' => 'Active'],
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

if (! function_exists('support_ticket_status_badge')) {
    /**
     * Tailwind classes + label for a support ticket's status.
     */
    function support_ticket_status_badge(string $status): array
    {
        return match ($status) {
            'in_progress' => ['classes' => 'bg-primary-light text-primary', 'label' => 'In Progress'],
            'resolved' => ['classes' => 'bg-success-light text-success', 'label' => 'Resolved'],
            'closed' => ['classes' => 'bg-secondary-light text-secondary-dark', 'label' => 'Closed'],
            default => ['classes' => 'bg-warning-light text-warning', 'label' => 'Open'],
        };
    }
}

if (! function_exists('support_ticket_category_badge')) {
    /**
     * Tailwind classes + label for a support ticket's category.
     */
    function support_ticket_category_badge(string $category): array
    {
        return match ($category) {
            'training' => ['classes' => 'bg-chart-3/15 text-chart-3', 'label' => 'Training Related'],
            'complaint' => ['classes' => 'bg-danger-light text-danger', 'label' => 'Complaint'],
            default => ['classes' => 'bg-chart-5/15 text-chart-5', 'label' => 'Need Help'],
        };
    }
}

if (! function_exists('customization_status_badge')) {
    /**
     * Tailwind classes + label for a customization request's review status.
     */
    function customization_status_badge(string $status): array
    {
        return match ($status) {
            'approved' => ['classes' => 'bg-success-light text-success', 'label' => 'Approved'],
            'rejected' => ['classes' => 'bg-danger-light text-danger', 'label' => 'Rejected'],
            'partial' => ['classes' => 'bg-primary-light text-primary', 'label' => 'Partially Approved'],
            default => ['classes' => 'bg-warning-light text-warning', 'label' => 'Pending Review'],
        };
    }
}
