<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Builds the JSON payload the single-page course player (client show page
 * and admin preview page) reads to render every lesson's video config and
 * every quiz's questions up front, so switching between items is a pure
 * client-side swap. Shared between the two contexts because the shape is
 * identical — only the progress/answer URLs and whether any prior answers
 * exist differ, both passed in by the caller.
 */
class CoursePlayerPayloadBuilder
{
    /**
     * @param  Collection  $modules  Course::curriculumFor()/curriculumPreview() result — each module's items_for_client already annotated.
     * @param  \Closure  $progressUrlFor  fn(CourseLesson $lesson): ?string
     * @param  \Closure  $answerUrlFor  fn(CourseQuizCheckpoint $checkpoint): ?string
     * @param  Collection  $answersByQuestionId  keyed by question id => ClientQuizAnswer; empty for a preview with no prior answers.
     * @return array{0: array, 1: array} [$itemsByKey, $orderedKeys]
     */
    public function build(Collection $modules, \Closure $progressUrlFor, \Closure $answerUrlFor, Collection $answersByQuestionId): array
    {
        $items = [];
        $order = [];

        foreach ($modules as $module) {
            foreach ($module->items_for_client as $item) {
                if ($item->item_type === 'lesson') {
                    $key = "lesson-{$item->id}";
                    $items[$key] = $this->lessonPayload($item, $key, $progressUrlFor, $answerUrlFor, $answersByQuestionId);
                } else {
                    $key = "quiz-{$item->id}";
                    $items[$key] = $this->quizPayload($item, $key, $answerUrlFor, $answersByQuestionId);
                }

                $order[] = $key;
            }
        }

        return [$items, $order];
    }

    private function lessonPayload($lesson, string $key, \Closure $progressUrlFor, \Closure $answerUrlFor, Collection $answersByQuestionId): array
    {
        return [
            'key' => $key,
            'type' => 'lesson',
            'title' => $lesson->title,
            'description' => $lesson->description,
            'duration' => $lesson->duration,
            'videoSource' => $lesson->video_source,
            'videoSrc' => $lesson->video_source === 'upload' ? $lesson->fileUrl() : null,
            'youtubeVideoId' => $lesson->youtubeVideoId(),
            'progressUrl' => $progressUrlFor($lesson),
            'startPosition' => $lesson->client_last_position,
            'completed' => $lesson->client_completed,
            'checkpoints' => $lesson->checkpoints->map(fn ($checkpoint) => [
                'id' => $checkpoint->id,
                'timestampSeconds' => $checkpoint->timestamp_seconds,
                'title' => $checkpoint->title,
                'answerUrl' => $answerUrlFor($checkpoint),
                'questions' => $this->questionsPayload($checkpoint->questions, $answersByQuestionId),
            ])->values(),
        ];
    }

    private function quizPayload($quiz, string $key, \Closure $answerUrlFor, Collection $answersByQuestionId): array
    {
        return [
            'key' => $key,
            'type' => 'module_quiz',
            'title' => $quiz->title,
            'isRequired' => $quiz->is_required,
            'completed' => $quiz->client_completed,
            'answerUrl' => $answerUrlFor($quiz),
            'questions' => $this->questionsPayload($quiz->questions, $answersByQuestionId),
        ];
    }

    /**
     * For an already-answered question, the full prior result (what was
     * selected/written, whether it was correct, and — if auto-graded and
     * wrong — which options were actually correct) is included so the
     * player can restore the real result on revisit instead of a vague
     * placeholder or a blank form.
     */
    private function questionsPayload($questions, Collection $answersByQuestionId)
    {
        return $questions->map(function ($question) use ($answersByQuestionId) {
            $answer = $answersByQuestionId->get($question->id);

            $payload = [
                'id' => $question->id,
                'type' => $question->type,
                'text' => $question->question_text,
                'points' => $question->points,
                'answered' => (bool) $answer,
                'isCorrect' => $answer?->is_correct,
                'pointsAwarded' => $answer?->points_awarded,
                'yourSelectedIds' => $answer?->selected_option_ids ?? [],
                'yourText' => $answer?->answer_text ?? '',
                'options' => $question->type !== 'text'
                    ? $question->options->map(fn ($option) => ['id' => $option->id, 'text' => $option->option_text])->values()
                    : [],
                'correctOptionIds' => [],
            ];

            if ($answer && $question->type !== 'text' && ! $answer->is_correct) {
                $payload['correctOptionIds'] = $question->options->where('is_correct', true)->pluck('id')->values();
            }

            return $payload;
        })->values();
    }
}
