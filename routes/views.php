<?php

use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('ordenes compra', [PurchaseOrderController::class, 'getAllOrders'])->name('orders.list');
    Route::get('ordenes compra/nueva orden', [PurchaseOrderController::class, 'createOrder'])->name('orders.create');
    Route::post('ordenes compra/nueva orden', [PurchaseOrderController::class, 'storeOrder'])->name('orders.store');
    Route::get('ordenes compra/orden compra/{orderId}', [PurchaseOrderController::class,'detailOrder'])->name('orders.detail');
    Route::get('ordenes compra/editar orden/{orderId}', [PurchaseOrderController::class,'editOrders'])->name('orders.edit');
    Route::put('ordenes compra/update orden/{orderId}', [PurchaseOrderController::class,'updateOrders'])->name('orders.update');
});
