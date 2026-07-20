<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCustomizationRequestRequest;
use App\Models\CustomizationRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class CustomizationRequestController extends Controller
{
    public function update(UpdateCustomizationRequestRequest $request, Project $project, CustomizationRequest $customizationRequest): RedirectResponse
    {
        abort_unless($customizationRequest->project_id === $project->id, 404);

        $customizationRequest->update([
            ...$request->validated(),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Customization request updated.');
    }
}
