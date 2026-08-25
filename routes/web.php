<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'data'])->middleware(['auth', 'verified'])->name('dashboard.data');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockTransferController;

Route::middleware(['auth'])->group(function () {
    Route::resource('product-categories', ProductCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('product-brands', ProductBrandController::class)->except(['show', 'create', 'edit']);
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products-data', [ProductController::class, 'data'])->name('products.data');
});

use App\Http\Controllers\PurchaseController;
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);

use App\Http\Controllers\StockOpnameController;

Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store', 'edit', 'update']);
Route::post('stock-opnames/{stockOpname}/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opnames.adjust');
Route::get('stock-opnames/{stockOpname}/pdf', [StockOpnameController::class, 'pdf'])->name('stock-opnames.pdf');
//transfer
Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
Route::post('stock-transfers/{stockTransfer}/ship', [StockTransferController::class, 'ship'])->name('stock-transfers.ship');
Route::post('stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');

//POS
use App\Http\Controllers\PosController;

Route::prefix('pos')->name('pos.')->group(function () {
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

use App\Http\Controllers\TechnicianFeeReportController;
Route::get('reports/technician-fee', [TechnicianFeeReportController::class, 'index'])->name('reports.technician-fee');
Route::get('reports/technician-fee/pdf', [TechnicianFeeReportController::class, 'pdf'])->name('reports.technician-fee.pdf');

use App\Http\Controllers\TechnicianManualFeeController;
Route::resource('technician-manual-fees', TechnicianManualFeeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

use App\Http\Controllers\TechnicianController;
Route::resource('technicians', TechnicianController::class)->parameters(['technicians' => 'technician']);
Route::patch('reports/technician-fee/{workOrderItemTechnician}', [TechnicianFeeReportController::class, 'updateFee'])->name('reports.technician-fee.update');
Route::get('reports/technician-fee/{workOrderItemTechnician}/edit', [TechnicianFeeReportController::class, 'edit'])->name('reports.technician-fee.edit');
Route::delete('reports/technician-fee/{workOrderItemTechnician}', [TechnicianFeeReportController::class, 'destroy'])->name('reports.technician-fee.destroy');
use App\Http\Controllers\ProductSubcategoryController;

Route::resource('product-subcategories', ProductSubcategoryController::class)
    ->parameters(['product-subcategories' => 'subcategory'])
    ->except(['show', 'create', 'edit']);

Route::post('product-subcategories/quick', [ProductSubcategoryController::class, 'quickStore'])->name('product-subcategories.quick');
Route::post('product-brands/quick', [ProductBrandController::class, 'quickStore'])->name('product-brands.quick');
Route::post('suppliers/quick', [SupplierController::class, 'quickStore'])->name('suppliers.quick');
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialReportController;

Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);

Route::get('reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
Route::get('reports/financial/pdf', [FinancialReportController::class, 'pdf'])->name('reports.financial.pdf');
Route::get('reports/financial/excel', [FinancialReportController::class, 'excel'])->name('reports.financial.excel');
use App\Http\Controllers\CashClosingController;

Route::get('cash-closings/today', [CashClosingController::class, 'today'])->name('cash-closings.today');
Route::post('cash-closings/open', [CashClosingController::class, 'open'])->name('cash-closings.open');
Route::post('cash-closings/{cashClosing}/close', [CashClosingController::class, 'close'])->name('cash-closings.close');
Route::get('cash-closings', [CashClosingController::class, 'index'])->name('cash-closings.index');
Route::get('reports/financial/sales-detail-excel', [FinancialReportController::class, 'salesDetailExcel'])->name('reports.financial.sales-detail-excel');
Route::get('reports/financial/sales-detail', [FinancialReportController::class, 'salesDetailIndex'])->name('reports.financial.sales-detail');
use App\Http\Controllers\WarrantyController;

Route::get('warranties', [WarrantyController::class, 'index'])->name('warranties.index');
Route::get('warranties/{warranty}', [WarrantyController::class, 'show'])->name('warranties.show');
Route::post('warranties/{warranty}/claim', [WarrantyController::class, 'claim'])->name('warranties.claim');
use App\Http\Controllers\CustomerController;

Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);
Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export');
Route::post('cash-closings/{cashClosing}/reopen', [CashClosingController::class, 'reopen'])->name('cash-closings.reopen');
use App\Http\Controllers\ActivityLogController;

Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

require __DIR__.'/auth.php';