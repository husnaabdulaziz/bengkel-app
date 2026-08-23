<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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


require __DIR__.'/auth.php';
