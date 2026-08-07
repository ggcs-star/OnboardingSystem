<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseModuleController;
use App\Http\Controllers\Admin\CourseModuleQuizController;
use App\Http\Controllers\Admin\CoursePreviewController;
use App\Http\Controllers\Admin\CourseQuizCheckpointController;
use App\Http\Controllers\Admin\CourseQuizQuestionController;
use App\Http\Controllers\Admin\CourseQuizReviewController;
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
    Route::patch('/sales-employees/{salesEmployee}/status', [SalesEmployeeController::class, 'updateStatus'])->name('sales-employees.status.update');
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
    Route::patch('/lms/products/{lmsProduct}/status', [LmsProductController::class, 'updateStatus'])->name('lms.products.status.update');
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

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::patch('/courses/{course}/publish', [CourseController::class, 'togglePublish'])->name('courses.publish.toggle');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/courses/{course}/preview', [CoursePreviewController::class, 'show'])->name('courses.preview');
    Route::post('/course-lessons/{courseLesson}/preview-progress', [CoursePreviewController::class, 'progress'])->name('course-lessons.preview-progress');
    Route::post('/course-quiz-checkpoints/{checkpoint}/preview-answers', [CoursePreviewController::class, 'quizAnswer'])->name('course-quiz-answers.preview-store');

    Route::post('/courses/{course}/modules', [CourseModuleController::class, 'store'])->name('course-modules.store');
    Route::put('/course-modules/{courseModule}', [CourseModuleController::class, 'update'])->name('course-modules.update');
    Route::delete('/course-modules/{courseModule}', [CourseModuleController::class, 'destroy'])->name('course-modules.destroy');
    Route::post('/courses/{course}/modules/reorder', [CourseModuleController::class, 'reorder'])->name('course-modules.reorder');

    Route::get('/course-modules/{courseModule}/lessons/create', [CourseLessonController::class, 'create'])->name('course-lessons.create');
    Route::post('/course-modules/{courseModule}/lessons', [CourseLessonController::class, 'store'])->name('course-lessons.store');
    Route::get('/course-lessons/{courseLesson}/edit', [CourseLessonController::class, 'edit'])->name('course-lessons.edit');
    Route::put('/course-lessons/{courseLesson}', [CourseLessonController::class, 'update'])->name('course-lessons.update');
    Route::delete('/course-lessons/{courseLesson}', [CourseLessonController::class, 'destroy'])->name('course-lessons.destroy');
    Route::post('/course-modules/{courseModule}/lessons/reorder', [CourseLessonController::class, 'reorder'])->name('course-lessons.reorder');

    Route::post('/course-lessons/{courseLesson}/checkpoints', [CourseQuizCheckpointController::class, 'store'])->name('course-quiz-checkpoints.store');
    Route::put('/course-quiz-checkpoints/{checkpoint}', [CourseQuizCheckpointController::class, 'update'])->name('course-quiz-checkpoints.update');
    Route::delete('/course-quiz-checkpoints/{checkpoint}', [CourseQuizCheckpointController::class, 'destroy'])->name('course-quiz-checkpoints.destroy');

    Route::post('/course-modules/{courseModule}/quizzes', [CourseModuleQuizController::class, 'store'])->name('course-module-quizzes.store');
    Route::get('/course-module-quizzes/{checkpoint}/edit', [CourseModuleQuizController::class, 'edit'])->name('course-module-quizzes.edit');
    Route::put('/course-module-quizzes/{checkpoint}', [CourseModuleQuizController::class, 'update'])->name('course-module-quizzes.update');
    Route::delete('/course-module-quizzes/{checkpoint}', [CourseModuleQuizController::class, 'destroy'])->name('course-module-quizzes.destroy');

    Route::post('/course-quiz-checkpoints/{checkpoint}/questions', [CourseQuizQuestionController::class, 'store'])->name('course-quiz-questions.store');
    Route::put('/course-quiz-questions/{question}', [CourseQuizQuestionController::class, 'update'])->name('course-quiz-questions.update');
    Route::delete('/course-quiz-questions/{question}', [CourseQuizQuestionController::class, 'destroy'])->name('course-quiz-questions.destroy');

    Route::get('/course-quiz-answers/pending', [CourseQuizReviewController::class, 'index'])->name('course-quiz-answers.pending');
    Route::patch('/course-quiz-answers/{answer}/grade', [CourseQuizReviewController::class, 'grade'])->name('course-quiz-answers.grade');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
    Route::get('/product-inquiry',[ProductInquiryController::class,'index'])->name('product.inquiry.index');
    Route::patch('/inquiries/{id}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
});
