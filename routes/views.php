<?php

use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('ordenes_compra', [PurchaseOrderController::class, 'getAllOrders'])->name('purchase_orders');
    // Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
