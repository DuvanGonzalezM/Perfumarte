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
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\NoveltyController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\DamageReturnController;
use App\Http\Controllers\ConsumableController;

Route::middleware('auth')->group(function () {
    Route::get('change-password', [PasswordController::class, 'show'])->name('change-password');
    Route::post('change-password', [PasswordController::class, 'update'])->name('change-password.update');
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
            Route::get('despachos', 'getAllDispatch')->name('dispatch.list');
            Route::get('detalle-despachos/{dispatchId}', 'detailDispatch')->name('dispatch.detail');
            Route::get('despachos/detalle despacho-devolucion/{dispatch_id}', 'getReturnedDispatch')->name('dispatchReturn.list');

        });
        Route::group(['middleware' => ['can:Crear Despachos']], function () {
            Route::get('despachos/nuevo-despacho', 'createDispatch')->name('dispatch.create');
            Route::post('despachos/nuevo-despacho', 'storeDispatch')->name('dispatch.store');
        });
        Route::group(['middleware' => ['can:Editar Despachos']], function () {
            Route::get('despachos/editar-despacho/{dispatch_id}', 'editDispatch')->name('dispatch.edit');
            Route::put('despachos/editar-despacho/{dispatch_id}', 'updateDispatch')->name('dispatch.update');
            Route::put('despachos/detalle-despacho-devolucion/{dispatch_id}', 'storeReturnedQuantities')->name('dispatchReturn.store');

        });

        Route::group(['middleware' => ['can:Aprobar Despachos']], function () {
            Route::put('detalle-despachos/{dispatchId}', 'approvedDispatch')->name('dispatch.approved');
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
            Route::get('solicitudes-insumos/nueva-solicitud', 'createRequst')->name('suppliesrequest.create');
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
            Route::put('stock/update', 'updateInventory')->name('stock.inventory.update');
        });
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'getUsers')->name('users.list');
        Route::post('users', 'storeUser')->name('users.store');
        Route::get('users/{user_id}', 'detailUser')->name('users.detail');
        Route::post('users/{user_id}', 'updateUserRolePermission')->name('users.role_permi')->middleware('can:Editar Usuarios');
        Route::post('users/{user_id}/reset-password', 'resetPassword')->name('users.reset-password');
        Route::get('api/permissions_roles/{roles_id}', 'getPermissionRol')->name('api.permi_roles');
        Route::get('permissions', 'getPermissions')->name('permissions.list');
        Route::post('permissions', 'storePermission')->name('permissions.store');
        Route::put('permissions', 'updatePermission')->name('permissions.update');
        Route::get('roles', 'getRoles')->name('roles.list');
        Route::post('roles', 'storeRole')->name('roles.store');
        Route::put('roles', 'updateRole')->name('roles.update');
        Route::put('users/edit/{user_id}', 'editUser')->name('users.edit');
        Route::delete('users/delete/{user_id}', 'destroyUser')->name('users.destroy');
    });

    Route::controller(LocationsController::class)->group(function () {
        Route::get('sedes', 'getLocations')->name('locations.list');
        Route::post('sedes', 'storeLocation')->name('locations.store');
        Route::get('sedes/detalle/{location_id}', 'detailLocation')->name('locations.detail');
        Route::put('sedes/update/{location_id}', 'updateLocation')->name('locations.update');
        Route::delete('sedes/delete/{location_id}', 'destroyLocation')->name('locations.destroy');
        Route::get('sedes/inventario/{location_id}', 'inventorylocation')->name('locations.inventory');
        Route::get('sedes/personal/{location_id}', 'personallocation')->name('locations.personal');
        Route::get('sedes/ventas/{location_id}', 'saleslocation')->name('locations.sales');
        Route::get('sedes/ventas/{location_id}/detalle/{cash_register_id}', 'salesDetail')->name('locations.salesDetail');
        Route::get('sedes/auditoria/{location_id}', 'auditlocation')->name('locations.audit');


    });


    Route::controller(RepackageController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Reenvases']], function () {
            Route::get('reenvase', 'getrepackage')->name('repackage.list');
        });
        Route::group(['middleware' => ['can:Crear Reenvases']], function () {
            Route::get('reenvase/nuevo-reenvase', 'createRepackage')->name('create.repackage');
            Route::post('reenvase/nuevo-reenvase', 'storeRepackage')->name('store.repackage');
        });
        Route::group(['middleware' => ['can:Editar Reenvases']], function () {
            Route::get('reenvase/editar/{repackage_id}', 'editRepackage')->name('edit.repackage');
            Route::put('reenvase/editar/{repackage_id}', 'updateRepackage')->name('update.repackage');
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
        Route::group(['middleware' => ['can:Editar Transformaciones']], function () {
            Route::get('transformaciones-de-laboratorio/{transformationId}/editar', 'editTransformation')->name('LabTransformation.edit');
            Route::put('transformaciones-de-laboratorio/{transformationId}/actualizar', 'updateTransformation')->name('LabTransformation.update');
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
        Route::get('inventario inicial', function () {
            return redirect()->route('inventory.start');
        });
        Route::get('inventario actual', function () {
            return redirect()->route('inventory.current');
        });
        Route::get('inventario-inicial', 'start')->name('inventory.start');
        Route::post('inventory/accept', 'accept')->name('inventory.accept');
        Route::group(['middleware' => ['role:Asesor comercial', 'inventory.check']], function () {
            Route::get('inventario-actual', 'current')->name('inventory.current');
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
                Route::get('ventas/nueva-venta', 'createSales')->name('sales.create');
                Route::post('ventas/nueva-venta', 'storeSales')->name('sales.store');
                Route::get('ventas/nueva-venta/{precio}/{pago}', 'test')->name('sales.validate');
            });
        });
    });
    Route::controller(AuditController::class)->group(function () {
        Route::group(['middleware' => ['can:Auditar']], function () {
            Route::get('auditorias', 'showAudits')->name('audits');
            Route::get('auditoria/inventario', 'getAllProducts')->name('audit.inventory');
            Route::get('auditoria/caja/{locationId}', 'getCashAuditByLocation')->name('audit.cash');
            Route::get('auditorias/detalle-auditoria-inventario/{id}', 'auditInventoryDetail')->name('detailInventory');
            Route::get('auditorias/detalle-auditoria-caja/{id_audits}', 'showDetailAuditCash')->name('detailCash');
            Route::post('auditoria/caja/{locationId}', 'confirmCashAudit')->name('audit.cash.confirm');
            Route::post('auditoria/inventario', 'storeAuditInventory')->name('audit.storeInventory');
        });
    });
    Route::controller(ProductController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Productos']], function () {
            Route::get('productos', 'getAllProducts')->name('products.list');
        });
        Route::group(['middleware' => ['can:Crear Productos']], function () {
            Route::get('productos/nuevo-producto', 'createProduct')->name('product.create');
            Route::post('productos/nuevo-producto', 'storeProduct')->name('product.store');
        });
        Route::group(['middleware' => ['can:Editar Productos']], function () {
            Route::put('productos/editar-producto/{product_id}', 'editProduct')->name('products.update');
        });
        Route::group(['middleware' => ['can:Desactivar Productos']], function () {
            Route::put('/productos/{product_id}', 'disableProduct')->name('products.disable');
        });
    });
    Route::controller(ReportController::class)->group(function () {
        Route::get('reportes', 'getReports')->name('reports');
        Route::get('reportes/generate/{typeReport}/{range_date}/{warehouseIds}', 'generateReport')->name('generate.report');
    });
    Route::controller(SupplierController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Proveedores']], function () {
            Route::get('proveedores', 'getAllSuppliers')->name('suppliers.list');
        });
        Route::group(['middleware' => ['can:Crear Proveedores']], function () {
            Route::get('proveedores/nuevo-proveedor', 'createSupplier')->name('supplier.create');
            Route::post('proveedores/nuevo-proveedor', 'storeSupplier')->name('supplier.store');
        });
        Route::group(['middleware' => ['can:Editar Proveedores']], function () {
            Route::put('proveedores/editar-proveedor/{supplier_id}', 'editSupplier')->name('supplier.update');
        });
        Route::group(['middleware' => ['can:Desactivar Proveedores']], function () {
            Route::put('/proveedores/{supplier_id}', 'disableSupplier')->name('supplier.disable');
        });
    });
    Route::controller(NoveltyController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Novedades']], function () {
            Route::get('novedades', 'getAllNovelties')->name('novelties.list');
        });
        Route::group(['middleware' => ['can:Crear Novedades']], function () {
            Route::get('novedades/nueva-novedad', 'createNovelty')->name('novelty.create');
            Route::post('novedades/nueva-novedad', 'storeNovelty')->name('novelty.store');
        });
    });
    Route::controller(NotificationController::class)->group(function () {
        Route::post('notificaciones/{notification_id}', 'readNotification')->name('notifications.read');
        Route::post('notificaciones', 'readAllNotification')->name('notifications.readAll');
    });
    Route::controller(CashRegisterController::class)->group(function () {
        Route::get('ver-caja', 'closeCashRegister')->name('cash_register.close');
        Route::post('cerrar-caja', 'store')->name('cash.close');
    });

    Route::controller(DamageReturnController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Devoluciones']], function () {
            Route::get('devoluciones', 'getAllDamageReturn')->name('damageReturn.list');
            Route::get('detalle-devolucion-de-averias/{id}', 'editDamageReturn')->name('damageReturn.detail');

        });
        Route::group(['middleware' => ['can:Crear Devoluciones']], function () {
            Route::get('devoluciones/nueva-devolucion', 'createDamageReturn')->name('damageReturn.create');
            Route::post('devoluciones/nueva-devolucion', 'storeDamageReturn')->name('damageReturn.store');
        });

        Route::group(['middleware' => ['can:Confirmar Devoluciones']], function () {
            Route::post('detalle-devolucion-de-averias/{id}', 'approvedDamageReturn')->name('damageReturn.approved');
        });

        Route::group(['middleware' => ['can:Aprobar Devoluciones']], function () {
            Route::put('aprobar-devolucion-de-averias/{id}', 'approveReturnFinal')->name('returnFinal.approved');
        });
    });

    Route::controller(ConsumableController::class)->group(function () {
        Route::group(['middleware' => ['can:Ver Consumibles']], function () {
            Route::get('consumibles', 'getAllConsumable')->name('consumable.list');

        });
        Route::group(['middleware' => ['can:Crear Consumibles']], function () {
            Route::get('consumibles/nuevo-registro-consumible', 'createConsumable')->name('consumable.create');
            Route::post('consumibles/nuevo-registro-consumible', 'storeConsumable')->name('consumable.store');
        });

        Route::group(['middleware' => ['can:Confirmar Devoluciones']], function () {
            Route::post('detalle-devolucion-de-consumibles/{id}', 'approvedConsumableReturn')->name('consumableReturn.approved');
        });

        Route::group(['middleware' => ['can:Aprobar Devoluciones']], function () {
            Route::put('aprobar-devolucion-de-consumibles/{id}', 'approveReturnFinal')->name('consumableReturnFinal.approved');
        });
    });


});
