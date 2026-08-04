<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CustomizationRequestController;
use App\Http\Controllers\Admin\CustomizationRequestOverviewController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\LmsArticleController;
use App\Http\Controllers\Admin\LmsCategoryController;
use App\Http\Controllers\Admin\LmsProductClientController;
use App\Http\Controllers\Admin\LmsProductController;
use App\Http\Controllers\Admin\LmsSubCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDocumentFieldController;
use App\Http\Controllers\Admin\ProductDocumentGroupController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductPolicyController;
use App\Http\Controllers\Admin\ProductRenewalSettingController;
use App\Http\Controllers\Admin\ProductTrainingController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectDocumentController;
use App\Http\Controllers\Admin\ProjectDocumentGroupController;
use App\Http\Controllers\Admin\ProjectSalesAssignmentController;
use App\Http\Controllers\Admin\ProjectTrainingOverviewController;
use App\Http\Controllers\Admin\SalesEmployeeController;
use App\Http\Controllers\Admin\SupportTicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductInquiryController;
use App\Http\Controllers\Admin\InquiryController;
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

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::patch('/clients/{client}/status', [ClientController::class, 'toggleStatus'])->name('clients.status.toggle');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/stage', [ProjectController::class, 'updateStage'])->name('projects.stage.update');
    Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status.update');

    Route::get('/projects/{project}/documents', [ProjectDocumentGroupController::class, 'index'])->name('projects.documents.index');
    Route::get('/projects/{project}/documents/{group}', [ProjectDocumentGroupController::class, 'show'])->name('projects.documents.show');
    Route::patch('/projects/{project}/documents/{group}/{field}', [ProjectDocumentController::class, 'update'])->name('projects.documents.update');
    Route::put('/projects/{project}/documents/{group}/{field}', [ProjectDocumentController::class, 'edit'])->name('projects.documents.edit');
    Route::delete('/projects/{project}/documents/{group}/{field}', [ProjectDocumentController::class, 'clear'])->name('projects.documents.clear');
    Route::patch('/projects/{project}/customization-requests/{customizationRequest}', [CustomizationRequestController::class, 'update'])->name('projects.customization-requests.update');
    Route::patch('/projects/{project}/sales-employee', [ProjectSalesAssignmentController::class, 'update'])->name('projects.sales-employee.update');

    Route::get('/sales-employees', [SalesEmployeeController::class, 'index'])->name('sales-employees.index');
    Route::post('/sales-employees', [SalesEmployeeController::class, 'store'])->name('sales-employees.store');
    Route::get('/sales-employees/{salesEmployee}', [SalesEmployeeController::class, 'show'])->name('sales-employees.show');
    Route::put('/sales-employees/{salesEmployee}', [SalesEmployeeController::class, 'update'])->name('sales-employees.update');
    Route::delete('/sales-employees/{salesEmployee}', [SalesEmployeeController::class, 'destroy'])->name('sales-employees.destroy');

    Route::get('/documents', [DocumentReviewController::class, 'index'])->name('documents.index');

    Route::get('/training', [ProjectTrainingOverviewController::class, 'index'])->name('training.index');
    Route::post('/training-videos', [ProductTrainingController::class, 'store'])->name('training-videos.store');
    Route::delete('/training-videos/{training}', [ProductTrainingController::class, 'destroy'])->name('training-videos.destroy');
    Route::post('/products/{product}/training-videos/reorder', [ProductTrainingController::class, 'reorder'])->name('training-videos.reorder');

    Route::get('/customization-requests', [CustomizationRequestOverviewController::class, 'index'])->name('customization-requests.index');

    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/{supportTicket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{supportTicket}/messages', [SupportTicketController::class, 'reply'])->name('support.messages.store');
    Route::patch('/support/{supportTicket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.status.update');

    Route::get('/lms/products', [LmsProductController::class, 'index'])->name('lms.products.index');
    Route::post('/lms/products', [LmsProductController::class, 'store'])->name('lms.products.store');
    Route::get('/lms/products/{lmsProduct}', [LmsProductController::class, 'show'])->name('lms.products.show');
    Route::put('/lms/products/{lmsProduct}', [LmsProductController::class, 'update'])->name('lms.products.update');
    Route::delete('/lms/products/{lmsProduct}', [LmsProductController::class, 'destroy'])->name('lms.products.destroy');

    Route::get('/lms/products/{lmsProduct}/preview', [LmsProductController::class, 'preview'])->name('lms.products.preview');
    Route::get('/lms/products/{lmsProduct}/preview/articles/{lmsArticle}', [LmsProductController::class, 'previewArticle'])->name('lms.products.preview.article');

    Route::post('/lms/products/{lmsProduct}/categories', [LmsCategoryController::class, 'store'])->name('lms.categories.store');
    Route::put('/lms/categories/{lmsCategory}', [LmsCategoryController::class, 'update'])->name('lms.categories.update');
    Route::delete('/lms/categories/{lmsCategory}', [LmsCategoryController::class, 'destroy'])->name('lms.categories.destroy');

    Route::post('/lms/categories/{lmsCategory}/sub-categories', [LmsSubCategoryController::class, 'store'])->name('lms.sub-categories.store');
    Route::put('/lms/sub-categories/{lmsSubCategory}', [LmsSubCategoryController::class, 'update'])->name('lms.sub-categories.update');
    Route::delete('/lms/sub-categories/{lmsSubCategory}', [LmsSubCategoryController::class, 'destroy'])->name('lms.sub-categories.destroy');

    Route::get('/lms/products/{lmsProduct}/articles/create', [LmsArticleController::class, 'create'])->name('lms.articles.create');
    Route::post('/lms/products/{lmsProduct}/articles', [LmsArticleController::class, 'store'])->name('lms.articles.store');
    Route::get('/lms/articles/{lmsArticle}/edit', [LmsArticleController::class, 'edit'])->name('lms.articles.edit');
    Route::put('/lms/articles/{lmsArticle}', [LmsArticleController::class, 'update'])->name('lms.articles.update');
    Route::delete('/lms/articles/{lmsArticle}', [LmsArticleController::class, 'destroy'])->name('lms.articles.destroy');
    Route::post('/lms/articles/upload-image', [LmsArticleController::class, 'uploadImage'])->name('lms.articles.upload-image');

    Route::get('/lms/products/{lmsProduct}/clients', [LmsProductClientController::class, 'index'])->name('lms.products.clients.index');
    Route::post('/lms/products/{lmsProduct}/clients/bulk-assign', [LmsProductClientController::class, 'bulkAssign'])->name('lms.products.clients.bulk-assign');
    Route::patch('/lms/products/{lmsProduct}/clients/{client}', [LmsProductClientController::class, 'toggle'])->name('lms.products.clients.toggle');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
    Route::get('/product-inquiry',[ProductInquiryController::class,'index'])->name('product.inquiry.index');
    Route::patch('/inquiries/{id}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
});
