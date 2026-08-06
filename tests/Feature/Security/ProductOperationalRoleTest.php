<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\CoreCatalogSeeder;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(CoreCatalogSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('TI');
});

test('el catálogo base deja marcados los productos que la operación necesita', function () {
    expect(Product::findByRole('dipropylene'))->not->toBeNull()
        ->and(Product::findByRole('solvent'))->not->toBeNull()
        ->and(Product::findByRole('gift_bag'))->not->toBeNull();

    expect(Product::operationalRole('container_30')->count())->toBeGreaterThan(0)
        ->and(Product::operationalRole('container_50')->count())->toBeGreaterThan(0)
        ->and(Product::operationalRole('container_100')->count())->toBeGreaterThan(0);
});

test('la bolsa de regalo se encuentra por rol y no por su id ni por su texto', function () {
    $bolsa = Product::findByRole('gift_bag');

    $bolsa->update(['product_id' => $bolsa->product_id, 'reference' => 'Empaque promocional']);

    expect(Product::findByRole('gift_bag')?->product_id)->toBe($bolsa->product_id);
});

test('dos productos no pueden compartir un rol exclusivo', function () {
    $this->actingAs($this->admin)
        ->post('/productos/nuevo-producto', [
            'reference' => 'Otro disolvente',
            'measurement_unit' => 'KG',
            'commercial_reference' => 'Otro disolvente',
            'category' => 'Insumo',
            'supplier_id' => 1,
            'code' => 'DUP-DISOL',
            'operational_role' => 'solvent',
        ])
        ->assertSessionHasErrors('operational_role');

    expect(Product::operationalRole('solvent')->count())->toBe(1);
});

test('varios productos si pueden ser envases del mismo tamaño', function () {
    $antes = Product::operationalRole('container_50')->count();

    $this->actingAs($this->admin)
        ->post('/productos/nuevo-producto', [
            'reference' => 'Envase 50 ml E',
            'measurement_unit' => 'UNIDAD',
            'commercial_reference' => 'Envase 50 ml E',
            'category' => 'Insumo',
            'supplier_id' => 1,
            'code' => 'ENV-050-E',
            'operational_role' => 'container_50',
        ])
        ->assertSessionHasNoErrors();

    expect(Product::operationalRole('container_50')->count())->toBe($antes + 1);
});

test('un rol operativo inventado se rechaza', function () {
    $this->actingAs($this->admin)
        ->post('/productos/nuevo-producto', [
            'reference' => 'Producto raro',
            'measurement_unit' => 'UNIDAD',
            'commercial_reference' => 'Producto raro',
            'category' => 'Insumo',
            'supplier_id' => 1,
            'code' => 'RARO-1',
            'operational_role' => 'lo-que-sea',
        ])
        ->assertSessionHasErrors('operational_role');
});

test('un producto normal se crea sin rol operativo', function () {
    $this->actingAs($this->admin)
        ->post('/productos/nuevo-producto', [
            'reference' => 'Perfume de prueba',
            'measurement_unit' => 'UNIDAD',
            'commercial_reference' => 'Perfume de prueba',
            'category' => 'Dama',
            'supplier_id' => 1,
            'code' => 'PERF-1',
        ])
        ->assertSessionHasNoErrors();

    expect(Product::where('code', 'PERF-1')->first()?->operational_role)->toBeNull();
});
