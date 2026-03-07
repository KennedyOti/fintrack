<?php

use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\RecurringTransactionController;
use App\Http\Controllers\Portal\ClientController;
use App\Http\Controllers\Portal\IncomeController;
use App\Http\Controllers\Portal\IncomeCategoryController;
use App\Http\Controllers\Portal\ExpenseController;
use App\Http\Controllers\Portal\ExpenseCategoryController;
use App\Http\Controllers\Portal\ProjectController;
use App\Http\Controllers\Portal\MilestoneController;
use App\Http\Controllers\Portal\TaskController;
use App\Http\Controllers\Portal\TimeLogController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\QuoteController;
use App\Http\Controllers\Portal\SavingsController;
use App\Http\Controllers\Portal\DebtController;
use App\Http\Controllers\Portal\FinanceController;
use App\Http\Controllers\Portal\SettingsController;
use App\Http\Controllers\Portal\NotificationController;
use App\Http\Controllers\Portal\ExportController;
use App\Http\Controllers\Portal\ClientViewController;
use App\Http\Controllers\Portal\QuickAddController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\HowItWorksController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\FreeDocsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('website.home');
})->name('home');

Route::get('/about',        [AboutController::class,      'index'])->name('about');
Route::get('/products',     [ProductsController::class,   'index'])->name('products');
Route::get('/how-it-works', [HowItWorksController::class, 'index'])->name('how-it-works');

// ── Free Business Docs (public, no auth) ─────────────────────────────────
Route::prefix('free-docs')->name('free-docs.')->group(function () {
    Route::get('/',                         [FreeDocsController::class, 'index'])->name('index');
    Route::get('/builder/{type}',           [FreeDocsController::class, 'builder'])->name('builder');
    Route::post('/generate-pdf',            [FreeDocsController::class, 'generatePdf'])->name('generate-pdf');
    Route::post('/save',                    [FreeDocsController::class, 'save'])->name('save');
    Route::get('/view/{token}',             [FreeDocsController::class, 'view'])->name('view');
    Route::get('/view/{token}/pdf',         [FreeDocsController::class, 'viewPdf'])->name('view-pdf');
});

// ── Public Blog ──────────────────────────────────────────────────────────
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/',                   [BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug}',    [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}',         [BlogController::class, 'tag'])->name('tag');
    Route::post('/{slug}/like',       [BlogController::class, 'like'])->name('like');
    Route::post('/{slug}/comment',    [BlogController::class, 'comment'])->name('comment');
    Route::get('/{slug}',             [BlogController::class, 'show'])->name('show');
});

// ── Shareable Client Links (no auth required) ──────────────────────────────
Route::prefix('view')->name('public.')->group(function () {
    Route::get('/invoice/{token}',     [ClientViewController::class, 'viewInvoice'])->name('invoice');
    Route::get('/invoice/{token}/pdf', [ClientViewController::class, 'downloadInvoicePdf'])->name('invoice.pdf');
    Route::get('/quote/{token}',       [ClientViewController::class, 'viewQuote'])->name('quote');
    Route::get('/quote/{token}/pdf',   [ClientViewController::class, 'downloadQuotePdf'])->name('quote.pdf');
    Route::post('/quote/{token}/accept', [ClientViewController::class, 'acceptQuote'])->name('quote.accept');
    Route::post('/quote/{token}/reject', [ClientViewController::class, 'rejectQuote'])->name('quote.reject');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/portal/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Clients
    Route::resource('clients', ClientController::class);
    
    // Income Categories
    Route::get('/income/categories', [IncomeCategoryController::class, 'index'])->name('income.categories.index');
    Route::get('/income/categories/create', [IncomeCategoryController::class, 'create'])->name('income.categories.create');
    Route::post('/income/categories', [IncomeCategoryController::class, 'store'])->name('income.categories.store');
    Route::get('/income/categories/{category}/edit', [IncomeCategoryController::class, 'edit'])->name('income.categories.edit');
    Route::put('/income/categories/{category}', [IncomeCategoryController::class, 'update'])->name('income.categories.update');
    Route::delete('/income/categories/{category}', [IncomeCategoryController::class, 'destroy'])->name('income.categories.destroy');
    
    // Income
    Route::resource('income', IncomeController::class);
    
    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::get('/expenses/{expense}/receipt', [ExpenseController::class, 'receiptView'])->name('expenses.receipt.view');
    Route::delete('/expenses/{expense}/receipt', [ExpenseController::class, 'receiptDelete'])->name('expenses.receipt.delete');
    
    // Expense Categories
    Route::get('/expense/categories', [ExpenseCategoryController::class, 'index'])->name('expense.categories.index');
    Route::get('/expense/categories/create', [ExpenseCategoryController::class, 'create'])->name('expense.categories.create');
    Route::post('/expense/categories', [ExpenseCategoryController::class, 'store'])->name('expense.categories.store');
    Route::get('/expense/category/{category}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense.categories.edit');
    Route::put('/expense/category/{category}', [ExpenseCategoryController::class, 'update'])->name('expense.categories.update');
    Route::delete('/expense/category/{category}', [ExpenseCategoryController::class, 'destroy'])->name('expense.categories.destroy');
    
    // Projects
    Route::resource('projects', ProjectController::class);

    // Project Milestones
    Route::post('projects/{project}/milestones', [MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::put('projects/{project}/milestones/{milestone}', [MilestoneController::class, 'update'])->name('projects.milestones.update');
    Route::delete('projects/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('projects.milestones.destroy');
    Route::patch('projects/{project}/milestones/{milestone}/status', [MilestoneController::class, 'updateStatus'])->name('projects.milestones.updateStatus');

    // Project Tasks
    Route::get('projects/{project}/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::post('projects/{project}/tasks/reorder', [TaskController::class, 'reorder'])->name('projects.tasks.reorder');
    Route::get('projects/{project}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('projects.tasks.edit');
    Route::put('projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::patch('projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('projects.tasks.updateStatus');

    // Project Time Logs
    Route::post('projects/{project}/tasks/{task}/logs', [TimeLogController::class, 'store'])->name('projects.tasks.logs.store');
    Route::delete('projects/{project}/tasks/{task}/logs/{log}', [TimeLogController::class, 'destroy'])->name('projects.tasks.logs.destroy');
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment.store');
    Route::delete('invoices/{invoice}/payment/{payment}', [InvoiceController::class, 'destroyPayment'])->name('invoices.payment.destroy');
    Route::post('invoices/{invoice}/share', [InvoiceController::class, 'generateShareLink'])->name('invoices.share');
    Route::delete('invoices/{invoice}/share', [InvoiceController::class, 'revokeShareLink'])->name('invoices.share.revoke');

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.status');
    Route::get('quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    Route::post('quotes/{quote}/share', [QuoteController::class, 'generateShareLink'])->name('quotes.share');
    Route::delete('quotes/{quote}/share', [QuoteController::class, 'revokeShareLink'])->name('quotes.share.revoke');
    
    // Savings
    Route::resource('savings', SavingsController::class);
    Route::post('savings/{savings}/deposit', [SavingsController::class, 'deposit'])->name('savings.deposit');
    Route::post('savings/{savings}/withdraw', [SavingsController::class, 'withdraw'])->name('savings.withdraw');
    Route::post('savings/quick-save', [SavingsController::class, 'quickSave'])->name('savings.quickSave');
    
    // Debts - Receivable
    Route::get('/debts/receivable', [DebtController::class, 'indexReceivable'])->name('debts.receivable.index');
    Route::get('/debts/receivable/create', [DebtController::class, 'createReceivable'])->name('debts.receivable.create');
    Route::post('/debts/receivable', [DebtController::class, 'storeReceivable'])->name('debts.receivable.store');
    Route::get('/debts/receivable/{debt}', [DebtController::class, 'showReceivable'])->name('debts.receivable.show');
    Route::get('/debts/receivable/{debt}/edit', [DebtController::class, 'editReceivable'])->name('debts.receivable.edit');
    Route::put('/debts/receivable/{debt}', [DebtController::class, 'updateReceivable'])->name('debts.receivable.update');
    Route::delete('/debts/receivable/{debt}', [DebtController::class, 'destroyReceivable'])->name('debts.receivable.destroy');
    
    // Debts - Payable
    Route::get('/debts/payable', [DebtController::class, 'indexPayable'])->name('debts.payable.index');
    Route::get('/debts/payable/create', [DebtController::class, 'createPayable'])->name('debts.payable.create');
    Route::post('/debts/payable', [DebtController::class, 'storePayable'])->name('debts.payable.store');
    Route::get('/debts/payable/{debt}', [DebtController::class, 'showPayable'])->name('debts.payable.show');
    Route::get('/debts/payable/{debt}/edit', [DebtController::class, 'editPayable'])->name('debts.payable.edit');
    Route::put('/debts/payable/{debt}', [DebtController::class, 'updatePayable'])->name('debts.payable.update');
    Route::delete('/debts/payable/{debt}', [DebtController::class, 'destroyPayable'])->name('debts.payable.destroy');
    
    // Recurring Transactions
    Route::resource('recurring', RecurringTransactionController::class);
    Route::patch('recurring/{recurring}/toggle', [RecurringTransactionController::class, 'toggle'])->name('recurring.toggle');
    Route::post('recurring/{recurring}/generate-now', [RecurringTransactionController::class, 'generateNow'])->name('recurring.generateNow');

    // Finances
    Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/generate-now', [NotificationController::class, 'generateNow'])->name('notifications.generateNow');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/clear/read', [NotificationController::class, 'destroyRead'])->name('notifications.destroyRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/currency', [SettingsController::class, 'updateCurrency'])->name('settings.currency.update');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::post('/settings/dark-mode', [SettingsController::class, 'updateDarkMode'])->name('settings.darkMode');
    Route::post('/settings/logo', [SettingsController::class, 'uploadLogo'])->name('settings.logo.upload');
    Route::delete('/settings/logo', [SettingsController::class, 'removeLogo'])->name('settings.logo.remove');
    Route::put('/settings/documents', [SettingsController::class, 'updateDocuments'])->name('settings.documents.update');

    // Quick Add (income/expense from global modal)
    Route::post('/quick-add/income', [QuickAddController::class, 'storeIncome'])->name('quick-add.income');
    Route::post('/quick-add/expense', [QuickAddController::class, 'storeExpense'])->name('quick-add.expense');
    
    // Data Export
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::post('/export/download', [ExportController::class, 'download'])->name('export.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin Routes ────────────────────────────────────────────────────────────
// Protected by auth + email verification + admin role check
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users',                               [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{id}',                          [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit',                     [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}',                          [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{id}/toggle-status',          [UserManagementController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::delete('/users/{id}',                       [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/restore',                 [UserManagementController::class, 'restore'])->name('users.restore');
    Route::post('/users/{id}/reset-password',          [UserManagementController::class, 'resetPassword'])->name('users.resetPassword');

    // Activity Logs
    Route::get('/activity-logs',                       [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::delete('/activity-logs',                    [ActivityLogController::class, 'clearOld'])->name('activity-logs.clearOld');

    // System Settings
    Route::get('/settings',                            [SystemSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings',                           [SystemSettingController::class, 'update'])->name('settings.update');

    // ── Blog Management (Admin only) ─────────────────────────────────────
    // Blog Posts
    Route::get('/blog',                                [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create',                         [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog',                               [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blog}/edit',                    [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blog}',                         [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{blog}',                      [AdminBlogController::class, 'destroy'])->name('blog.destroy');
    Route::patch('/blog/{blog}/toggle-status',         [AdminBlogController::class, 'toggleStatus'])->name('blog.toggleStatus');
    Route::post('/blog/upload-image',                  [AdminBlogController::class, 'uploadImage'])->name('blog.upload-image');

    // Blog Categories
    Route::get('/blog/categories',                     [BlogCategoryController::class, 'index'])->name('blog.categories.index');
    Route::get('/blog/categories/create',              [BlogCategoryController::class, 'create'])->name('blog.categories.create');
    Route::post('/blog/categories',                    [BlogCategoryController::class, 'store'])->name('blog.categories.store');
    Route::get('/blog/categories/{category}/edit',     [BlogCategoryController::class, 'edit'])->name('blog.categories.edit');
    Route::put('/blog/categories/{category}',          [BlogCategoryController::class, 'update'])->name('blog.categories.update');
    Route::delete('/blog/categories/{category}',       [BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');

    // Blog Comments
    Route::get('/blog/comments',                       [BlogCommentController::class, 'index'])->name('blog.comments.index');
    Route::post('/blog/comments/{comment}/approve',    [BlogCommentController::class, 'approve'])->name('blog.comments.approve');
    Route::post('/blog/comments/{comment}/spam',       [BlogCommentController::class, 'spam'])->name('blog.comments.spam');
    Route::delete('/blog/comments/{comment}',          [BlogCommentController::class, 'destroy'])->name('blog.comments.destroy');
    Route::post('/blog/comments/bulk',                 [BlogCommentController::class, 'bulkAction'])->name('blog.comments.bulk');

});

require __DIR__.'/auth.php';
