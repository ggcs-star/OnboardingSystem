<div class="overflow-x-auto rounded-xl border border-app-border bg-white">
    <table class="min-w-full divide-y divide-app-border text-sm">
        <thead>
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                <th class="px-4 py-3">Field</th>
                <th class="px-4 py-3">Submitted Value</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-app-border">
            @forelse ($project->documentValues as $value)
                @php
                    $badge = document_value_status_badge($value->status);
                    $typeBadge = document_field_type_badge($value->documentField->field_type);
                @endphp
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-secondary-dark">{{ $value->documentField->label }}</div>
                        <x-badge :classes="$typeBadge['classes']">{{ $typeBadge['label'] }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-secondary">
                        @if ($value->file)
                            <a href="{{ asset('storage/' . $value->file) }}" target="_blank" class="text-primary hover:underline">View file</a>
                        @elseif ($value->value)
                            {{ $value->value }}
                        @else
                            <span class="text-secondary/60">Not submitted yet</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if (in_array($value->status, ['submitted', 'rejected'], true))
                            <form method="POST" action="{{ route('admin.projects.document-values.update', ['project' => $project, 'documentValue' => $value]) }}" class="inline-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="status" value="approved" class="rounded-lg bg-success-light px-3 py-1.5 text-xs font-medium text-success hover:opacity-80">Approve</button>
                                <button type="submit" name="status" value="rejected" class="rounded-lg bg-danger-light px-3 py-1.5 text-xs font-medium text-danger hover:opacity-80">Reject</button>
                            </form>
                        @else
                            <span class="text-xs text-secondary/60">Waiting on client</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-secondary">No document fields for this product.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
