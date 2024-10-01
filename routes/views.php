<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RequestPraisController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rutas ordenes de compra
Route::middleware('auth')->group(function () {
    Route::get('ordenes compra', [PurchaseOrderController::class, 'getAllOrders'])->name('orders.list');
    Route::get('/dashboard', [DashboardController::class, 'getDataInventory'])->name('dashboard');
    Route::get('ordenes compra/nueva orden', [PurchaseOrderController::class, 'createOrder'])->name('orders.create');
    Route::post('ordenes compra/nueva orden', [PurchaseOrderController::class, 'storeOrder'])->name('orders.store');
    Route::get('ordenes compra/orden compra/{orderId}', [PurchaseOrderController::class,'detailOrder'])->name('orders.detail');
    Route::get('ordenes compra/editar orden/{orderId}', [PurchaseOrderController::class,'editOrders'])->name('orders.edit');
    Route::put('ordenes compra/update orden/{orderId}', [PurchaseOrderController::class,'updateOrders'])->name('orders.update');
    Route::get('solicitudes insumos', [RequestPraisController::class, 'getAllRequest'])->name('suppliesrequest.list');
    Route::get('detalle solicitudes insumos/{requestId}', [RequestPraisController::class, 'detailRequest'])->name('suppliesrequest.detail');
});
