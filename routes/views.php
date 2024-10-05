<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RequestPraisController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rutas ordenes de compra
Route::middleware('auth')->group(function () {
    Route::get('ordenes compra', [PurchaseOrderController::class, 'getAllOrders'])->name('orders.list');
    Route::get('dashboard', [DashboardController::class, 'getDataInventory'])->name('dashboard');
    Route::get('ordenes compra/nueva orden', [PurchaseOrderController::class, 'createOrder'])->name('orders.create');
    Route::post('ordenes compra/nueva orden', [PurchaseOrderController::class, 'storeOrder'])->name('orders.store');
    Route::get('ordenes compra/orden compra/{orderId}', [PurchaseOrderController::class,'detailOrder'])->name('orders.detail');
    Route::get('ordenes compra/editar orden/{orderId}', [PurchaseOrderController::class,'editOrders'])->name('orders.edit');
    Route::put('ordenes compra/update orden/{orderId}', [PurchaseOrderController::class,'updateOrders'])->name('orders.update');
    Route::get('solicitudes insumos', [RequestPraisController::class, 'getAllRequest'])->name('suppliesrequest.list');
    Route::get('detalle solicitudes insumos/{requestId}', [RequestPraisController::class, 'detailRequest'])->name('suppliesrequest.detail');
    Route::get('transformaciones', [RequestPraisController::class, 'getAllRequestTransformation'])->name('transformationRequest.list');
    Route::get('transformaciones/nueva transformacion', [RequestPraisController::class, 'createTransformation'])->name('transformation.create');
    Route::post('transformaciones/nueva transformacion', [RequestPraisController::class, 'storeTransformation'])->name('transformation.store');
    Route::get('detalle solicitud de trasnformacion/{requestId}', [RequestPraisController::class, 'detailTransformation'])->name('transformation.detail');
});
