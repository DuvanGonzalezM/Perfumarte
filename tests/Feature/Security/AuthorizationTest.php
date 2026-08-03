<?php

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

/**
 * Regresión del IDOR de escritura en stock.
 *
 * `PUT stock/update` vivía bajo `can:Ver Stock` —un permiso de LECTURA— y no
 * validaba la pertenencia de `warehouse_id`. Las referencias ausentes del
 * payload se borraban FÍSICAMENTE y arrastraban por cascada `sale_details`,
 * de modo que un asesor comercial podía vaciar el inventario de otra sede y
 * destruir su histórico de ventas con una sola petición.
 */
test('ver stock ya no autoriza a modificar el inventario', function () {
    $asesor = User::factory()->create();
    $asesor->assignRole('Asesor comercial');

    expect($asesor->can('Ver Stock'))->toBeFalse();
    expect($asesor->can('Editar Stock'))->toBeFalse();

    $this->actingAs($asesor)
        ->put('/stock/update', ['warehouse_id' => 1, 'products' => []])
        ->assertForbidden();
});

test('un payload vacío ya no borra físicamente el inventario', function () {
    [$location, $warehouse, $inventory] = crearInventarioDePrueba();

    $jefe = User::factory()->create();
    $jefe->assignRole('Jefe de operaciones');

    expect($jefe->can('Editar Stock'))->toBeTrue();

    $this->actingAs($jefe)
        ->put('/stock/update', ['warehouse_id' => $warehouse->warehouse_id, 'products' => []]);

    // La fila sigue existiendo: solo se pone a cero. Antes se borraba y la
    // cascada se llevaba por delante sale_details y otras seis tablas.
    $inventory->refresh();
    expect($inventory->exists)->toBeTrue();
    expect((float) $inventory->quantity)->toBe(0.0);
});

test('no se puede modificar el inventario de una bodega de otra sede', function () {
    [$locationPropia, $warehousePropia] = crearInventarioDePrueba();
    [$locationAjena, $warehouseAjena] = crearInventarioDePrueba();

    $supervisor = User::factory()->create();
    $supervisor->assignRole('Jefe de operaciones');
    $supervisor->location_user()->attach($locationPropia->location_id);

    $this->actingAs($supervisor)
        ->put('/stock/update', [
            'warehouse_id' => $warehouseAjena->warehouse_id,
            'products' => [],
        ])
        ->assertForbidden();
});

/**
 * Regresión de la escalada al rol TI.
 *
 * `detailUser()` ocultaba el rol TI a quien no lo tuviera, pero la escritura no
 * validaba nada: bastaba enviar el id del rol TI en el payload.
 */
test('un usuario que no es TI no puede asignarse el rol TI', function () {
    $administrador = User::factory()->create();
    $administrador->assignRole('Administrador');

    $victima = User::factory()->create();
    $rolTi = Role::where('name', 'TI')->firstOrFail();

    $this->actingAs($administrador)
        ->post('/users/'.$victima->user_id, [
            'username' => $victima->username,
            'name' => $victima->name,
            'roles' => [$rolTi->id],
        ])
        ->assertSessionHasErrors('roles');

    expect($victima->fresh()->hasRole('TI'))->toBeFalse();
});

test('un usuario TI sí puede asignar el rol TI', function () {
    $ti = User::factory()->create();
    $ti->assignRole('TI');

    $destino = User::factory()->create();
    $rolTi = Role::where('name', 'TI')->firstOrFail();

    $this->actingAs($ti)
        ->post('/users/'.$destino->user_id, [
            'username' => $destino->username,
            'name' => $destino->name,
            'roles' => [$rolTi->id],
        ])
        ->assertSessionHasNoErrors();

    expect($destino->fresh()->hasRole('TI'))->toBeTrue();
});

/**
 * Regresión de las rutas sin ningún control de autorización.
 */
test('el listado de usuarios ya no es accesible para cualquier autenticado', function () {
    $asesor = User::factory()->create();
    $asesor->assignRole('Asesor comercial');

    $this->actingAs($asesor)->get('/users')->assertForbidden();
});

test('los informes financieros ya no son descargables por cualquier autenticado', function () {
    $asesor = User::factory()->create();
    $asesor->assignRole('Asesor comercial');

    $this->actingAs($asesor)
        ->get('/reportes/generate/ventas/2026-01-01,2026-01-31/1')
        ->assertForbidden();
});

test('el cierre de caja exige permiso', function () {
    $laboratorio = User::factory()->create();
    $laboratorio->assignRole('Laboratorio');

    $this->actingAs($laboratorio)->get('/ver-caja')->assertForbidden();
});

/**
 * Habilita a un asesor comercial para operar en su sede.
 *
 * El middleware CheckInventoryAccess desvía a `inventory.start` a todo asesor
 * que no haya aceptado el inventario del día.
 */
function habilitarJornada(User $usuario, Location $location): void
{
    \App\Models\InventoryValidation::create([
        'user_id' => $usuario->user_id,
        'location_id' => $location->location_id,
        'date' => now()->toDateString(),
        'accepted_at' => now(),
    ]);
}

/**
 * Crea la cadena mínima sede → bodega → inventario para las pruebas.
 *
 * @return array{0: Location, 1: Warehouse, 2: Inventory}
 */
function crearInventarioDePrueba(): array
{
    $zoneId = DB::table('zones')->insertGetId([
        'zone_name' => 'Zona '.fake()->unique()->numberBetween(1, 99999),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $location = Location::create([
        'name' => 'Sede '.fake()->unique()->numberBetween(1, 99999),
        'address' => 'Calle 1',
        'zone_id' => $zoneId,
        'cash_base' => 0,
    ]);

    $warehouse = Warehouse::create([
        'location_id' => $location->location_id,
        'name' => 'Bodega principal',
        'price5' => 1000,
        'price30' => 5000,
        'price50' => 8000,
        'price100' => 15000,
        'price_drops' => 100,
    ]);

    $supplier = Supplier::create([
        'nit' => (string) fake()->unique()->numberBetween(100000, 999999),
        'name' => 'Proveedor',
        'country' => 'Colombia',
        'address' => 'Calle 2',
        'phone' => '3000000000',
        'email' => 'proveedor@example.com',
        'status' => true,
    ]);

    $product = Product::create([
        'reference' => 'REF-'.fake()->unique()->numberBetween(1, 99999),
        'name' => 'Perfume de prueba',
        'measurement_unit' => 'ml',
        'commercial_reference' => 'COM-1',
        'category' => 'Perfume',
        'code' => 'C-'.fake()->unique()->numberBetween(1, 99999),
        'supplier_id' => $supplier->supplier_id,
        'status' => true,
        'dependents' => '',
    ]);

    $inventory = Inventory::create([
        'warehouse_id' => $warehouse->warehouse_id,
        'product_id' => $product->product_id,
        'quantity' => 100,
    ]);

    return [$location, $warehouse, $inventory];
}
