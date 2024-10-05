<?php

use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RequestPraisController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rutas ordenes de compra
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDataInventory'])->name('dashboard');
    Route::controller(PurchaseOrderController::class)->group(function(){
        Route::get('ordenes compra', 'getAllOrders')->name('orders.list');
        Route::get('ordenes compra/nueva orden',  'createOrder')->name('orders.create');
        Route::post('ordenes compra/nueva orden',  'storeOrder')->name('orders.store');
        Route::get('ordenes compra/orden compra/{orderId}', 'detailOrder')->name('orders.detail');
        Route::get('ordenes compra/editar orden/{orderId}', 'editOrders')->name('orders.edit');
        Route::put('ordenes compra/update orden/{orderId}', 'updateOrders')->name('orders.update');
    });
    Route::controller(RequestPraisController::class)->group(function(){
        Route::get('solicitudes insumos', 'getAllRequest')->name('suppliesrequest.list');
        Route::get('solicitudes insumos/solicitud insumos/{requestId}', 'detailRequest')->name('suppliesrequest.detail');
    });
    Route::controller(StockController::class)->group(function(){
        Route::get('stock', 'getStocks')->name('stock.dashboard');
        Route::get('stock/bodega/{warehouse_id}', 'getAllInventory')->name('stock.inventory');
        Route::get('stock/multiple bodega', 'getMultipleInventory')->name('stock.multiple');
        Route::get('api/bodega/{warehouse_id}', 'getInventory')->name('api.warehouse');
    });
    Route::get('solicitudes insumos', [RequestPraisController::class, 'getAllRequest'])->name('suppliesrequest.list');
    Route::get('detalle solicitudes insumos/{requestId}', [RequestPraisController::class, 'detailRequest'])->name('suppliesrequest.detail');
    Route::get('despachos', [DispatchController::class, 'getAllDispatch'])->name('dispatch.list');
    Route::get('detalle despachos/{dispatchId}', [DispatchController::class, 'detailDispatch'])->name('dispatch.detail');
});
