<?php

use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('el total de la venta lo fija el servidor, no el navegador', function () {
    [$location, $warehouse, $inventory] = crearInventarioDePrueba();

    $cajero = User::factory()->create();
    $cajero->assignRole('Asesor comercial');
    $cajero->location_user()->attach($location->location_id);
    habilitarJornada($cajero, $location);

    $caja = CashRegister::create([
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
        'confirmationclosingcash' => false,
    ]);

    $totalRealEsperado = 10000;

    $this->actingAs($cajero)->post('/ventas/nueva-venta', [
        'assessor' => $cajero->user_id,
        'pay_method' => 'Efectivo',
        'total' => 1000,   // <- importe falsificado
        'references' => [[
            'reference' => $inventory->inventory_id,
            'quantity' => 30,
            'units' => 2,
            'container' => null,
            'perdurable' => [],
        ]],
    ]);

    $venta = Sale::latest('sale_id')->first();

    expect($venta)->not->toBeNull();
    expect((int) $venta->total)->toBe($totalRealEsperado);
    expect((int) $caja->fresh()->total_collected)->toBe($totalRealEsperado);
});

test('la venta queda imputada a la sede del cajero', function () {
    [$location, $warehouse, $inventory] = crearInventarioDePrueba();

    $cajero = User::factory()->create();
    $cajero->assignRole('Asesor comercial');
    $cajero->location_user()->attach($location->location_id);
    habilitarJornada($cajero, $location);

    CashRegister::create([
        'location_id' => $location->location_id,
        'total_collected' => 0, 'total_digital' => 0,
        'count_100_bill' => 0, 'count_50_bill' => 0, 'count_20_bill' => 0,
        'count_10_bill' => 0, 'count_5_bill' => 0, 'count_2_bill' => 0,
        'total_coins' => 0, 'confirmationclosingcash' => false,
    ]);

    $this->actingAs($cajero)->post('/ventas/nueva-venta', [
        'assessor' => $cajero->user_id,
        'pay_method' => 'Efectivo',
        'references' => [[
            'reference' => $inventory->inventory_id,
            'quantity' => 30,
            'units' => 1,
            'container' => null,
            'perdurable' => [],
        ]],
    ]);

    $venta = Sale::latest('sale_id')->first();

    expect($venta)->not->toBeNull();
    expect((int) $venta->location_id)->toBe((int) $location->location_id);
});

test('no se admite un medio de pago fuera de la lista blanca', function () {
    [$location, $warehouse, $inventory] = crearInventarioDePrueba();

    $cajero = User::factory()->create();
    $cajero->assignRole('Asesor comercial');
    $cajero->location_user()->attach($location->location_id);
    habilitarJornada($cajero, $location);

    $this->actingAs($cajero)
        ->post('/ventas/nueva-venta', [
            'assessor' => $cajero->user_id,
            'pay_method' => 'Trueque',
            'references' => [[
                'reference' => $inventory->inventory_id,
                'quantity' => 30,
                'units' => 1,
                'container' => null,
                'perdurable' => [],
            ]],
        ])
        ->assertSessionHasErrors('pay_method');
});

test('el stock fraccionado ya no se trunca al descontarse', function () {
    // en cada venta.
    [$location, $warehouse, $inventory] = crearInventarioDePrueba();

    $cajero = User::factory()->create();
    $cajero->assignRole('Asesor comercial');
    $cajero->location_user()->attach($location->location_id);
    habilitarJornada($cajero, $location);

    CashRegister::create([
        'location_id' => $location->location_id,
        'total_collected' => 0, 'total_digital' => 0,
        'count_100_bill' => 0, 'count_50_bill' => 0, 'count_20_bill' => 0,
        'count_10_bill' => 0, 'count_5_bill' => 0, 'count_2_bill' => 0,
        'total_coins' => 0, 'confirmationclosingcash' => false,
    ]);

    $this->actingAs($cajero)->post('/ventas/nueva-venta', [
        'assessor' => $cajero->user_id,
        'pay_method' => 'Efectivo',
        'references' => [[
            'reference' => $inventory->inventory_id,
            'quantity' => 5,
            'units' => 1,
            'container' => null,
            'perdurable' => [],
        ]],
    ]);

    expect((float) $inventory->fresh()->quantity)->toBe(97.5);
});
