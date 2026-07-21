<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CustomizationRequestController;
use App\Http\Controllers\Admin\CustomizationRequestOverviewController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDocumentFieldController;
use App\Http\Controllers\Admin\ProductDocumentGroupController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductPolicyController;
use App\Http\Controllers\Admin\ProductRenewalSettingController;
use App\Http\Controllers\Admin\ProductSubscriptionPlanController;
use App\Http\Controllers\Admin\ProductTrainingController;
use App\Http\Controllers\Admin\ProjectDocumentController;
use App\Http\Controllers\Admin\ProjectTrainingOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::post('/products/{product}/document-groups', [ProductDocumentGroupController::class, 'store'])->name('products.document-groups.store');
    Route::post('/products/{product}/document-groups/reorder', [ProductDocumentGroupController::class, 'reorder'])->name('products.document-groups.reorder');
    Route::delete('/products/{product}/document-groups/{documentGroup}', [ProductDocumentGroupController::class, 'destroy'])->name('products.document-groups.destroy');

    Route::post('/products/{product}/document-fields', [ProductDocumentFieldController::class, 'store'])->name('products.document-fields.store');
    Route::post('/products/{product}/document-fields/reorder', [ProductDocumentFieldController::class, 'reorder'])->name('products.document-fields.reorder');
    Route::patch('/products/{product}/document-fields/{documentField}', [ProductDocumentFieldController::class, 'update'])->name('products.document-fields.update');
    Route::delete('/products/{product}/document-fields/{documentField}', [ProductDocumentFieldController::class, 'destroy'])->name('products.document-fields.destroy');

    Route::post('/products/{product}/faqs', [ProductFaqController::class, 'store'])->name('products.faqs.store');
    Route::delete('/products/{product}/faqs/{faq}', [ProductFaqController::class, 'destroy'])->name('products.faqs.destroy');

    Route::put('/products/{product}/policies/{policy}', [ProductPolicyController::class, 'update'])->name('products.policies.update');

    Route::put('/products/{product}/renewal-settings', [ProductRenewalSettingController::class, 'update'])->name('products.renewal-settings.update');

    Route::post('/products/{product}/subscription-plans', [ProductSubscriptionPlanController::class, 'store'])->name('products.subscription-plans.store');
    Route::put('/products/{product}/subscription-plans/{subscriptionPlan}', [ProductSubscriptionPlanController::class, 'update'])->name('products.subscription-plans.update');
    Route::delete('/products/{product}/subscription-plans/{subscriptionPlan}', [ProductSubscriptionPlanController::class, 'destroy'])->name('products.subscription-plans.destroy');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    Route::patch('/projects/{project}/documents/{group}/{field}', [ProjectDocumentController::class, 'update'])->name('projects.documents.update');
    Route::patch('/projects/{project}/customization-requests/{customizationRequest}', [CustomizationRequestController::class, 'update'])->name('projects.customization-requests.update');

    Route::get('/documents', [DocumentReviewController::class, 'index'])->name('documents.index');

    Route::get('/training', [ProjectTrainingOverviewController::class, 'index'])->name('training.index');
    Route::post('/training-videos', [ProductTrainingController::class, 'store'])->name('training-videos.store');
    Route::delete('/training-videos/{training}', [ProductTrainingController::class, 'destroy'])->name('training-videos.destroy');

    Route::get('/customization-requests', [CustomizationRequestOverviewController::class, 'index'])->name('customization-requests.index');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
});
