<?php

use App\Http\Controllers\EditOrder;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('ordenes_compra', [PurchaseOrderController::class, 'getAllOrders'])->name('purchase_orders');
    Route::get('ordenes_compra_editar{pruchase_orders}', [PurchaseOrderController::class, 'edit'])->name('purchase_orders.edit');
    Route::put('ordenes_compra_update{purchase_orders}', [PurchaseOrderController::class, 'update'])->name('purchase_orders.update');
    // Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
