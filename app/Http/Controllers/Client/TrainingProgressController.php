<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductTraining;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrainingProgressController extends Controller
{
    public function update(Request $request, ProductTraining $training): RedirectResponse
    {
        $client = $request->user()->client;

        abort_unless($client, 403);

        $progress = $client->trainingProgress()->firstOrCreate(
            ['training_id' => $training->id],
            ['completed' => false]
        );

        $progress->update([
            'completed' => ! $progress->completed,
            'completed_at' => ! $progress->completed ? now() : null,
        ]);

        return redirect()->back();
    }
}
