<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLERS ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. GUEST (LOGIN & LOGOUT)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// =========================================================================
// 2. MAIN APPLICATION (AUTHENTICATED USERS)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD & PROFILE ---
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    // --- MASTER DATA (GENERAL ACCESS) ---
    // Note: Delete action mungkin perlu diproteksi di view/controller jika staff dilarang hapus
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{id}/statement', [StatementController::class, 'index'])->name('customers.statement');

    // --- INVENTORY (PRODUCTS & TRANSACTIONS) ---
    
    // 1. Route Produk (CRUD & History)
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/create', 'create')->name('products.create');
        Route::post('/products', 'store')->name('products.store');
        Route::get('/products/{id}/edit', 'edit')->name('products.edit');
        Route::put('/products/{id}', 'update')->name('products.update');
        Route::get('/products/{id}/history', 'history')->name('products.history');
        Route::get('/products/{id}/barcode', 'printBarcode')->name('products.barcode');
        Route::delete('/products/{id}', 'destroy')->name('products.destroy');
    });

    // 2. Route Transaksi Cepat (Masuk & Keluar) - INI YANG HILANG
    Route::controller(TransactionController::class)->group(function () {
        Route::post('/products/in', 'storeIn')->name('products.in');
        Route::post('/products/out', 'storeOut')->name('products.out');
    });

    // --- PURCHASING (PO & RETURNS) ---
    Route::resource('purchases', PurchaseController::class);
    Route::controller(PurchaseController::class)->group(function () {
        Route::patch('/purchases/{id}/complete', 'markAsCompleted')->name('purchases.complete');
        Route::get('/purchases/{id}/print', 'print')->name('purchases.print');
        Route::get('/purchases/{id}/receive', 'receive')->name('purchases.receive'); // Form Terima
        Route::post('/purchases/{id}/receive', 'processReceive')->name('purchases.receive.store'); // Proses Terima
    });

    // Purchase Payments (Hutang)
    Route::controller(PurchasePaymentController::class)->group(function() {
        Route::get('/purchases/{id}/pay', 'create')->name('purchases.pay');
        Route::post('/purchase-payments', 'store')->name('purchase_payments.store');
        Route::get('/purchases/payments/{id}/print', 'print')->name('purchases.payments.print');
    });

    // Purchase Returns
    Route::resource('purchase_returns', PurchaseReturnController::class);
    Route::controller(PurchaseReturnController::class)->group(function() {
        Route::get('/purchase_returns/{id}/print', 'print')->name('purchase_returns.print');
        Route::get('/api/purchases/{id}/items', 'getReceivedProducts');
        // Approval actions dipindah ke Admin Section di bawah
    });

    // --- SALES (SO & RETURNS) ---
    Route::resource('sales', SalesOrderController::class);
    Route::controller(SalesOrderController::class)->group(function () {
        Route::patch('/sales/{id}/ship', 'markAsShipped')->name('sales.ship');
        Route::get('/sales/{id}/print-so', 'printSo')->name('sales.print_so');
        Route::patch('/sales/{id}/refresh', 'refreshStatus')->name('sales.refresh');
    });

    // Sales Returns
    Route::resource('returns', SalesReturnController::class);
    Route::controller(SalesReturnController::class)->group(function() {
        Route::get('/returns/{id}/print', 'print')->name('returns.print');
        Route::get('/api/sales/{id}/shipped-items', 'getShippedProducts');
    });

    // --- LOGISTICS (SHIPMENTS) ---
    Route::controller(ShipmentController::class)->group(function () {
        Route::get('/shipments', 'index')->name('shipments.index');
        Route::get('/sales/{id}/shipment/create', 'create')->name('shipments.create');
        Route::post('/sales/{id}/shipment', 'store')->name('shipments.store');
        Route::get('/shipments/{id}/print', 'print')->name('shipments.print');
        Route::get('/shipments/{id}', 'show')->name('shipments.show');
    });

    // --- FINANCE (INVOICES & PAYMENTS) ---
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoices', 'index')->name('invoices.index');
        Route::post('/shipments/{id}/create-invoice', 'createFromShipment')->name('invoices.createFromShipment');
        Route::get('/invoices/{id}/print', 'print')->name('invoices.print');
        Route::get('/invoices/{id}', 'show')->name('invoices.show');
    });

    Route::controller(PaymentController::class)->group(function() {
        Route::get('/invoices/{id}/payment/create', 'create')->name('payments.create');
        Route::post('/payments', 'store')->name('payments.store');
        Route::get('/payments/{id}/print', 'print')->name('payments.print');
    });

    // --- CASH & BANK MANAGEMENT ---
    Route::prefix('finance')->group(function () {
        Route::controller(FinanceController::class)->group(function() {
            // Dashboard & Akun
            Route::get('/', 'index')->name('finance.index');
            Route::get('/accounts/create', 'createAccount')->name('finance.accounts.create');
            Route::post('/accounts', 'storeAccount')->name('finance.accounts.store');
            Route::get('/accounts/{id}/edit', 'editAccount')->name('finance.accounts.edit');
            Route::patch('/accounts/{id}', 'updateAccount')->name('finance.accounts.update');
            Route::delete('/accounts/{id}', 'destroyAccount')->name('finance.accounts.destroy');
            
            // Kategori
            Route::post('/categories', 'storeCategory')->name('finance.categories.store');
            Route::delete('/categories/{id}', 'destroyCategory')->name('finance.categories.destroy');

            // Transaksi (Expense/Income)
            Route::get('/transactions', 'transactions')->name('finance.transactions.index');
            Route::get('/transactions/create', 'createTransaction')->name('finance.transactions.create');
            Route::post('/transactions', 'storeTransaction')->name('finance.transactions.store');
            
            // Mutasi (Transfer)
            Route::get('/transfer', 'createTransfer')->name('finance.transfer.create');
            Route::post('/transfer', 'storeTransfer')->name('finance.transfer.store');
        });
    });


    // =====================================================================
    // 3. ADMIN & SENSITIVE ACTIONS (PROTECTED BY ROLE)
    // =====================================================================
    // Middleware 'role:super_admin|admin' memastikan hanya role tersebut yang bisa akses
    Route::middleware(['role:super_admin|admin'])->group(function () {

        // --- USER MANAGEMENT ---
        Route::resource('users', UserController::class);

        // --- APPROVALS & UNLOCKS ---
        Route::patch('/sales/{id}/approve', [SalesOrderController::class, 'approve'])->name('sales.approve');
        Route::patch('/purchase_returns/{id}/approve', [PurchaseReturnController::class, 'approve'])->name('purchase_returns.approve');
        Route::patch('/purchase_returns/{id}/reject', [PurchaseReturnController::class, 'reject'])->name('purchase_returns.reject');
        Route::patch('/returns/{id}/approve', [SalesReturnController::class, 'approve'])->name('returns.approve');
        Route::patch('/returns/{id}/reject', [SalesReturnController::class, 'reject'])->name('returns.reject');
        Route::patch('/customers/{id}/unlock', [CustomerController::class, 'unlock'])->name('customers.unlock');

        // --- DELETE ACTIONS ---
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // --- SYSTEM MAINTENANCE ---
        Route::controller(BackupController::class)->group(function() {
            Route::get('/system/maintenance', 'index')->name('system.backup');
            Route::get('/system/backup/download', 'download')->name('system.backup.download');
            Route::post('/system/backup/restore', 'restore')->name('system.backup.restore');
        });

        // --- ADVANCED REPORTS ---
        Route::controller(ReportController::class)->group(function () {
            Route::get('/reports', 'index')->name('reports.index');
            
            // Operational Reports
            Route::get('/reports/history', 'history')->name('reports.history');
            Route::get('/reports/stock', 'stock')->name('reports.stock');
            Route::get('/reports/sales', 'sales')->name('reports.sales');
            Route::get('/reports/statement', 'supplierStatement')->name('reports.statement');

            // Financial Reports
            Route::get('/reports/receivables', 'accountsReceivable')->name('reports.receivables');
            Route::get('/debt', 'accountsPayable')->name('reports.debt.index');
            Route::get('/debt/print', 'accountsPayablePrint')->name('reports.debt.print');
            Route::get('/reports/profit-loss', 'profitLoss')->name('reports.profit_loss');
            Route::get('/reports/cash-flow', 'cashFlow')->name('reports.cash_flow');

            // Exports
            Route::get('/reports/export/excel', 'exportExcel')->name('reports.export_excel');
            Route::get('/reports/export/pdf', 'exportPdf')->name('reports.export_pdf');
        });

    }); // End Admin Middleware

}); // End Auth Middleware