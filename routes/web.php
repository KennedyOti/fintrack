<?php

use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ClientController;
use App\Http\Controllers\Portal\IncomeController;
use App\Http\Controllers\Portal\IncomeCategoryController;
use App\Http\Controllers\Portal\ExpenseController;
use App\Http\Controllers\Portal\ExpenseCategoryController;
use App\Http\Controllers\Portal\ProjectController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\QuoteController;
use App\Http\Controllers\Portal\SavingsController;
use App\Http\Controllers\Portal\DebtController;
use App\Http\Controllers\Portal\FinanceController;
use App\Http\Controllers\Portal\SettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('website.home');
})->name('home');

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
    
    // Expense Categories
    Route::get('/expense/categories', [ExpenseCategoryController::class, 'index'])->name('expense.categories.index');
    Route::get('/expense/categories/create', [ExpenseCategoryController::class, 'create'])->name('expense.categories.create');
    Route::post('/expense/categories', [ExpenseCategoryController::class, 'store'])->name('expense.categories.store');
    Route::get('/expense/category/{category}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense.categories.edit');
    Route::put('/expense/category/{category}', [ExpenseCategoryController::class, 'update'])->name('expense.categories.update');
    Route::delete('/expense/category/{category}', [ExpenseCategoryController::class, 'destroy'])->name('expense.categories.destroy');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment.store');
    Route::delete('invoices/{invoice}/payment/{payment}', [InvoiceController::class, 'destroyPayment'])->name('invoices.payment.destroy');
    
    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    
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
    
    // Finances
    Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/currency', [SettingsController::class, 'updateCurrency'])->name('settings.currency.update');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
