<?php

use App\Http\Controllers\Admin\CheatsheetOverviewController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CustomizationRequestController;
use App\Http\Controllers\Admin\CustomizationRequestOverviewController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDocumentFieldController;
use App\Http\Controllers\Admin\ProductDocumentGroupController;
use App\Http\Controllers\Admin\ProductFaqController;
use App\Http\Controllers\Admin\ProductCheatsheetController;
use App\Http\Controllers\Admin\ProductPolicyController;
use App\Http\Controllers\Admin\ProductRenewalSettingController;
use App\Http\Controllers\Admin\ProductTrainingController;
use App\Http\Controllers\Admin\ProjectDocumentController;
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

    Route::patch('/projects/{project}/documents/{group}/{field}', [ProjectDocumentController::class, 'update'])->name('projects.documents.update');
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

    Route::get('/cheatsheets', [CheatsheetOverviewController::class, 'index'])->name('cheatsheets.index');
    Route::post('/cheatsheets', [ProductCheatsheetController::class, 'store'])->name('cheatsheets.store');
    Route::get('/cheatsheets/{cheatsheet}/download', [ProductCheatsheetController::class, 'download'])->name('cheatsheets.download');
    Route::delete('/cheatsheets/{cheatsheet}', [ProductCheatsheetController::class, 'destroy'])->name('cheatsheets.destroy');
    Route::post('/products/{product}/cheatsheets/reorder', [ProductCheatsheetController::class, 'reorder'])->name('cheatsheets.reorder');

    Route::get('/customization-requests', [CustomizationRequestOverviewController::class, 'index'])->name('customization-requests.index');

    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/{supportTicket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{supportTicket}/messages', [SupportTicketController::class, 'reply'])->name('support.messages.store');
    Route::patch('/support/{supportTicket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.status.update');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
    Route::get('/product-inquiry',[ProductInquiryController::class,'index'])->name('product.inquiry.index');
    Route::patch('/inquiries/{id}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
});
