<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseQuizCheckpoint;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo content for the Courses module — three short courses (Laravel, Java,
 * AI & Machine Learning) attached to existing products, each with 2 modules
 * of 2 lessons, mixing YouTube and "uploaded" video lessons and every quiz
 * question type (radio, checkbox, text) — both as mid-video checkpoints and
 * as standalone module quizzes (one per course, positioned after a lesson,
 * one of them marked optional to demo that toggle). Safe to re-run: matched
 * by slug/title and left untouched if already present.
 *
 * Note: "uploaded" lessons are seeded with video_source = 'upload' and no
 * physical video file, since seeders can't fabricate a real playable video
 * without an encoder (none is available in this environment). Open the
 * lesson in the admin editor and upload any short .mp4 to complete that
 * part of the demo — everything else (modules, checkpoints, question
 * types, scoring) already works identically either way.
 */
class CourseDemoSeeder extends Seeder
{
    private const DEMO_YOUTUBE_URL = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

    public function run(): void
    {
        $products = Product::orderBy('id')->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No products found — skipping Courses demo content.');

            return;
        }

        $courses = [
            $this->seedLaravelCourse($products[0 % $products->count()]),
            $this->seedJavaCourse($products[1 % $products->count()]),
            $this->seedAiCourse($products[2 % $products->count()]),
        ];

        $this->assignDemoClients(array_map(fn (Course $course) => $course->product, $courses));
    }

    private function seedLaravelCourse(Product $product): Course
    {
        $course = $this->course(
            $product,
            'Laravel for Beginners',
            'Learn to build modern web applications with the Laravel PHP framework — from routing to Eloquent ORM.',
            [37, 99, 235]
        );

        $fundamentals = $this->module($course, 'Laravel Fundamentals', 'Core concepts every Laravel developer needs to know.');

        $intro = $this->lesson($fundamentals, 'Introduction to Laravel', 'youtube', self::DEMO_YOUTUBE_URL, '6:12', 'What Laravel is and why it\'s a popular PHP framework.');
        $this->radioQuestion(
            $this->checkpoint($intro, 45, 'Quick Check'),
            'Laravel is written in which programming language?',
            ['PHP', 'Python', 'Ruby', 'JavaScript'],
            0
        );

        $routing = $this->lesson($fundamentals, 'Routing & Controllers', 'upload', null, '8:45', 'Defining routes and handling requests with controllers.');
        $this->checkboxQuestion(
            $this->checkpoint($routing, 60, 'Quick Check'),
            'Which of these are valid Laravel route methods?',
            ['Route::get()', 'Route::post()', 'Route::fetch()', 'Route::delete()'],
            [0, 1, 3]
        );

        $eloquent = $this->module($course, 'Eloquent ORM', 'Working with your database through Laravel\'s ORM.');

        $models = $this->lesson($eloquent, 'Models & Migrations', 'youtube', self::DEMO_YOUTUBE_URL, '9:30', 'Defining Eloquent models and versioning your schema with migrations.');
        $this->textQuestion(
            $this->checkpoint($models, 50, 'Quick Check'),
            'In one sentence, what is the purpose of a migration in Laravel?'
        );

        $relationships = $this->lesson($eloquent, 'Relationships in Eloquent', 'youtube', self::DEMO_YOUTUBE_URL, '11:20', 'hasOne, hasMany, belongsTo and belongsToMany explained with examples.');
        $this->radioQuestion(
            $this->checkpoint($relationships, 40, 'Quick Check'),
            'Which relationship method is used when a Post belongs to one User?',
            ['belongsTo', 'hasMany', 'belongsToMany', 'hasOne'],
            0
        );
        $this->checkboxQuestion(
            $this->checkpoint($relationships, 300, 'Wrap Up'),
            'Which of these are real Eloquent relationship types?',
            ['hasMany', 'belongsToMany', 'hasManyThrough', 'connectsTo'],
            [0, 1, 2]
        );

        // Standalone module quiz — sits in the curriculum after "Introduction
        // to Laravel", not tied to a video timestamp. Required to advance.
        $quiz1 = $this->moduleQuiz($fundamentals, 'Quiz 1', $intro, true);
        $this->radioQuestion(
            $quiz1,
            'Which command creates a new Laravel project via Composer?',
            ['composer create-project laravel/laravel example-app', 'npm install laravel', 'php artisan new example-app', 'laravel init example-app'],
            0
        );
        $this->checkboxQuestion(
            $quiz1,
            'Which of these are valid Blade directives?',
            ['@if', '@foreach', '@print', '@csrf'],
            [0, 1, 3]
        );
        $this->textQuestion($quiz1, 'In one sentence, what is Blade in Laravel?');

        return $course;
    }

    private function seedJavaCourse(Product $product): Course
    {
        $course = $this->course(
            $product,
            'Java Programming Fundamentals',
            'A beginner-friendly path through Java syntax, control flow and object-oriented programming.',
            [220, 38, 38]
        );

        $basics = $this->module($course, 'Java Basics', 'Variables, data types and controlling program flow.');

        $variables = $this->lesson($basics, 'Variables & Data Types', 'youtube', self::DEMO_YOUTUBE_URL, '7:05', 'Primitive types, variables and casting in Java.');
        $this->radioQuestion(
            $this->checkpoint($variables, 35, 'Quick Check'),
            'Which of these is a primitive data type in Java?',
            ['int', 'String', 'ArrayList', 'Object'],
            0
        );

        $loops = $this->lesson($basics, 'Control Flow & Loops', 'upload', null, '8:15', 'if/else, switch, for, while and do-while in Java.');
        $this->checkboxQuestion(
            $this->checkpoint($loops, 55, 'Quick Check'),
            'Which of these are valid Java loop constructs?',
            ['for', 'while', 'loop', 'do-while'],
            [0, 1, 3]
        );

        $oop = $this->module($course, 'Object-Oriented Java', 'Classes, objects, inheritance and interfaces.');

        $classes = $this->lesson($oop, 'Classes & Objects', 'youtube', self::DEMO_YOUTUBE_URL, '10:00', 'Defining classes, constructors, and creating objects.');
        $this->textQuestion(
            $this->checkpoint($classes, 60, 'Quick Check'),
            'In one sentence, what is the difference between a class and an object?'
        );

        $inheritance = $this->lesson($oop, 'Inheritance & Interfaces', 'youtube', self::DEMO_YOUTUBE_URL, '12:40', 'Extending classes, implementing interfaces, and polymorphism.');
        $this->radioQuestion(
            $this->checkpoint($inheritance, 42, 'Quick Check'),
            'Which keyword is used for a Java class to inherit from another class?',
            ['extends', 'implements', 'inherits', 'super'],
            0
        );
        $this->checkboxQuestion(
            $this->checkpoint($inheritance, 320, 'Wrap Up'),
            'Which of these are true about Java interfaces?',
            ['A class can implement multiple interfaces', 'Interfaces can declare method signatures', 'A class can only extend one class', 'Interfaces support multiple inheritance of type'],
            [0, 1, 2, 3]
        );

        // Standalone module quiz after "Variables & Data Types". Required to advance.
        $quiz1 = $this->moduleQuiz($basics, 'Quiz 1', $variables, true);
        $this->radioQuestion(
            $quiz1,
            'Which keyword is used to declare a constant in Java?',
            ['final', 'const', 'static', 'readonly'],
            0
        );
        $this->checkboxQuestion(
            $quiz1,
            'Which of these are Java access modifiers?',
            ['public', 'private', 'protected', 'external'],
            [0, 1, 2]
        );
        $this->textQuestion($quiz1, "In one sentence, what does the 'static' keyword mean in Java?");

        return $course;
    }

    private function seedAiCourse(Product $product): Course
    {
        $course = $this->course(
            $product,
            'AI & Machine Learning Essentials',
            'A practical introduction to artificial intelligence, machine learning types, and responsible AI.',
            [16, 185, 129]
        );

        $foundations = $this->module($course, 'AI Foundations', 'What AI is, and how machine learning fits in.');

        $whatIsAi = $this->lesson($foundations, 'What is Artificial Intelligence?', 'youtube', self::DEMO_YOUTUBE_URL, '6:30', 'Defining AI and how it differs from traditional software.');
        $this->radioQuestion(
            $this->checkpoint($whatIsAi, 40, 'Quick Check'),
            'Which best describes Artificial Intelligence?',
            ['Systems that perform tasks requiring human-like intelligence', 'Any program with a graphical interface', 'A database query language', 'A type of computer hardware'],
            0
        );

        $mlTypes = $this->lesson($foundations, 'Types of Machine Learning', 'upload', null, '9:10', 'Supervised, unsupervised and reinforcement learning explained.');
        $this->checkboxQuestion(
            $this->checkpoint($mlTypes, 65, 'Quick Check'),
            'Which of these are recognized types of machine learning?',
            ['Supervised learning', 'Unsupervised learning', 'Reinforcement learning', 'Manual learning'],
            [0, 1, 2]
        );

        $practical = $this->module($course, 'Practical AI', 'Neural networks and responsible AI in practice.');

        $neuralNets = $this->lesson($practical, 'Introduction to Neural Networks', 'youtube', self::DEMO_YOUTUBE_URL, '11:45', 'Neurons, layers, weights and how a network learns.');
        $this->textQuestion(
            $this->checkpoint($neuralNets, 50, 'Quick Check'),
            'In one sentence, what does a neural network learn to do during training?'
        );

        $ethics = $this->lesson($practical, 'AI Ethics & Bias', 'youtube', self::DEMO_YOUTUBE_URL, '8:55', 'Where bias enters AI systems and how teams can mitigate it.');
        $this->radioQuestion(
            $this->checkpoint($ethics, 38, 'Quick Check'),
            'A biased AI model most often reflects bias present in its...',
            ['Training data', 'Programming language', 'Server hardware', 'File format'],
            0
        );
        $this->checkboxQuestion(
            $this->checkpoint($ethics, 280, 'Wrap Up'),
            'Which of these help reduce bias in an AI system?',
            ['Diverse and representative training data', 'Regular fairness audits', 'Ignoring model outputs entirely', 'Human review of high-stakes decisions'],
            [0, 1, 3]
        );

        // Standalone module quiz after "What is Artificial Intelligence?" —
        // marked optional, so "Next" skips straight past it (unlike the
        // required quizzes on the other two demo courses).
        $quiz1 = $this->moduleQuiz($foundations, 'Quiz 1', $whatIsAi, false);
        $this->radioQuestion(
            $quiz1,
            'Which of these is an example of supervised learning?',
            ['Predicting house prices from labeled data', 'Clustering customers with no labels', 'Generating random numbers', 'None of the above'],
            0
        );
        $this->checkboxQuestion(
            $quiz1,
            'Which of these are common AI applications?',
            ['Image recognition', 'Spam filtering', 'Manual data entry', 'Recommendation systems'],
            [0, 1, 3]
        );
        $this->textQuestion($quiz1, 'In one sentence, what is the difference between AI and Machine Learning?');

        return $course;
    }

    private function course(Product $product, string $title, string $description, array $thumbnailRgb): Course
    {
        return Course::firstOrCreate(
            ['slug' => Str::slug($title)],
            [
                'product_id' => $product->id,
                'title' => $title,
                'description' => $description,
                'thumbnail' => $this->thumbnail($title, $thumbnailRgb),
                'is_published' => true,
            ]
        );
    }

    private function module(Course $course, string $title, ?string $description): CourseModule
    {
        return $course->modules()->firstOrCreate(
            ['title' => $title],
            ['description' => $description]
        );
    }

    private function lesson(CourseModule $module, string $title, string $videoSource, ?string $videoUrl, ?string $duration, ?string $description): CourseLesson
    {
        return $module->lessons()->firstOrCreate(
            ['title' => $title],
            [
                'description' => $description,
                'video_source' => $videoSource,
                'video_url' => $videoUrl,
                'duration' => $duration,
            ]
        );
    }

    private function checkpoint(CourseLesson $lesson, int $timestampSeconds, ?string $title): CourseQuizCheckpoint
    {
        return $lesson->checkpoints()->firstOrCreate(
            ['timestamp_seconds' => $timestampSeconds],
            ['title' => $title]
        );
    }

    /** Standalone quiz item positioned after $afterLesson in the module's curriculum (not tied to a video timestamp). */
    private function moduleQuiz(CourseModule $module, string $title, CourseLesson $afterLesson, bool $isRequired): CourseQuizCheckpoint
    {
        return $module->quizzes()->firstOrCreate(
            ['title' => $title],
            [
                'course_id' => $module->course_id,
                'after_course_lesson_id' => $afterLesson->id,
                'is_required' => $isRequired,
            ]
        );
    }

    private function radioQuestion(CourseQuizCheckpoint $checkpoint, string $text, array $options, int $correctIndex): void
    {
        $this->question($checkpoint, 'radio', $text, $options, [$correctIndex]);
    }

    private function checkboxQuestion(CourseQuizCheckpoint $checkpoint, string $text, array $options, array $correctIndexes): void
    {
        $this->question($checkpoint, 'checkbox', $text, $options, $correctIndexes);
    }

    private function textQuestion(CourseQuizCheckpoint $checkpoint, string $text): void
    {
        $checkpoint->questions()->firstOrCreate(
            ['question_text' => $text],
            ['type' => 'text']
        );
    }

    private function question(CourseQuizCheckpoint $checkpoint, string $type, string $text, array $options, array $correctIndexes): void
    {
        $question = $checkpoint->questions()->firstOrCreate(
            ['question_text' => $text],
            ['type' => $type]
        );

        if ($question->options()->exists()) {
            return;
        }

        foreach ($options as $index => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => in_array($index, $correctIndexes, true),
            ]);
        }
    }

    /** Grants every existing demo client access to the products used above, so the courses are immediately visible from any client login. */
    private function assignDemoClients(array $products): void
    {
        $clients = Client::all();

        foreach ($products as $product) {
            foreach ($clients as $client) {
                $product->clientProducts()->firstOrCreate(
                    ['client_id' => $client->id],
                    ['status' => true, 'assigned_at' => now()]
                );
            }
        }
    }

    /** Generates and stores a labelled placeholder PNG the first time it's needed, mirroring LmsDemoSeeder's convention. */
    private function thumbnail(string $label, array $rgb): string
    {
        $path = 'course-thumbnails/demo/' . Str::slug($label) . '.png';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->generatePlaceholderPng($label, $rgb));
        }

        return $path;
    }

    private function generatePlaceholderPng(string $label, array $rgb): string
    {
        $width = 640;
        $height = 360;

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, ...$rgb);
        imagefill($image, 0, 0, $background);

        $white = imagecolorallocate($image, 255, 255, 255);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($label);
        $x = max(20, intdiv($width - $textWidth, 2));
        $y = intdiv($height, 2) - 10;
        imagestring($image, $font, $x, $y, $label, $white);

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        return $data;
    }
}
