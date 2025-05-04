<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function closeCashRegister()
    {
        $today = Carbon::today();
        
        // Buscar la caja del día actual para la sede del usuario que no esté cerrada
        $cashRegister = CashRegister::whereDate('created_at', $today)
            ->where('location_id', auth()->user()->location_id)
            ->where('confirmationclosingcash', null )
            ->first();

        $totalDigital = (float) Sale::whereDate('created_at', $today)
            ->where('payment_method', 'Transferencia')
            ->where('cash_register_id', $cashRegister->cash_register_id)
            ->sum('total');
        
        $totalCash = (float) Sale::whereDate('created_at', $today)
            ->where('payment_method', 'Efectivo')
            ->where('cash_register_id', $cashRegister->cash_register_id)
            ->sum('total');

        // Preparar los datos para la vista
        $data = [
            'totalDigital' => $totalDigital,
            'totalCash' => $totalCash,
            'totalSales' => $totalDigital + $totalCash,
            'cashRegisterId' => $cashRegister ? $cashRegister->cash_register_id : null
        ];
    
        return Inertia::render('Sale/CashClose', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cashRegisterId' => 'required|exists:cash_registers,cash_register_id',
            'count_100_bill' => 'required|integer|min:0',
            'count_50_bill' => 'required|integer|min:0',
            'count_20_bill' => 'required|integer|min:0',
            'count_10_bill' => 'required|integer|min:0',
            'count_5_bill' => 'required|integer|min:0',
            'count_2_bill' => 'required|integer|min:0',
            'total_coins' => 'required|integer|min:0',
            'total_digital' => 'required|numeric|min:0',
            'total_cash' => 'required|numeric|min:0',
            'observations' => 'nullable|string'
        ]);

        // Calcular el total recolectado en efectivo
        $totalCash = 
            ($request->count_100_bill * 100000) +
            ($request->count_50_bill * 50000) +
            ($request->count_20_bill * 20000) +
            ($request->count_10_bill * 10000) +
            ($request->count_5_bill * 5000) +
            ($request->count_2_bill * 2000) +
            $request->total_coins;

        // Calcular el total recolectado (efectivo + digital)
        $totalCollected = (float) $totalCash + (float) $request->total_digital;

        // Actualizar el registro de cierre de caja existente
        $cashRegister = CashRegister::findOrFail($request->cashRegisterId);
        
        // Verificar que la caja pertenezca a la sede del usuario
        if ($cashRegister->location_id !== auth()->user()->location_id) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para cerrar esta caja');
        }

        $cashRegister->update([
            'total_collected' => $totalCollected,
            'total_digital' => (float) $request->total_digital,
            'count_100_bill' => $request->count_100_bill,
            'count_50_bill' => $request->count_50_bill,
            'count_20_bill' => $request->count_20_bill,
            'count_10_bill' => $request->count_10_bill,
            'count_5_bill' => $request->count_5_bill,
            'count_2_bill' => $request->count_2_bill,
            'total_coins' => $request->total_coins,
            'observations' => $request->observations,
            'confirmationclosingcash' => true
        ]);

        return redirect()->route('sales.list')
            ->with('success', 'Cierre de caja realizado con éxito');
    }
}
