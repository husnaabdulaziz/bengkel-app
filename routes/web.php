<?php

use App\Http\Controllers\SystemMenuController;
use App\Http\Controllers\SuperAdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSubcategoryController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TechnicianFeeReportController;
use App\Http\Controllers\TechnicianManualFeeController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionSettingController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\DataResetController;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ===== Profile (semua user login boleh edit profil sendiri, tidak perlu permission khusus) =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== Dashboard =====
Route::middleware(['auth', 'verified', 'menu_permission:access_dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
});

// ===== Produk =====
Route::middleware(['auth', 'menu_permission:access_produk'])->group(function () {
    Route::resource('product-categories', ProductCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('product-brands', ProductBrandController::class)->except(['show', 'create', 'edit']);
    Route::post('product-brands/quick', [ProductBrandController::class, 'quickStore'])->name('product-brands.quick');
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);
    Route::post('suppliers/quick', [SupplierController::class, 'quickStore'])->name('suppliers.quick');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products-data', [ProductController::class, 'data'])->name('products.data');
    Route::resource('product-subcategories', ProductSubcategoryController::class)
        ->parameters(['product-subcategories' => 'subcategory'])
        ->except(['show', 'create', 'edit']);
    Route::post('product-subcategories/quick', [ProductSubcategoryController::class, 'quickStore'])->name('product-subcategories.quick');
    Route::get('products/import', [ProductImportController::class, 'showForm'])->name('products.import');
    Route::get('products/import/template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');
    Route::post('products/import', [ProductImportController::class, 'import'])->name('products.import.store');

    Route::get('products/low-stock', [\App\Http\Controllers\LowStockController::class, 'index'])->name('products.low-stock');

    Route::get('products/stickers', [\App\Http\Controllers\ProductStickerController::class, 'index'])->name('products.stickers');
});

// ===== Pembelian =====
Route::middleware(['auth', 'menu_permission:access_pembelian'])->group(function () {
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);

    Route::get('purchases/create-po', [PurchaseController::class, 'poBuilder'])->name('purchases.create-po');
    Route::post('purchases/store-po', [PurchaseController::class, 'storePO'])->name('purchases.store-po');
    Route::get('purchases/{purchase}/receive', [PurchaseController::class, 'showReceive'])->name('purchases.receive.show');
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
});

// ===== Stock Opname =====
Route::middleware(['auth', 'menu_permission:access_stock_opname', 'menu_enabled:access_stock_opname'])->group(function () {
    Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('stock-opnames/{stockOpname}/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opnames.adjust');
    Route::get('stock-opnames/{stockOpname}/pdf', [StockOpnameController::class, 'pdf'])->name('stock-opnames.pdf');
});

// ===== Transfer Stock =====
Route::middleware(['auth', 'menu_permission:access_stock_transfer', 'menu_enabled:access_stock_transfer'])->group(function () {
    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('stock-transfers/{stockTransfer}/ship', [StockTransferController::class, 'ship'])->name('stock-transfers.ship');
    Route::post('stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');
});

// ===== POS =====
Route::middleware(['auth', 'menu_permission:access_pos'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/new', [PosController::class, 'create'])->name('create');
    Route::post('/', [PosController::class, 'store'])->name('store');
    Route::get('/search-customer', [PosController::class, 'searchCustomer'])->name('search-customer');
    Route::get('/search-product', [PosController::class, 'searchProduct'])->name('search-product');
    Route::get('/queue', [PosController::class, 'queue'])->name('queue');
    Route::get('/queue/data', [PosController::class, 'queueData'])->name('queue.data');
    Route::delete('/queue/{workOrder}', [PosController::class, 'destroy'])->name('queue.destroy');
    Route::get('/queue/{workOrder}', [PosController::class, 'showQueue'])->name('queue.show');
    Route::post('/queue/{workOrder}/items', [PosController::class, 'addItem'])->name('queue.add-item');
    Route::delete('/queue/{workOrder}/items/{item}', [PosController::class, 'removeItem'])->name('queue.remove-item');
    Route::post('/queue/{workOrder}/process', [PosController::class, 'process'])->name('queue.process');
    Route::get('/queue/{workOrder}/payment', [PosController::class, 'paymentForm'])->name('payment');
    Route::post('/queue/{workOrder}/payment', [PosController::class, 'confirmPayment'])->name('payment.confirm');
    Route::get('/invoice/{workOrder}', [PosController::class, 'invoice'])->name('invoice');
    Route::post('/queue/{workOrder}/technicians', [PosController::class, 'updateTechnicians'])->name('queue.update-technicians');
});

// ===== Mekanik & Fee Mekanik =====
Route::middleware(['auth', 'menu_permission:access_mekanik'])->group(function () {
    Route::resource('technicians', TechnicianController::class)->parameters(['technicians' => 'technician']);
    Route::resource('technician-manual-fees', TechnicianManualFeeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// reports/technician-fee dipakai di 2 menu (Fee Mekanik & Laporan Keuangan), jadi terima salah satu permission
Route::middleware(['auth', 'menu_permission:access_mekanik|access_laporan_keuangan'])->group(function () {
    Route::get('reports/technician-fee', [TechnicianFeeReportController::class, 'index'])->name('reports.technician-fee');
    Route::get('reports/technician-fee/pdf', [TechnicianFeeReportController::class, 'pdf'])->name('reports.technician-fee.pdf');
    Route::patch('reports/technician-fee/{workOrderItemTechnician}', [TechnicianFeeReportController::class, 'updateFee'])->name('reports.technician-fee.update');
    Route::get('reports/technician-fee/{workOrderItemTechnician}/edit', [TechnicianFeeReportController::class, 'edit'])->name('reports.technician-fee.edit');
    Route::delete('reports/technician-fee/{workOrderItemTechnician}', [TechnicianFeeReportController::class, 'destroy'])->name('reports.technician-fee.destroy');
});

// ===== Laporan Keuangan =====
Route::middleware(['auth', 'menu_permission:access_laporan_keuangan'])->group(function () {
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
    Route::get('reports/financial/pdf', [FinancialReportController::class, 'pdf'])->name('reports.financial.pdf');
    Route::get('reports/financial/excel', [FinancialReportController::class, 'excel'])->name('reports.financial.excel');
    Route::get('reports/financial/sales-detail', [FinancialReportController::class, 'salesDetailIndex'])->name('reports.financial.sales-detail');
    Route::get('reports/financial/sales-detail-excel', [FinancialReportController::class, 'salesDetailExcel'])->name('reports.financial.sales-detail-excel');
});

// ===== Kas Harian =====
Route::middleware(['auth', 'menu_permission:access_kas_harian'])->group(function () {
    Route::get('cash-closings/today', [CashClosingController::class, 'today'])->name('cash-closings.today');
    Route::post('cash-closings/open', [CashClosingController::class, 'open'])->name('cash-closings.open');
    Route::post('cash-closings/{cashClosing}/close', [CashClosingController::class, 'close'])->name('cash-closings.close');
    Route::post('cash-closings/{cashClosing}/reopen', [CashClosingController::class, 'reopen'])->name('cash-closings.reopen');
    Route::get('cash-closings', [CashClosingController::class, 'index'])->name('cash-closings.index');
});

// ===== Garansi =====
Route::middleware(['auth', 'menu_permission:access_garansi'])->group(function () {
    Route::get('warranties', [WarrantyController::class, 'index'])->name('warranties.index');
    Route::get('warranties/{warranty}', [WarrantyController::class, 'show'])->name('warranties.show');
    Route::post('warranties/{warranty}/claim', [WarrantyController::class, 'claim'])->name('warranties.claim');
});

// ===== Pelanggan =====
Route::middleware(['auth', 'menu_permission:access_pelanggan'])->group(function () {
    Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);
    Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export');
});

// ===== Log Aktivitas =====
Route::middleware(['auth', 'menu_permission:access_log_aktivitas'])->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// ===== Pengaturan Toko =====
Route::middleware(['auth', 'menu_permission:access_pengaturan_toko'])->group(function () {
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('settings/karyawan-access', [PermissionSettingController::class, 'edit'])->name('settings.karyawan-access');
    Route::post('settings/karyawan-access', [PermissionSettingController::class, 'update'])->name('settings.karyawan-access.update');
});

// ===== Super Admin (pakai class middleware langsung, bukan alias string, supaya tidak kena bug alias) =====
Route::middleware(['auth', EnsureSuperAdmin::class])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('system-menus', [SystemMenuController::class, 'edit'])->name('system-menus.edit');
    Route::post('system-menus', [SystemMenuController::class, 'update'])->name('system-menus.update');

    Route::get('users', [SuperAdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [SuperAdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [SuperAdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [SuperAdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');

    Route::get('reset-data', [DataResetController::class, 'edit'])->name('reset-data.edit');
    Route::post('reset-data', [DataResetController::class, 'process'])->name('reset-data.process');

    Route::get('permissions', [\App\Http\Controllers\SuperAdminPermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('permissions', [\App\Http\Controllers\SuperAdminPermissionController::class, 'update'])->name('permissions.update');

    Route::get('backup', [\App\Http\Controllers\SuperAdminBackupController::class, 'index'])->name('backup.index');
    Route::get('backup/download', [\App\Http\Controllers\SuperAdminBackupController::class, 'download'])->name('backup.download');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureSuperAdmin::class])->group(function () {
    Route::get('switch-company', [\App\Http\Controllers\CompanySwitchController::class, 'index'])->name('switch-company.index');
    Route::post('switch-company', [\App\Http\Controllers\CompanySwitchController::class, 'store'])->name('switch-company.store');
    Route::post('switch-company/clear', [\App\Http\Controllers\CompanySwitchController::class, 'clear'])->name('switch-company.clear');
});

require __DIR__.'/auth.php';