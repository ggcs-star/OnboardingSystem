<?php

use App\Http\Controllers\Client\CourseController;
use App\Http\Controllers\Client\CourseLessonProgressController;
use App\Http\Controllers\Client\CourseQuizAnswerController;
use App\Http\Controllers\Client\CustomizationRequestController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentController;
use App\Http\Controllers\Client\DocumentValueController;
use App\Http\Controllers\Client\LmsController;
use App\Http\Controllers\Client\OnboardingController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\ProjectPolicyController;
use App\Http\Controllers\Client\SupportTicketController;
use App\Http\Controllers\Client\TrainingController;
use App\Http\Controllers\Client\TrainingProgressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ClientProductInquiryController;

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/{product}', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/onboarding/{project}/documents', [OnboardingController::class, 'documents'])->name('onboarding.documents');
    Route::get('/onboarding/{project}/subscription', [OnboardingController::class, 'subscription'])->name('onboarding.subscription');
    Route::post('/onboarding/{project}/subscription/plan', [OnboardingController::class, 'selectPlan'])->name('onboarding.subscription.plan');
    Route::post('/onboarding/{project}/subscription/payment', [OnboardingController::class, 'recordPayment'])->name('onboarding.subscription.payment');
    Route::post('/onboarding/{project}/subscription/sales-employee', [OnboardingController::class, 'selectSalesperson'])->name('onboarding.subscription.sales-employee');

    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/course-lessons/{courseLesson}/progress', [CourseLessonProgressController::class, 'update'])->name('course-lessons.progress.update');
    Route::post('/course-quiz-checkpoints/{checkpoint}/answers', [CourseQuizAnswerController::class, 'store'])->name('course-quiz-answers.store');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    Route::get('/lms', [LmsController::class, 'index'])->name('lms.index');
    Route::get('/lms/{lmsProduct}', [LmsController::class, 'product'])->name('lms.product');
    Route::get('/lms/{lmsProduct}/articles/{lmsArticle}', [LmsController::class, 'article'])->name('lms.article');

    Route::get('/customization-requests', [CustomizationRequestController::class, 'index'])->name('customization-requests.index');
    Route::post('/customization-requests', [CustomizationRequestController::class, 'storeAny'])->name('customization-requests.store');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/documents', [ProjectController::class, 'documents'])->name('projects.documents');
    Route::patch('/projects/{project}/hold', [ProjectController::class, 'toggleHold'])->name('projects.hold');
    Route::patch('/projects/{project}/contact', [ProjectController::class, 'updateContact'])->name('projects.contact.update');

    Route::patch('/projects/{project}/documents', [DocumentValueController::class, 'bulkUpdate'])
        ->name('projects.documents.bulk-update');
    Route::patch('/projects/{project}/documents/{group}/{field}', [DocumentValueController::class, 'update'])
        ->name('projects.documents.update');

    Route::patch('/training-progress/{training}', [TrainingProgressController::class, 'update'])
        ->name('training-progress.update');

    Route::post('/projects/{project}/customization-requests', [CustomizationRequestController::class, 'store'])
        ->name('projects.customization-requests.store');

    Route::get('/projects/{project}/policies', [ProjectPolicyController::class, 'index'])
        ->name('projects.policies');

    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{supportTicket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{supportTicket}/messages', [SupportTicketController::class, 'reply'])->name('support.messages.store');

    Route::post('/product-inquiry',[ClientProductInquiryController::class,'store'])->name('product.inquiry.store');
});
