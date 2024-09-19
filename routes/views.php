<?php

use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('ordenes compra', [PurchaseOrderController::class, 'getAllOrders'])->name('orders.list');
    Route::get('ordenes compra/nueva orden', [PurchaseOrderController::class, 'createOrder'])->name('orders.create');
    Route::post('ordenes compra/nueva orden', [PurchaseOrderController::class, 'storeOrder'])->name('orders.store');
});
