<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function sales()
    {
        $userLocation = auth()->user()->location_user->first()?->location_id;

        if (! $userLocation) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede asignada.');
        }

        $sales = Sale::with('user')
            ->whereHas('cashRegister', function ($query) use ($userLocation) {
                $query->where('location_id', $userLocation)
                    ->whereDate('created_at', date('Y-m-d'));
            })
            ->get();

        $cashRegister = CashRegister::where('location_id', $userLocation)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        return Inertia::render('Sale/SalesList', [
            'sales' => $sales,
            'confirmationclosingcash' => $cashRegister?->confirmationclosingcash
        ]);
    }

    public function createSales()
    {
        $userLocation = auth()->user()->location_user->first();

        if (! $userLocation) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede asignada.');
        }

        $assessors = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial')->orWhere('name', 'Usuario');
        })
            ->whereHas('location_user', function ($query) use ($userLocation) {
                $query->where('location_user.location_id', '=', $userLocation->location_id);
            })
            ->get();
        $warehouses = $userLocation->warehouses;
        $warehouse = [];
        $inventory = null;
        if (count($warehouses) > 0) {
            $warehouse = $warehouses[0];
            $inventory = Inventory::with('product')->where('warehouse_id', $warehouse->warehouse_id)->get();
        }

        return Inertia::render('Sale/CreateSale', [
            'assessors' => $assessors,
            'inventory' => $inventory,
            'warehouse' => $warehouse,
            'containersBySize' => config('prais.product_roles.containers_by_size'),
            'giftBagProductId' => Product::findByRole('gift_bag')?->product_id,
        ]);
    }

    public function priceReference(  $quantity, $warehouse, $totalUnits = 0, $allReferences = [])
    {
        $totalUnitsBySize = [
            5 => 0,
            30 => 0,
            50 => 0,
            100 => 0
        ];

        foreach ($allReferences as $ref) {
            if (isset($ref['quantity']) && isset($ref['units']) && isset($totalUnitsBySize[$ref['quantity']])) {
                $totalUnitsBySize[$ref['quantity']] += $ref['units'];
            }
        }

        switch ($quantity) {
            case 5:
                return $warehouse->price5;
              
            case 30:
                return ($totalUnitsBySize[30] ?? 0) >= 12
                    ? $warehouse->price30 - 1000
                    : $warehouse->price30;

            case 50:
                return ($totalUnitsBySize[50] ?? 0) >= 12
                    ? $warehouse->price50 - 2000
                    : $warehouse->price50;

            case 100:
                return ($totalUnitsBySize[100] ?? 0) >= 12
                    ? $warehouse->price100 - 2000
                    : $warehouse->price100;

            default:
                return 0;
        }
    }

    private const PAYMENT_METHODS = ['Efectivo', 'Transferencia', 'Tarjeta'];

    public function storeSales(Request $request)
    {
        $userLocation = auth()->user()->location_user->first();

        if (! $userLocation) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede asignada.');
        }

        $validated = $request->validate([
            'assessor' => 'required|integer|exists:users,user_id',
            'pay_method' => 'required|string|in:' . implode(',', self::PAYMENT_METHODS),
            'transaction_code' => 'nullable|string|max:255',
            'references' => 'required|array|min:1',
            'references.*.reference' => 'required|integer|exists:inventories,inventory_id',
            'references.*.quantity' => 'required|integer|min:1',
            'references.*.units' => 'required|integer|min:1',
            'references.*.container' => 'nullable|integer|exists:products,product_id',
            'references.*.perdurable' => 'present|array',
            'references.*.perdurable.*' => 'numeric|min:0',
            'count_100_bill' => 'nullable|integer',
            'count_50_bill' => 'nullable|integer',
            'count_20_bill' => 'nullable|integer',
            'count_10_bill' => 'nullable|integer',
            'count_5_bill' => 'nullable|integer',
            'count_2_bill' => 'nullable|integer',
            'total_coins' => 'nullable|integer',
            'rest_count_100_bill' => 'nullable|integer',
            'rest_count_50_bill' => 'nullable|integer',
            'rest_count_20_bill' => 'nullable|integer',
            'rest_count_10_bill' => 'nullable|integer',
            'rest_count_5_bill' => 'nullable|integer',
            'rest_total_coins' => 'nullable|integer',
        ]);

        $assessorBelongsToLocation = User::where('user_id', $validated['assessor'])
            ->whereHas('location_user', fn ($q) => $q->where('location_user.location_id', $userLocation->location_id))
            ->exists();

        if (! $assessorBelongsToLocation) {
            return back()->withErrors(['assessor' => 'El asesor seleccionado no pertenece a su sede.']);
        }

        try {
            return DB::transaction(function () use ($request, $validated, $userLocation) {
                $cashRegister = CashRegister::where('location_id', $userLocation->location_id)
                    ->whereDate('created_at', date('Y-m-d'))
                    ->lockForUpdate()
                    ->first();

                if (! $cashRegister) {
                    throw new \Exception('No hay una caja abierta hoy para esta sede.');
                }

                if ($cashRegister->confirmationclosingcash) {
                    throw new \Exception('La caja del día ya fue cerrada.');
                }

                $warehouse = $userLocation->warehouses->first();

                if (! $warehouse) {
                    throw new \Exception('Su sede no tiene una bodega asociada.');
                }

                $sale = Sale::create([
                    'cash_register_id' => $cashRegister->cash_register_id,
                    'location_id' => $userLocation->location_id,
                    'total' => 0,
                    'user_id' => $validated['assessor'],
                    'payment_method' => $validated['pay_method'],
                    'transaction_code' => $validated['pay_method'] == 'Transferencia'
                        ? ($validated['transaction_code'] ?? '')
                        : '',
                ]);

                $giftBagId = Inventory::with('product')
                    ->whereHas('product', function ($query) {
                        $query->operationalRole('gift_bag');
                    })
                    ->where('warehouse_id', $warehouse->warehouse_id)
                    ->first()?->inventory_id;

                $disolventeInventory = Inventory::where('warehouse_id', $warehouse->warehouse_id)
                    ->whereHas('product', function ($query) {
                        $query->operationalRole('solvent');
                    })
                    ->lockForUpdate()
                    ->first();

                $references = $validated['references'];
                $referenceIds = collect($references)->pluck('reference')->all();
                $containerProductIds = collect($references)->pluck('container')->filter()->values()->all();

                $inventoryMap = Inventory::where('warehouse_id', $warehouse->warehouse_id)
                    ->whereIn('inventory_id', $referenceIds)
                    ->lockForUpdate()
                    ->get()->keyBy('inventory_id');

                $containerInventoryMap = collect();
                $dependentsInventoryMap = collect();

                if (!empty($containerProductIds)) {
                    $containerInventoryMap = Inventory::with('product')
                        ->where('warehouse_id', $warehouse->warehouse_id)
                        ->whereIn('product_id', $containerProductIds)
                        ->lockForUpdate()
                        ->get()->keyBy('product_id');

                    $dependentCodes = $containerInventoryMap
                        ->filter(fn($item) => $item->product->dependents)
                        ->flatMap(fn($item) => explode(',', $item->product->dependents))
                        ->unique()->values()->all();

                    if (!empty($dependentCodes)) {
                        $dependentsInventoryMap = Inventory::with('product')
                            ->where('warehouse_id', $warehouse->warehouse_id)
                            ->whereHas('product', fn($q) => $q->whereIn('code', $dependentCodes))
                            ->lockForUpdate()
                            ->get()->keyBy(fn($item) => $item->product->code);
                    }
                }

                $serverTotal = 0;

                foreach ($references as $reference) {

                    $drops = 0;
                    array_map(function ($i) use (&$drops) {
                        $drops += $i;
                    }, $reference['perdurable']);

                    $price = $drops * $warehouse->price_drops;

                    $unitPrice = ($giftBagId !== null && $reference['reference'] == $giftBagId)
                        ? 3000
                        : $this->priceReference(
                            $reference['quantity'],
                            $warehouse,
                            $reference['units'],
                            $references
                        );

                    $totalPrice = ($unitPrice * $reference['units']) + $price;
                    $serverTotal += $totalPrice;

                    SaleDetail::create([
                        'inventory_id' => $reference['reference'],
                        'sale_id' => $sale->sale_id,
                        'quantity' => $reference['quantity'],
                        'units' => $reference['units'],
                        'drops' => $drops,
                        'price' => $totalPrice,
                    ]);

                    if ($giftBagId !== null && $reference['reference'] == $giftBagId) {
                        $quantityToSubtract = $reference['units'];
                    } else {
                        $quantityToSubtract = ($reference['quantity'] * $reference['units']) * 0.5;
                    }

                    $inventory = $inventoryMap->get($reference['reference']);

                    $containerInventory = null;
                    $dependentsInventories = [];

                    if ($reference['container']) {
                        $containerInventory = $containerInventoryMap->get($reference['container']);

                        if ($containerInventory && $containerInventory->product->dependents) {
                            foreach (explode(',', $containerInventory->product->dependents) as $dependent) {
                                $dep = $dependentsInventoryMap->get($dependent);
                                if ($dep) {
                                    $dependentsInventories[] = $dep;
                                }
                            }
                        }
                    }

                    if (!$inventory || $inventory->quantity < $quantityToSubtract) {
                        throw new \Exception('Stock insuficiente del producto seleccionado.');
                    }

                    if ($disolventeInventory && $disolventeInventory->quantity < $quantityToSubtract) {
                        throw new \Exception('Stock insuficiente de disolvente.');
                    }

                    if ($containerInventory && $containerInventory->quantity < $reference['units']) {
                        throw new \Exception('Stock insuficiente de envases.');
                    }

                    foreach ($dependentsInventories as $dependentInventory) {
                        if ($dependentInventory->quantity < $reference['units']) {
                            throw new \Exception("Stock insuficiente de {$dependentInventory->product->name}.");
                        }
                    }

                    $inventory->quantity -= $quantityToSubtract;
                    $inventory->save();

                    if ($disolventeInventory) {
                        $disolventeInventory->quantity -= $quantityToSubtract;
                        $disolventeInventory->save();
                    }

                    if ($containerInventory) {
                        $containerInventory->quantity -= $reference['units'];
                        $containerInventory->save();
                    }

                    foreach ($dependentsInventories as $dependentInventory) {
                        $dependentInventory->quantity -= $reference['units'];
                        $dependentInventory->save();
                    }
                }

                if ($request->has('total') && (int) $request->total !== (int) $serverTotal) {
                    Log::warning('Discrepancia entre el total enviado y el calculado en el servidor', [
                        'sale_id' => $sale->sale_id,
                        'user_id' => auth()->id(),
                        'location_id' => $userLocation->location_id,
                        'client_total' => $request->total,
                        'server_total' => $serverTotal,
                    ]);
                }

                $sale->total = $serverTotal;
                $sale->save();

                $cashRegister->total_collected += $serverTotal;

                if ($validated['pay_method'] == 'Transferencia') {
                    $cashRegister->total_digital += $serverTotal;
                }

                $cashRegister->count_100_bill += (int) ($validated['count_100_bill'] ?? 0);
                $cashRegister->count_50_bill += (int) ($validated['count_50_bill'] ?? 0);
                $cashRegister->count_20_bill += (int) ($validated['count_20_bill'] ?? 0);
                $cashRegister->count_10_bill += (int) ($validated['count_10_bill'] ?? 0);
                $cashRegister->count_5_bill += (int) ($validated['count_5_bill'] ?? 0);
                $cashRegister->count_2_bill += (int) ($validated['count_2_bill'] ?? 0);
                $cashRegister->total_coins += (int) ($validated['total_coins'] ?? 0);

                $cashRegister->count_100_bill -= (int) ($validated['rest_count_100_bill'] ?? 0);
                $cashRegister->count_50_bill -= (int) ($validated['rest_count_50_bill'] ?? 0);
                $cashRegister->count_20_bill -= (int) ($validated['rest_count_20_bill'] ?? 0);
                $cashRegister->count_10_bill -= (int) ($validated['rest_count_10_bill'] ?? 0);
                $cashRegister->count_5_bill -= (int) ($validated['rest_count_5_bill'] ?? 0);
                $cashRegister->total_coins -= (int) ($validated['rest_total_coins'] ?? 0);

                $cashRegister->save();

                return redirect()->route('sales.detail', $sale->sale_id);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }
    }

    public function salesDetail($sale_id)
    {
        $locationIds = auth()->user()->location_user->pluck('location_id');

        $sale = Sale::with(['saleDetails.inventory.product', 'user'])
            ->when($locationIds->isNotEmpty(), fn ($query) => $query->whereHas(
                'cashRegister',
                fn ($cashRegister) => $cashRegister->whereIn('location_id', $locationIds)
            ))
            ->where('sale_id', $sale_id)
            ->firstOrFail();

        return Inertia::render('Sale/SaleDetail', ['sale' => $sale]);
    }
}
