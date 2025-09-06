<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function sales()
    {
        $userLocation = auth()->user()->location_user[0]->location_id;
        
        // Obtener las ventas del día
        $sales = Sale::with('user')
            ->whereHas('cashRegister', function ($query) use ($userLocation) {
                $query->where('location_id', $userLocation)
                    ->whereDate('created_at', date('Y-m-d'));
            })
            ->get();

        // Obtener el estado de la caja del día
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
        $assessors = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial')->orWhere('name', 'Usuario');
        })
            ->whereHas('location_user', function ($query) {
                $query->where('location_user.location_id', '=', auth()->user()->location_user[0]->location_id);
            })
            ->get();
        $warehouses = auth()->user()->location_user[0]->warehouses;
        $warehouse = [];
        $inventory = null;
        if (count($warehouses) > 0) {
            $warehouse = $warehouses[0];
            $inventory = Inventory::with('product')->where('warehouse_id', $warehouse->warehouse_id)->get();
        }

        return Inertia::render('Sale/CreateSale', ['assessors' => $assessors, 'inventory' => $inventory, 'warehouse' => $warehouse]);
    }

    public function priceReference($quantity, $warehouse, $totalUnits = 0, $allReferences = [])
    {
        $totalUnitsBySize = [
            5 => 0,
            30 => 0,
            50 => 0,
            100 => 0
        ];
        
        // Calcular total de unidades por tamaño
        foreach ($allReferences as $ref) {
            if (isset($ref['quantity']) && isset($ref['units']) && isset($totalUnitsBySize[$ref['quantity']])) {
                $totalUnitsBySize[$ref['quantity']] += $ref['units'];
            }
        }
    
        switch ($quantity) {
            case 5:
                $totalUnits5ml = $totalUnitsBySize[5] ?? 0;
                
                if ($totalUnits5ml >= 50) {
                    // 50+ units: first 50 at 2100, rest at 105000/50 = 2100
                    $discountedUnits = 50;
                    $regularUnits = $totalUnits5ml - $discountedUnits;
                    $totalDiscountedPrice = 105000;
                    $totalRegularPrice = $regularUnits * 2100;
                    return ($totalDiscountedPrice + $totalRegularPrice) / $totalUnits5ml;
                } 
                elseif ($totalUnits5ml >= 25) {
                    // 25-49 units: first 25 at 2700, rest at 66000/25 = 2640
                    $discountedUnits = 25;
                    $regularUnits = $totalUnits5ml - $discountedUnits;
                    $totalDiscountedPrice = 66000;
                    $totalRegularPrice = $regularUnits * 2700;
                    return ($totalDiscountedPrice + $totalRegularPrice) / $totalUnits5ml;
                } 
                elseif ($totalUnits5ml >= 12) {
                    // 12-24 units: first 12 at 3166.67, rest at 38000/12 ≈ 3166.67
                    $discountedUnits = 12;
                    $regularUnits = $totalUnits5ml - $discountedUnits;
                    $totalDiscountedPrice = 38000;
                    $totalRegularPrice = $regularUnits * 3200;
                    return ($totalDiscountedPrice + $totalRegularPrice) / $totalUnits5ml;
                }
                
                // Less than 12 units: base price
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

    public function storeSales(Request $request)
    {
        $cashRegister = CashRegister::where('location_id', auth()->user()->location_user[0]->location_id)->whereDate('created_at', date('Y-m-d'))->first();
        $warehouse = auth()->user()->location_user[0]->warehouses[0];
        
        $sale = Sale::create([
            'cash_register_id' => $cashRegister->cash_register_id,
            'total' => $request->total,
            'user_id' => $request->assessor,
            'payment_method' => $request->pay_method,
            'transaction_code' => $request->pay_method == 'Transferencia' ? $request->transaction_code : '',
        ]);
        
        foreach ($request->references as $reference) {
            $giftBagId = Inventory::with('product')->whereHas('product', function ($query) {
                $query->where('reference', 'Bolsa de regalo');
            })->where('warehouse_id', $warehouse->warehouse_id)->first()->inventory_id;
            $drops = 0;
            array_map(function ($i) use (&$drops) {
                $drops += $i;
            }, $reference['perdurable']);

            $price = $drops * $warehouse->price_drops;

            // Precio base por unidad
            if($reference['reference'] == $giftBagId){
                $unitPrice = 2000;
            }else{
                $unitPrice = $this->priceReference($reference['quantity'], $warehouse, $reference['units'], $request->references);
            }
            
            // Calcular precio total
            $totalPrice = ($unitPrice * $reference['units']) + $price;

            SaleDetail::create([
                'inventory_id' => $reference['reference'],
                'sale_id' => $sale->sale_id,
                'quantity' => $reference['quantity'],
                'units' => $reference['units'],
                'drops' => $drops,
                'price' => $totalPrice,
            ]);
            $inventory = Inventory::where('warehouse_id', $warehouse->warehouse_id)->where('inventory_id', $reference['reference'])->first();
            

                // Calcular la cantidad a retirar (50% del tamaño)
                if($reference['reference'] == $giftBagId){
                    $quantityToSubtract = $reference['units'];
                }else{
                    $quantityToSubtract = ($reference['quantity'] * $reference['units']) * 0.5;
                }
                // Descontar de la fragancia
                $inventory->quantity -= $quantityToSubtract;
                
                // Descontar del disolvente (buscar en el mismo almacén y por código del producto)
                $disolventeInventory = Inventory::where('warehouse_id', $warehouse->warehouse_id)
                    ->whereHas('product', function ($query) {
                        $query->where('product_id', '2');
                    })
                    ->first();

                if ($disolventeInventory) {
                    $disolventeInventory->quantity -= $quantityToSubtract;
                    $disolventeInventory->save();
                }   

                if($reference['container']){
                    $containerInventory = Inventory::with('product')->where('warehouse_id', $warehouse->warehouse_id)
                        ->where('product_id', $reference['container'])
                        ->first();

                    if ($containerInventory) {
                        $containerInventory->quantity -= $reference['units'];
                        foreach (explode(',', $containerInventory->product->dependents) as $dependent) {
                            $dependentInventory = Inventory::with('product')->where('warehouse_id', $warehouse->warehouse_id)
                            ->whereHas('product', function ($query) use ($dependent) {
                                $query->where('code', $dependent);
                            })
                            ->first();
                            if ($dependentInventory) {
                                $dependentInventory->quantity -= $reference['units'];
                                $dependentInventory->save();
                            }
                        }
                        $containerInventory->save();
                    }   
                }
            $inventory->save();
        }

        $cashRegister->total_collected += $request->total;
        if($request->pay_method == 'Transferencia'){
            $cashRegister->total_digital += $request->total;
        }
        $cashRegister->count_100_bill += $request->count_100_bill;
        $cashRegister->count_50_bill += $request->count_50_bill;
        $cashRegister->count_20_bill += $request->count_20_bill;
        $cashRegister->count_10_bill += $request->count_10_bill;
        $cashRegister->count_5_bill += $request->count_5_bill;
        $cashRegister->count_2_bill += $request->count_2_bill;
        $cashRegister->total_coins += $request->total_coins;
        $cashRegister->count_100_bill -= $request->rest_count_100_bill;
        $cashRegister->count_50_bill -= $request->rest_count_50_bill;
        $cashRegister->count_20_bill -= $request->rest_count_20_bill;
        $cashRegister->count_10_bill -= $request->rest_count_10_bill;
        $cashRegister->count_5_bill -= $request->rest_count_5_bill;
        $cashRegister->total_coins -= $request->rest_total_coins;
        $cashRegister->save();

        return redirect()->route('sales.detail', $sale->sale_id);
        
    }

    public function salesDetail($sale_id){
        $sale = Sale::with(['saleDetails.inventory.product', 'user'])->where('sale_id', $sale_id)->first();
        return Inertia::render('Sale/SaleDetail', ['sale' => $sale]);
    }

    public function calculateChange($amountOwed, $amountPaid, CashRegister $cashRegister)
    {
        $changeAmount = $amountPaid - $amountOwed;

        if ($changeAmount < 0) {
            throw new \Exception("Pago insuficiente.");
        }

        if ($changeAmount == 0) {
            return ['change_amount' => 0, 'bills_used' => []];
        }

        $denominations = [
            100000 => [
                'count' => $cashRegister->count_100_bill,
                'name' => '100,000'
            ],
            50000 => [
                'count' => $cashRegister->count_50_bill,
                'name' => '50,000'
            ],
            20000 => [
                'count' => $cashRegister->count_20_bill,
                'name' => '20,000'
            ],
            10000 => [
                'count' => $cashRegister->count_10_bill,
                'name' => '10,000'
            ],
            5000 => [
                'count' => $cashRegister->count_5_bill,
                'name' => '5,000'
            ],
            2000 => [
                'count' => $cashRegister->count_2_bill,
                'name' => '2,000'
            ],
            1000 => [
                'count' => $cashRegister->count_1_bill,
                'name' => '1,000'
            ]
        ];

        $remaining = $changeAmount;
        $change = [];

        while ($remaining > 0) {
            $maxDenomination = null;
            $maxCount = 0;

            foreach ($denominations as $denomination => $data) {
                $count = (int)($remaining / $denomination);

                if ($count > 0 && $count <= $data['count'] && $count > $maxCount) {
                    $maxDenomination = $denomination;
                    $maxCount = $count;
                }
            }

            if ($maxDenomination === null) {
                throw new \Exception("No hay suficiente cambio disponible");
            }

            $change[$maxDenomination] = $maxCount;
            $remaining -= $maxDenomination * $maxCount;
            $denominations[$maxDenomination]['count'] -= $maxCount;
        }


        foreach ($denominations as $denomination => $data) {
            $cashRegister->{'count_' . str_replace(',', '', $data['name']) . '_bill'} = $data['count'];
        }
        $cashRegister->save();

        return [
            'change_amount' => $changeAmount,
            'bills_used' => $change
        ];
    }

    public function test($precio, $pago){
        try {
            $cashRegister = CashRegister::where('location_id', auth()->user()->location_user[0]->location_id)->first();
            $result = $this->calculateChange($precio, $pago, $cashRegister);
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en SaleController@test: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Ocurrió un error durante el test: ' . $e->getMessage()], 500);
        }
    }
}
