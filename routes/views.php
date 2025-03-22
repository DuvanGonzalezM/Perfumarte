<?php

use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabTransformationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RepackageController;
use App\Http\Controllers\RequestPraisController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplyReceptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\InventoryLocationController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'getDataInventory'])->name('dashboard');
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
            Route::get('solicitudes-insumos', 'getAllRequest')->name('suppliesrequest.list');
            Route::get('solicitudes-insumos/solicitud/{requestId}', 'detailRequest')->name('suppliesrequest.detail');
        });
        Route::group(['middleware' => ['can:Editar Solicitudes Insumos']], function () {
            Route::get('solicitudes-insumos/{requestId}/editar', 'showDetail')->name('requests.detail');
            Route::put('solicitudes-insumos/{requestId}/actualizar', 'update')->name('suppliesrequest.update');
        });
        Route::group(['middleware' => ['can:Crear Solicitudes Insumos']], function () {
            Route::get('solicitudes-insumos/nueva-solicitud', 'createRequst')->name('suppliesrequest.store');
            Route::post('solicitudes-insumos/nueva-solicitud', 'storeRequest')->name('suppliesrequest.store');
        });
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
    Route::controller(AssignmentController::class)->group(function () {
        Route::group(['middleware' => ['can:Asignar Supervisor']], function () {
            Route::get('asignacion-supervisores', 'getAllSupervisor')->name('assignment.supervisor');
            Route::put('asignacion-supervisores', 'updateAssignment')->name('assignment.update');
        });
        Route::group(['middleware' => ['can:Asignar Personal']], function () {
            Route::get('asignar-asesores', 'getAllLocation')->name('list.location');
            Route::get('asignar-asesores/{location_id}', 'getAllAdvisor')->name('assignment.Advisor');
            Route::post('asignar-asesores', 'storeAdvisor')->name('store.Advisor');
        });
    });
    Route::controller(InventoryLocationController::class)->group(function () {
        Route::get('inventario inicial', 'start')->name('inventory.start');
        Route::post('inventory/accept', 'accept')->name('inventory.accept');
        Route::group(['middleware' => ['role:Asesor comercial', 'inventory.check']], function () {
            Route::get('inventario actual', 'current')->name('inventory.current');
        });
    });
    Route::group(['middleware' => ['inventory.check']], function () {
        Route::controller(SupplyReceptionController::class)->group(function () {
            Route::group(['middleware' => ['can:Recibir Insumos']], function () {
                Route::get('despachos/recibir', 'show')->name('dispatch.show');
                Route::post('despachos/recibir', 'receive')->name('dispatch.receive');
            });
        });
        Route::controller(SaleController::class)->group(function () {
            Route::group(['middleware' => ['can:Ver Ventas']], routes: function () {
                Route::get('ventas', 'sales')->name('sales.list');
                Route::get('ventas/detalle/{sale_id}', 'salesDetail')->name('sales.detail');
            });
            Route::group(['middleware' => ['can:Crear Ventas']], routes: function () {
                Route::get('ventas/nueva venta', 'createSales')->name('sales.create');
                Route::post('ventas/nueva venta', 'storeSales')->name('sales.store');
                Route::get('ventas/nueva venta/{precio}/{pago}', 'test')->name('sales.validate');
            });
        });
    });
    Route::controller(AuditController::class)->group(function () {
        Route::group(['middleware' => ['can:Auditar']], function () {
            Route::get('auditorias', 'showAudits')->name('audits');
            Route::get('auditoria/inventario', 'getAllProducts')->name('audit.inventory');
            Route::get('auditoria/caja/{locationId}', 'getCashAuditByLocation')->name('audit.cash');
            Route::get('auditorias/detalle auditoria inventario/{id}', 'auditInventoryDetail')->name('detailInventory');
            Route::get('auditorias/detalle auditoria caja/{id_audits}', 'showDetailAuditCash')->name('detailCash');
            Route::post('auditoria/caja/{locationId}', 'confirmCashAudit')->name('audit.cash.confirm');
            Route::post('auditoria/inventario', 'storeAuditInventory')->name('audit.storeInventory');
        });
    });
    Route::controller(ReportController::class)->group(function () {
        Route::get('reportes', 'getReports')->name('reports');
        Route::post('reportes', 'storeReports')->name('store.report');
        Route::get('reportes/download', 'downloadFile')->name('download.report');
    });
});