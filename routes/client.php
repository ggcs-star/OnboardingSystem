<?php

use App\Http\Controllers\Client\CustomizationRequestController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\DocumentValueController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Client\TrainingController;
use App\Http\Controllers\Client\TrainingProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');

    Route::get('/customization-requests', [CustomizationRequestController::class, 'index'])->name('customization-requests.index');
    Route::post('/customization-requests', [CustomizationRequestController::class, 'storeAny'])->name('customization-requests.store');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::patch('/projects/{project}/document-values/{documentValue}', [DocumentValueController::class, 'update'])
        ->name('projects.document-values.update');

    Route::patch('/training-progress/{training}', [TrainingProgressController::class, 'update'])
        ->name('training-progress.update');

    Route::post('/projects/{project}/customization-requests', [CustomizationRequestController::class, 'store'])
        ->name('projects.customization-requests.store');
});
