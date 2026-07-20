<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CustomizationRequestController;
use App\Http\Controllers\Admin\CustomizationRequestOverviewController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDocumentFieldController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductPolicyController;
use App\Http\Controllers\Admin\ProductRenewalSettingController;
use App\Http\Controllers\Admin\ProductTrainingController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectDocumentValueController;
use App\Http\Controllers\Admin\ProjectTrainingOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::post('/products/{product}/document-fields', [ProductDocumentFieldController::class, 'store'])->name('products.document-fields.store');
    Route::post('/products/{product}/document-fields/reorder', [ProductDocumentFieldController::class, 'reorder'])->name('products.document-fields.reorder');
    Route::patch('/products/{product}/document-fields/{documentField}', [ProductDocumentFieldController::class, 'update'])->name('products.document-fields.update');
    Route::delete('/products/{product}/document-fields/{documentField}', [ProductDocumentFieldController::class, 'destroy'])->name('products.document-fields.destroy');

    Route::post('/products/{product}/training-videos', [ProductTrainingController::class, 'store'])->name('products.training.store');
    Route::delete('/products/{product}/training-videos/{training}', [ProductTrainingController::class, 'destroy'])->name('products.training.destroy');

    Route::post('/products/{product}/faqs', [ProductFaqController::class, 'store'])->name('products.faqs.store');
    Route::delete('/products/{product}/faqs/{faq}', [ProductFaqController::class, 'destroy'])->name('products.faqs.destroy');

    Route::put('/products/{product}/policies/{policy}', [ProductPolicyController::class, 'update'])->name('products.policies.update');

    Route::put('/products/{product}/renewal-settings', [ProductRenewalSettingController::class, 'update'])->name('products.renewal-settings.update');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::patch('/projects/{project}/stage', [ProjectController::class, 'updateStage'])->name('projects.stage');
    Route::post('/projects/{project}/toggle-blocked', [ProjectController::class, 'toggleBlocked'])->name('projects.toggle-blocked');
    Route::patch('/projects/{project}/document-values/{documentValue}', [ProjectDocumentValueController::class, 'update'])->name('projects.document-values.update');
    Route::patch('/projects/{project}/customization-requests/{customizationRequest}', [CustomizationRequestController::class, 'update'])->name('projects.customization-requests.update');

    Route::get('/documents', [DocumentReviewController::class, 'index'])->name('documents.index');

    Route::get('/training', [ProjectTrainingOverviewController::class, 'index'])->name('training.index');

    Route::get('/customization-requests', [CustomizationRequestOverviewController::class, 'index'])->name('customization-requests.index');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
});
