<?php

use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabTransformationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RepackageController;
use App\Http\Controllers\RequestPraisController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryLocationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDataInventory'])->name('dashboard');
    Route::controller(PurchaseOrderController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Ordenes de Compra']], function () {
            Route::get('ordenes-compra', 'getAllOrders')->name('orders.list');
            Route::get('ordenes-compra/orden-compra/{orderId}', 'detailOrder')->name('orders.detail');
        });
        Route::group(['middleware' => ['can:Crear Ordenes de Compra']], function () {
            Route::get('ordenes-compra/nueva-orden', 'createOrder')->name('orders.create');
            Route::post('ordenes-compra/nueva-orden', 'storeOrder')->name('orders.store');
        });
        Route::group(['middleware' => ['can:Editar Ordenes de Compra']], function () {
            Route::get('ordenes-compra/editar-orden/{orderId}', 'editOrders')->name('orders.edit');
            Route::put('ordenes-compra/update-orden/{orderId}', 'updateOrders')->name('orders.update');
        });
    });
    Route::controller(DispatchController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Despachos']], function () {
            Route::get('despachos', [DispatchController::class, 'getAllDispatch'])->name('dispatch.list');
            Route::get('detalle-despachos/{dispatchId}', [DispatchController::class, 'detailDispatch'])->name('dispatch.detail');
        });
        Route::group(['middleware' => ['can:Crear Despachos']], function () {
            Route::get('despachos/nuevo-despacho', 'createDispatch')->name('dispatch.create');
            Route::post('despachos/nuevo-despacho', 'storeDispatch')->name('dispatch.store');
        });
    });
    Route::controller(RequestPraisController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Solicitudes Insumos']], function () {
            Route::get('solicitudes insumos', 'getAllRequest')->name('suppliesrequest.list');
            Route::get('solicitudes insumos/solicitud/{requestId}', 'detailRequest')->name('suppliesrequest.detail');
            Route::get('solicitudes insumos/validar', 'getValidationView')->name('suppliesrequest.validation');
            Route::get('solicitudes insumos/{requestId}/editar', 'showDetail')->name('requests.detail');
            Route::put('solicitudes insumos/{requestId}/actualizar', 'update')->name('suppliesrequest.update');
            Route::post('solicitudes insumos/{requestId}/aprobar', 'approveRequest')->name('suppliesrequests.approve');
            Route::post('solicitudes insumos/{requestId}/validate', 'validateRequest')->name('requests.validate');
        });
    });
    Route::controller(RequestPraisController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Solicitudes Transformacion']], function () {
            Route::get('transformaciones', 'getAllRequestTransformation')->name('transformationRequest.list');
            Route::get('transformaciones/transformacion/{requestId}', 'detailTransformation')->name('transformation.detail');
        });
        Route::group(['middleware' => ['can:Crear Solicitudes Transformacion']], function () {
            Route::get('transformaciones/nueva-transformacion', 'createTransformation')->name('transformation.create');
            Route::post('transformaciones/nueva-transformacion', 'storeTransformation')->name('transformation.store');
        });
    });
    Route::controller(StockController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Stock']], function () {
            Route::get('stock', 'getStocks')->name('stock.dashboard');
            Route::get('stock/bodega/{warehouse_id}', 'getAllInventory')->name('stock.inventory');
            Route::get('stock/multiple-bodega', 'getMultipleInventory')->name('stock.multiple');
            Route::get('api/bodega/{warehouse_id}', 'getInventory')->name('api.warehouse');
        });
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'getUsers')->name('users.list');
        Route::get('users/{user_id}', 'detailUser')->name('users.detail');
        Route::post('users/{user_id}', 'updateUserRolePermission')->name('users.role_permi');
        Route::get('api/permissions_roles/{roles_id}', 'getPermissionRol')->name('api.permi_roles');
        Route::get('permissions', 'getPermissions')->name('permissions.list');
        Route::post('permissions', 'storePermission')->name('permissions.store');
        Route::put('permissions', 'updatePermission')->name('permissions.update');
        Route::get('roles', 'getRoles')->name('roles.list');
        Route::post('roles', 'storeRole')->name('roles.store');
        Route::put('roles', 'updateRole')->name('roles.update');
    });
    Route::controller(RepackageController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Reenvases']], function () {
            Route::get('reenvase', 'getrepackage')->name('repackage.list');
        });
        Route::group(['middleware' => ['can:Crear Reenvases']], function () {
            Route::get('reenvase/nuevo-reenvase', 'createRepackage')->name('create.repackage');
            Route::post('reenvase/nuevo-reenvase', 'storeRepackage')->name('store.repackage');
        });
    });

    Route::controller(LabTransformationController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Reenvases']], function () {
            Route::get('transformaciones-de-laboratorio', 'getAllTransformation')->name('LabTransformation.list');
            Route::get('transformaciones-de-laboratorio/detalle-de-transformacion/{transformationId}', 'detailLabTransformation')->name('Labtransformation.detail');
        });
        Route::group(['middleware' => ['can:Ver Transformaciones']], function () {
            Route::get('nueva-transformacion-de-laboratorio', 'createLabTransformation')->name('LabTransformation.create');
            Route::post('nueva-transformacion-de-laboratorio', 'storeLabTransformation')->name('store.LabTransformation');
        });
    });

    Route::controller(InventoryLocationController::class)->group(function () {
        Route::get('/inventario inicial', 'start')->name('inventory.start');
        Route::post('/inventory/accept', 'accept')->name('inventory.accept');
        Route::group(['middleware' => ['role:Asesor comercial', 'inventory.check']], function () {
            Route::get('/inventario actual', 'current')->name('inventory.current');
        });
    });

});
