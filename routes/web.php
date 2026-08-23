<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
});



require __DIR__.'/auth.php';