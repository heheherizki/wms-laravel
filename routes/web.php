<?php

use Illuminate\Support\Facades\Route;

// Import Controllers agar kode lebih bersih
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. GUEST / AUTHENTICATION
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// =========================================================================
// 2. PROTECTED ROUTES (STAFF & ADMIN)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // --- PROFILE ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    // --- MASTER DATA (READ/WRITE for Staff) ---
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);

    // --- PRODUCTS (READ ONLY for Staff) ---
    // Staff bisa lihat history dan print barcode, tapi tidak bisa hapus/edit master
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/{id}/history', 'history')->name('products.history');
        Route::get('/products/{id}/barcode', 'printBarcode')->name('products.barcode');
    });

    // --- TRANSACTIONS (STOCK IN/OUT) ---
    Route::controller(TransactionController::class)->group(function () {
        Route::post('/products/in', 'storeIn')->name('products.in');
        Route::post('/products/out', 'storeOut')->name('products.out');
    });

    // --- PURCHASING (PEMBELIAN / PO) ---
    Route::resource('purchases', PurchaseController::class);
    Route::controller(PurchaseController::class)->group(function () {
        Route::patch('/purchases/{id}/complete', 'markAsCompleted')->name('purchases.complete');
        Route::get('/purchases/{id}/print', 'print')->name('purchases.print');
    });

    // --- SALES (PENJUALAN / SO) ---
    Route::resource('sales', SalesOrderController::class);
    Route::controller(SalesOrderController::class)->group(function () {
    Route::patch('/sales/{id}/ship', 'markAsShipped')->name('sales.ship'); // Legacy full ship
    Route::get('/sales/{id}/print-so', 'printSo')->name('sales.print_so'); // Picking List
        // Route print invoice/shipment lama bisa dihapus jika sudah pindah ke modul baru,
        // atau dibiarkan untuk kompatibilitas data lama.
    
    // Route untuk cek ulang status per Order
    Route::patch('/sales/{id}/refresh', [\App\Http\Controllers\SalesOrderController::class, 'refreshStatus'])->name('sales.refresh');    

    });

    // --- SHIPMENT (PENGIRIMAN PARTIAL) ---
    Route::controller(ShipmentController::class)->group(function () {
        Route::get('/shipments', 'index')->name('shipments.index');
        Route::get('/sales/{id}/shipment/create', 'create')->name('shipments.create');
        Route::post('/sales/{id}/shipment', 'store')->name('shipments.store');
        Route::get('/shipments/{id}/print', 'print')->name('shipments.print');
    });

    // --- INVOICE (KEUANGAN) ---
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoices', 'index')->name('invoices.index');
        Route::post('/shipments/{id}/create-invoice', 'createFromShipment')->name('invoices.createFromShipment');
        Route::get('/invoices/{id}/print', 'print')->name('invoices.print');
        Route::get('/invoices/{id}', 'show')->name('invoices.show');
    });

    // Route Bayar Hutang Supplier
    Route::get('/purchases/{id}/pay', [App\Http\Controllers\PurchasePaymentController::class, 'create'])->name('purchases.pay');
    Route::post('/purchases/pay', [App\Http\Controllers\PurchasePaymentController::class, 'store'])->name('purchases.payment.store');
    Route::get('/purchases/payments/{id}/print', [App\Http\Controllers\PurchasePaymentController::class, 'print'])->name('purchases.payments.print');
    // Route Terima Barang (Partial)
    Route::get('/purchases/{id}/receive', [App\Http\Controllers\PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{id}/receive', [App\Http\Controllers\PurchaseController::class, 'processReceive'])->name('purchases.receive.store');

    // Customer Statement
    Route::get('/customers/{id}/statement', [\App\Http\Controllers\StatementController::class, 'index'])->name('customers.statement');

    // --- SALES RETURN (RETUR PENJUALAN) ---
    Route::resource('returns', \App\Http\Controllers\SalesReturnController::class);

    // API Helper untuk Retur (Ambil produk shipped per SO)
    Route::get('/returns/{id}/print', [\App\Http\Controllers\SalesReturnController::class, 'print'])->name('returns.print');
    Route::get('/api/sales/{id}/shipped-items', [\App\Http\Controllers\SalesReturnController::class, 'getShippedProducts']);
    
    // Route khusus untuk Approve/Reject (Hanya Admin yang boleh)
    Route::middleware(['admin'])->group(function () {
        Route::patch('/returns/{id}/approve', [\App\Http\Controllers\SalesReturnController::class, 'approve'])->name('returns.approve');
        Route::patch('/returns/{id}/reject', [\App\Http\Controllers\SalesReturnController::class, 'reject'])->name('returns.reject');
    });

    // --- PAYMENTS (PEMBAYARAN) ---
    Route::get('/invoices/{id}/payment/create', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}/print', [\App\Http\Controllers\PaymentController::class, 'print'])->name('payments.print');
    
    // Admin Only Delete Payment
    Route::middleware(['admin'])->delete('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');


    // =====================================================================
    // 3. ADMIN ONLY ZONE (SENSITIVE ACTIONS)
    // =====================================================================
    Route::middleware(['admin'])->group(function () {

        // USER MANAGEMENT
        Route::resource('users', UserController::class);

        // SYSTEM BACKUP & RESTORE (BARU)
        Route::controller(\App\Http\Controllers\BackupController::class)->group(function() {
            Route::get('/system/maintenance', 'index')->name('system.backup'); // Halaman Utama
            Route::get('/system/backup/download', 'download')->name('system.backup.download'); // Aksi Download
            Route::post('/system/backup/restore', 'restore')->name('system.backup.restore'); // Aksi Upload
        });

        // PRODUCT MANAGEMENT (CREATE, UPDATE, DELETE)
        Route::controller(ProductController::class)->group(function () {
            Route::post('/products', 'store')->name('products.store');
            Route::put('/products/{id}', 'update')->name('products.update');
            Route::delete('/products/{id}', 'destroy')->name('products.destroy');
        });

        // REPORTS & EXPORTS
        Route::controller(ReportController::class)->group(function () {
            // Dashboard Laporan
            Route::get('/reports', 'index')->name('reports.index');
            
            // Laporan Spesifik
            Route::get('/reports/history', 'history')->name('reports.history');
            Route::get('/reports/stock', 'stock')->name('reports.stock');
            Route::get('/reports/sales', 'sales')->name('reports.sales');

            // Export Actions
            Route::get('/reports/export/excel', 'exportExcel')->name('reports.export_excel');
            Route::get('/reports/export/pdf', 'exportPdf')->name('reports.export_pdf');

            // Laporan Piutang
            Route::get('/reports/receivables', 'accountsReceivable')->name('reports.receivables');

            // Hutang (AP) --> INI YANG BARU KITA INTEGRASIKAN
            Route::get('/debt', [App\Http\Controllers\ReportController::class, 'accountsPayable'])->name('reports.debt.index');
            Route::get('/debt/print', [App\Http\Controllers\ReportController::class, 'accountsPayablePrint'])->name('reports.debt.print');
        });

        // Open Hold Sales Order
        Route::patch('/sales/{id}/approve', [\App\Http\Controllers\SalesOrderController::class, 'approve'])->name('sales.approve');

        Route::patch('/customers/{id}/unlock', [\App\Http\Controllers\CustomerController::class, 'unlock'])->name('customers.unlock');

    }); // End Admin Middleware

}); // End Auth Middleware