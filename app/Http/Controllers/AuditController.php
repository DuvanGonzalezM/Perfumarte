<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditCash;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Location;
use App\Models\Warehouse;
use Inertia\Inertia;

use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function getCashAuditByLocation($locationId)
    {

        $today = now()->toDateString();
        $location = Location::find($locationId);

        $cashSales = Sale::with([
            'cashRegister.location' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }
        ])
            ->whereDate('created_at', $today)
            ->where('payment_method', 'Efectivo')
            ->with('saleDetails')
            ->get();

        $digitalSales = Sale::with([
            'cashRegister.location' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }
        ])
            ->whereDate('created_at', $today)
            ->where('payment_method', 'Transferencia')
            ->with('saleDetails')
            ->get();

        return Inertia::render('Audit/AuditCash', [
            'cashSales' => $cashSales,
            'digitalSales' => $digitalSales,
            'location_id' => $locationId,
            'location_name' => $location ? $location->name : 'Ubicación no encontrada', 
        ]);
    }

    public function confirmCashAudit(Request $request, $locationId)
    {
        $cashRegister = CashRegister::with('sales.saleDetails')->where('location_id', $locationId)->first();

        $validatedData = $request->validate([
            'total_cash_sales' => 'required|numeric',
            'total_digital_sales' => 'required|numeric',
            'confirmationCash' => 'required|boolean',
            'confirmationDigital' => 'required|boolean',
            'observations' => 'nullable|string'
        ]);

        $audit = new Audit();
        $audit->user_id = auth()->id();
        $audit->location_id = $locationId;
        $audit->type_audit = '1';
        $audit->save();

        $auditCash = new AuditCash();
        $auditCash->id_audits = $audit->id_audits;
        $auditCash->cash_register_id = $cashRegister->cash_register_id;
        $auditCash->money_in_box = $validatedData['total_cash_sales'];
        $auditCash->money_in_digital = $validatedData['total_digital_sales'];
        $auditCash->confirmation_cash = $validatedData['confirmationCash'];
        $auditCash->confirmation_digital = $validatedData['confirmationDigital'];
        $auditCash->observation = $validatedData['observations'];
        $auditCash->save();

        return redirect()->route('audits')->with('success', 'Cash audit confirmed successfully');
    }
    public function showAudits()
    {
        $user = auth()->user();
        $locationIds = $user->location_user()->pluck('locations.location_id');

        $audits = Audit::with(['user', 'location'])
            ->whereIn('location_id', $locationIds)
            ->get();

        $locationsAudit = Location::whereIn('location_id', $locationIds)->get();
        $warehouses = Warehouse::whereIn('location_id', $locationIds)->get();

        return Inertia::render('Audit/AuditList', [
            'audits' => $audits,
            'locationsAudit' => $locationsAudit,
            'warehouses' => $warehouses
        ]);
    }
    public function showDetailAuditCash($id)
    {
        $audit = Audit::with('location')->find($id);
    
        if (!$audit) {
            return redirect()->route('audits')->with('error', 'Auditoría no encontrada.');
        }
    
        $auditCash = AuditCash::where('id_audits', $audit->id_audits)->first();
    
        if (!$auditCash) {
            return redirect()->route('audits')->with('error', 'Detalles de auditoría de caja no encontrados.');
        }
    
        return Inertia::render('Audit/AuditDetailCash', [
            'audit' => $audit,
            'auditCash' => $auditCash
        ]);
    }
    public function showDetailAuditInventory($id)
    {
        $location = Location::all();
        $audits = Audit::with('user')->find($id);
        return Inertia::render('Audit/AuditDetailInventory', ['audits' => $audits, 'locations' => $location]);
    }
    public function showInventoryAudit()
    {
        return Inertia::render('Audit/AuditInventory');
    }
}
