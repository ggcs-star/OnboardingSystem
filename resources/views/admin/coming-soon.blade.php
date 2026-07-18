<x-admin-layout :title="$label . ' — ' . config('app.name')">
    <div class="flex min-h-[60vh] flex-col items-center justify-center rounded-xl border border-dashed border-app-border bg-white text-center">
        <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-primary-light text-primary">
            <x-icon name="folder" class="w-7 h-7" />
        </span>
        <h1 class="text-lg font-semibold text-secondary-dark">{{ $label }}</h1>
        <p class="mt-1 text-sm text-secondary">This section is coming soon.</p>
    </div>
</x-admin-layout>
