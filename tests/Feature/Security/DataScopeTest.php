<?php

use App\Models\CashRegister;
use App\Models\Consumable;
use App\Models\DamageReturnDetail;
use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function cajaAbierta(Location $location): CashRegister
{
    return CashRegister::create([
        'location_id' => $location->location_id,
        'total_collected' => 0,
        'total_digital' => 0,
        'count_100_bill' => 0,
        'count_50_bill' => 0,
        'count_20_bill' => 0,
        'count_10_bill' => 0,
        'count_5_bill' => 0,
        'count_2_bill' => 0,
        'total_coins' => 0,
    ]);
}

function asesorDe(Location $location): User
{
    $asesor = User::factory()->create();
    $asesor->assignRole('Asesor comercial');
    $asesor->location_user()->attach($location->location_id);
    habilitarJornada($asesor, $location);

    return $asesor;
}

function despachoEnRuta(Inventory $inventory, int $warehouseId, int $dispatchedQuantity): DispatchDetail
{
    $dispatch = new Dispatch;
    $dispatch->status = 'En ruta';
    $dispatch->save();

    return DispatchDetail::create([
        'dispatch_id' => $dispatch->dispatch_id,
        'warehouse_id' => $warehouseId,
        'inventory_id' => $inventory->inventory_id,
        'dispatched_quantity' => $dispatchedQuantity,
        'received' => 0,
    ]);
}

test('la recepcion no admite mas cantidad de la despachada', function () {
    [$sede, $bodega, $inventario] = crearInventarioDePrueba();
    $detalle = despachoEnRuta($inventario, $bodega->warehouse_id, 5);

    $this->actingAs(asesorDe($sede))->post('/despachos/recibir', [
        'products' => [[
            'dispatchs_detail_id' => $detalle->dispatchs_detail_id,
            'received' => true,
            'observation' => 'ok',
            'quantity' => 100000,
        ]],
    ]);

    expect((float) $inventario->fresh()->quantity)->toBe(105.0);
});

test('un asesor no puede recibir un despacho dirigido a otra sede', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB, $bodegaB, $inventarioB] = crearInventarioDePrueba();

    $detalleB = despachoEnRuta($inventarioB, $bodegaB->warehouse_id, 5);

    $this->actingAs(asesorDe($sedeA))->post('/despachos/recibir', [
        'products' => [[
            'dispatchs_detail_id' => $detalleB->dispatchs_detail_id,
            'received' => true,
            'observation' => 'ok',
            'quantity' => 5,
        ]],
    ])->assertSessionHasErrors('error');

    expect($detalleB->fresh()->dispatch->status)->toBe('En ruta');
    expect((float) $inventarioB->fresh()->quantity)->toBe(100.0);
});

test('la cantidad devuelta de un despacho no puede superar la despachada', function () {
    [$sedeCentral, $bodegaCentral, $inventarioCentral] = crearInventarioDePrueba();
    [$sedeDestino, $bodegaDestino] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodegaCentral->warehouse_id)->update(['warehouse_id' => 2]);
    $inventarioCentral->warehouse_id = 2;
    $inventarioCentral->save();

    $detalle = despachoEnRuta($inventarioCentral, $bodegaDestino->warehouse_id, 3);

    $jefe = User::factory()->create();
    $jefe->assignRole('Jefe de operaciones');

    $this->actingAs($jefe)
        ->put('/despachos/detalle-despacho-devolucion/'.$detalle->dispatch_id, [
            'details' => [[
                'id' => $detalle->dispatchs_detail_id,
                'returned_quantity' => 5000,
            ]],
        ])
        ->assertSessionHasErrors('error');

    expect((float) $inventarioCentral->fresh()->quantity)->toBe(100.0);
});

test('no se admiten detalles de otro despacho en la misma devolucion', function () {
    [$sedeCentral, $bodegaCentral, $inventarioCentral] = crearInventarioDePrueba();
    [$sedeDestino, $bodegaDestino] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodegaCentral->warehouse_id)->update(['warehouse_id' => 2]);
    $inventarioCentral->warehouse_id = 2;
    $inventarioCentral->save();

    $detalleUno = despachoEnRuta($inventarioCentral, $bodegaDestino->warehouse_id, 3);
    $detalleDos = despachoEnRuta($inventarioCentral, $bodegaDestino->warehouse_id, 3);

    $jefe = User::factory()->create();
    $jefe->assignRole('Jefe de operaciones');

    $this->actingAs($jefe)
        ->put('/despachos/detalle-despacho-devolucion/'.$detalleUno->dispatch_id, [
            'details' => [[
                'id' => $detalleDos->dispatchs_detail_id,
                'returned_quantity' => 1,
            ]],
        ])
        ->assertSessionHasErrors('error');

    expect((float) $inventarioCentral->fresh()->quantity)->toBe(100.0);
});

test('un asesor no puede descontar consumibles de la bodega de otra sede', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB, $bodegaB, $inventarioB] = crearInventarioDePrueba();

    $this->actingAs(asesorDe($sedeA))->post('/consumibles/nuevo-registro-consumible', [
        'consumable' => [[
            'warehouse_id' => $bodegaB->warehouse_id,
            'references' => [[
                'reference' => $inventarioB->inventory_id,
                'consumable_quantity' => 30,
            ]],
        ]],
    ]);

    expect((float) $inventarioB->fresh()->quantity)->toBe(100.0);
    expect(Consumable::count())->toBe(0);
});

test('un asesor no puede crear una devolucion sobre la bodega de otra sede', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB, $bodegaB, $inventarioB] = crearInventarioDePrueba();

    $this->actingAs(asesorDe($sedeA))->post('/devoluciones/nueva-devolucion', [
        'damageReturn' => [[
            'warehouse_id' => $bodegaB->warehouse_id,
            'references' => [[
                'reference' => $inventarioB->inventory_id,
                'damage_quantity' => 10,
                'observations' => 'daño',
            ]],
        ]],
    ]);

    expect(DamageReturnDetail::where('warehouse_id', $bodegaB->warehouse_id)->count())->toBe(0);
});

test('un supervisor no puede leer el arqueo de una sede ajena', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB] = crearInventarioDePrueba();

    cajaAbierta($sedeB);

    $supervisor = User::factory()->create();
    $supervisor->assignRole('Supervisor');
    $supervisor->location_user()->attach($sedeA->location_id);

    $this->actingAs($supervisor)
        ->get('/auditoria/caja/'.$sedeB->location_id)
        ->assertForbidden();
});

test('el arqueo solo incluye las ventas de su propia sede', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB] = crearInventarioDePrueba();

    $cajaA = cajaAbierta($sedeA);
    $cajaB = cajaAbierta($sedeB);
    $cajero = User::factory()->create();

    Sale::create([
        'cash_register_id' => $cajaA->cash_register_id,
        'location_id' => $sedeA->location_id,
        'total' => 99999,
        'user_id' => $cajero->user_id,
        'payment_method' => 'Efectivo',
        'transaction_code' => '',
    ]);

    Sale::create([
        'cash_register_id' => $cajaB->cash_register_id,
        'location_id' => $sedeB->location_id,
        'total' => 111,
        'user_id' => $cajero->user_id,
        'payment_method' => 'Efectivo',
        'transaction_code' => '',
    ]);

    $auditor = User::factory()->create();
    $auditor->assignRole('Supervisor');
    $auditor->location_user()->attach($sedeB->location_id);

    $response = $this->actingAs($auditor)->get('/auditoria/caja/'.$sedeB->location_id);
    $response->assertOk();

    $totales = collect($response->viewData('page')['props']['cashSales'])->pluck('total');

    expect($totales)->toContain(111);
    expect($totales)->not->toContain(99999);
});

test('un asesor no puede ver el detalle de una venta de otra sede', function () {
    [$sedeA] = crearInventarioDePrueba();
    [$sedeB] = crearInventarioDePrueba();

    $cajaB = cajaAbierta($sedeB);
    $vendedorB = User::factory()->create();

    $venta = Sale::create([
        'cash_register_id' => $cajaB->cash_register_id,
        'location_id' => $sedeB->location_id,
        'total' => 12345,
        'user_id' => $vendedorB->user_id,
        'payment_method' => 'Efectivo',
        'transaction_code' => '',
    ]);

    $this->actingAs(asesorDe($sedeA))
        ->get('/ventas/detalle/'.$venta->sale_id)
        ->assertNotFound();
});

test('un asesor si puede ver el detalle de una venta de su propia sede', function () {
    [$sedeA] = crearInventarioDePrueba();

    $caja = cajaAbierta($sedeA);
    $vendedor = User::factory()->create();

    $venta = Sale::create([
        'cash_register_id' => $caja->cash_register_id,
        'location_id' => $sedeA->location_id,
        'total' => 500,
        'user_id' => $vendedor->user_id,
        'payment_method' => 'Efectivo',
        'transaction_code' => '',
    ]);

    $this->actingAs(asesorDe($sedeA))
        ->get('/ventas/detalle/'.$venta->sale_id)
        ->assertOk();
});

test('no se elimina una sede con la caja del dia abierta', function () {
    [$sede] = crearInventarioDePrueba();
    $caja = cajaAbierta($sede);

    expect($caja->fresh()->confirmationclosingcash)->toBeNull();

    $admin = User::factory()->create();
    $admin->assignRole('Administrador');

    $this->actingAs($admin)->delete('/sedes/delete/'.$sede->location_id);

    expect(Location::withTrashed()->find($sede->location_id)->deleted_at)->toBeNull();
});
