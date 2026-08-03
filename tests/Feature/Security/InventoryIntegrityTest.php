<?php

use App\Events\CreatePurchaseOrder;
use App\Models\DispatchDetail;
use App\Models\Product;
use App\Models\ProductEntry;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    Event::fake([CreatePurchaseOrder::class]);
});

test('crear un producto desde la interfaz no revienta por dependents', function () {
    [$sede, $bodega, $inventario] = crearInventarioDePrueba();
    $supplier = Supplier::first();

    $auxiliar = User::factory()->create();
    $auxiliar->assignRole('Auxiliar administrativo');

    $this->actingAs($auxiliar)->post('/productos/nuevo-producto', [
        'reference' => 'NUEVO',
        'measurement_unit' => 'und',
        'commercial_reference' => 'NUEVO',
        'category' => 'Dama',
        'supplier_id' => $supplier->supplier_id,
        'code' => 'N1',
    ])->assertRedirect();

    expect(Product::where('reference', 'NUEVO')->exists())->toBeTrue();
});

test('crear un despacho no revienta por returned_quantity ni observations', function () {
    [$sedeCentral, $bodegaCentral, $inventarioCentral] = crearInventarioDePrueba();
    [$sedeDestino, $bodegaDestino] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodegaCentral->warehouse_id)->update(['warehouse_id' => 2]);
    $inventarioCentral->warehouse_id = 2;
    $inventarioCentral->save();

    $jefe = User::factory()->create();
    $jefe->assignRole('Jefe de operaciones');

    $this->actingAs($jefe)->post('/despachos/nuevo-despacho', [
        'dispatches' => [[
            'warehouse' => $bodegaDestino->warehouse_id,
            'request_id' => null,
            'references' => [[
                'reference' => $inventarioCentral->inventory_id,
                'dispatched_quantity' => 4,
            ]],
        ]],
    ])->assertRedirect(route('dispatch.list'));

    expect(DispatchDetail::count())->toBe(1);
    expect((int) DispatchDetail::first()->returned_quantity)->toBe(0);
});

test('una orden de compra fallida no deja inventario incrementado', function () {
    [$sede, $bodega, $inventario] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodega->warehouse_id)->update(['warehouse_id' => 3]);
    $inventario->warehouse_id = 3;
    $inventario->save();

    $auxiliar = User::factory()->create();
    $auxiliar->assignRole('Auxiliar administrativo');

    try {
        $this->actingAs($auxiliar)->post('/ordenes-compra/nueva-orden', [
            'supplier' => 1,
            'supplier_order' => 'OC-1',
            'references' => [
                ['reference' => $inventario->product_id, 'quantity' => 100, 'unity' => 'UND', 'batch' => 'L1'],
                ['reference' => 999999, 'quantity' => 50, 'unity' => 'UND', 'batch' => 'L2'],
            ],
        ]);
    } catch (Throwable $e) {
        // el fallo de integridad es el escenario bajo prueba
    }

    expect((float) $inventario->fresh()->quantity)->toBe(100.0);
    expect(ProductEntry::count())->toBe(0);
    expect(PurchaseOrder::count())->toBe(0);
});

test('una orden de compra valida si persiste inventario y entradas', function () {
    [$sede, $bodega, $inventario] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodega->warehouse_id)->update(['warehouse_id' => 3]);
    $inventario->warehouse_id = 3;
    $inventario->save();

    $auxiliar = User::factory()->create();
    $auxiliar->assignRole('Auxiliar administrativo');

    $this->actingAs($auxiliar)->post('/ordenes-compra/nueva-orden', [
        'supplier' => 1,
        'supplier_order' => 'OC-2',
        'references' => [
            ['reference' => $inventario->product_id, 'quantity' => 25, 'unity' => 'UND', 'batch' => 'L1'],
        ],
    ])->assertRedirect();

    expect((float) $inventario->fresh()->quantity)->toBe(125.0);
    expect(ProductEntry::count())->toBe(1);
});

test('la transformacion de laboratorio rechaza cantidades negativas', function () {
    [$sede, $bodega, $inventario] = crearInventarioDePrueba();

    DB::table('warehouses')->where('warehouse_id', $bodega->warehouse_id)->update(['warehouse_id' => 1]);
    $inventario->warehouse_id = 1;
    $inventario->save();

    $laboratorio = User::factory()->create();
    $laboratorio->assignRole('Laboratorio');

    $this->actingAs($laboratorio)->post('/nueva-transformacion-de-laboratorio', [
        'reference' => $inventario->product_id,
        'escencia' => -5000,
        'dipropileno' => 0,
        'disolvente' => 0,
        'request' => 1,
        'status' => 'Finalizado',
    ])->assertSessionHasErrors('escencia');

    expect((float) $inventario->fresh()->quantity)->toBe(100.0);
});
