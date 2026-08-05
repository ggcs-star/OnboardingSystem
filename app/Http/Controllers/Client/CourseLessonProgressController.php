<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseLessonProgressController extends Controller
{
    public function update(Request $request, CourseLesson $courseLesson): JsonResponse
    {
        $client = $request->user()->client;
        abort_unless($client, 403);

        $data = $request->validate([
            'position' => ['required', 'integer', 'min:0'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $progress = $client->courseLessonProgress()->firstOrCreate(
            ['course_lesson_id' => $courseLesson->id],
            ['completed' => false]
        );

        $attributes = ['last_position_seconds' => $data['position']];

        if (($data['completed'] ?? false) && ! $progress->completed) {
            $attributes['completed'] = true;
            $attributes['completed_at'] = now();
        }

        $progress->update($attributes);

        return response()->json(['ok' => true]);
    }
}
