<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function sales()
    {
        $sales = Sale::with('user')->where('created_at', '>=', date('Y-m-d'))->get();
        return Inertia::render('Sale/SalesList', ['sales' => $sales]);
    }

    public function createSales()
    {
        $user = Auth::user();
        $location_id = $user->location_user[0]->location_id;
        $assessors = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
        })
            ->whereDoesntHave('location_user', function ($query) use ($location_id) {
                $query->where('location_user.location_id', '!=', $location_id);
            })
            ->get();
        $warehouses = $user->location_user[0]->warehouses;
        $inventory = null;
        if (count($warehouses) > 0) {
            $inventory = Inventory::with(relations: 'product')->where('warehouse_id', $warehouses[0]->warehouse_id)->get();
        }

        return Inertia::render('Sale/CreateSale', ['assessors' => $assessors, 'inventory' => $inventory]);
    }
    public function storeSales(Request $request)
    {
        $user = Auth::user();
        $location_id = $user->location_user[0]->location_id;
        $cashRegister = CashRegister::where('location_id', $location_id)->first();

        $sale = Sale::create([
            'cash_register_id' => $cashRegister->cash_register_id,
            'total' => $request->total,
            'user_id' => $request->assessor,
            'payment_method' => 'Efectivo',
            'transaction_code' => 'NA',
        ]);

        foreach ($request->references as $reference) {
            SaleDetail::create([
               'inventory_id' => $reference['reference'],
               'sale_id' => $sale->sale_id,
               'quantity' => $reference['quantity'],
               'price' => $reference['quantity'] == 30 ? 17000 : ($reference['quantity'] == 50 ? 25000 : ($reference['quantity'] == 100 ? 38000 : 0)),
            ]);
        }

        return redirect()->route('sales.list')->with('success', 'Despacho creado exitosamente.');
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

        // Mapeo de denominaciones en centavos (ajustar según tu caso)
        $denominations = [
            100000 => $cashRegister->count_100_bill,  // Billete de 100,000 COP
            50000 => $cashRegister->count_50_bill,    // Billete de 50,000 COP
            20000 => $cashRegister->count_20_bill,    // Billete de 20,000 COP
            10000 => $cashRegister->count_10_bill,    // Billete de 10,000 COP
            5000 => $cashRegister->count_5_bill,      // Billete de 5,000 COP
            2000 => $cashRegister->count_2_bill,      // Billete de 2,000 COP
            1000 => $cashRegister->count_1_bill,      // Billete de 1,000 COP
        ];

        $remaining = $changeAmount;
        $change = [];

        // Primera pasada: prioriza billetes con mayor disponibilidad
        $sortedByCount = $denominations;
        arsort($sortedByCount);

        foreach ($sortedByCount as $denom => $count) {
            $reserve = 2; // Mantener al menos 2 billetes
            $usable = max(0, $count - $reserve);
            $maxPossible = min((int)($remaining / $denom), $usable);

            if ($maxPossible > 0) {
                $change[$denom] = $maxPossible;
                $remaining -= $maxPossible * $denom;
                $denominations[$denom] -= $maxPossible;

                if ($remaining === 0) break;
            }
        }

        // Segunda pasada: algoritmo voraz estándar si aún queda cambio
        if ($remaining > 0) {
            $sortedByValue = array_reverse($denominations, true);

            foreach ($sortedByValue as $denom => $count) {
                if ($count <= 0) continue;

                $maxPossible = min((int)($remaining / $denom), $count);

                if ($maxPossible > 0) {
                    $change[$denom] = ($change[$denom] ?? 0) + $maxPossible;
                    $remaining -= $maxPossible * $denom;
                    $denominations[$denom] -= $maxPossible;

                    if ($remaining === 0) break;
                }
            }
        }

        if ($remaining !== 0) {
            throw new \Exception("No hay suficiente cambio disponible");
        }

        // Actualizar la caja registradora
        $cashRegister->update([
            'count_100_bill' => $denominations[100000],
            'count_50_bill' => $denominations[50000],
            'count_20_bill' => $denominations[20000],
            'count_10_bill' => $denominations[10000],
            'count_5_bill' => $denominations[5000],
            'count_2_bill' => $denominations[2000],
            'count_1_bill' => $denominations[1000],
        ]);

        return [
            'change_amount' => $changeAmount,
            'bills_used' => $change
        ];
    }

    public function test($precio, $pago){
        try {
            $user = Auth::user();
            $location_id = $user->location_user[0]->location_id;
            $cashRegister = CashRegister::where('location_id', $location_id)->first();
            $result = $this->calculateChange($precio, $pago, $cashRegister);
            return $result;
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
