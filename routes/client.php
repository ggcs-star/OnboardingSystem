<?php

use App\Http\Controllers\Client\CustomizationRequestController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentValueController;
use App\Http\Controllers\Client\OnboardingController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\ProjectPolicyController;
use App\Http\Controllers\Client\TrainingController;
use App\Http\Controllers\Client\TrainingProgressController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/customization-requests', [CustomizationRequestController::class, 'index'])->name('customization-requests.index');
    Route::post('/customization-requests', [CustomizationRequestController::class, 'storeAny'])->name('customization-requests.store');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::patch('/projects/{project}/documents', [DocumentValueController::class, 'bulkUpdate'])
        ->name('projects.documents.bulk-update');
    Route::patch('/projects/{project}/documents/{group}/{field}', [DocumentValueController::class, 'update'])
        ->name('projects.documents.update');

    Route::patch('/training-progress/{training}', [TrainingProgressController::class, 'update'])
        ->name('training-progress.update');

    Route::post('/projects/{project}/customization-requests', [CustomizationRequestController::class, 'store'])
        ->name('projects.customization-requests.store');

    Route::post('/projects/{project}/policies', [ProjectPolicyController::class, 'store'])
        ->name('projects.policies.store');
    Route::delete('/projects/{project}/policies/{policy}', [ProjectPolicyController::class, 'destroy'])
        ->name('projects.policies.destroy');
});
