<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditCash;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Location;
use App\Models\Warehouse;
use Inertia\Inertia;

use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function showAudits()
    {
        $user = auth()->user(); 
        $locationIds = $user->location_user()->pluck('locations.location_id'); 
    

        $audits = Audit::with('user.location')
            ->whereHas('user.location', function ($query) use ($locationIds) {
                $query->whereIn('locations.location_id', $locationIds);
            })
            ->get();
    
        $locations = Location::whereIn('location_id', $locationIds)->get(); 
        $warehouses = Warehouse::whereIn('location_id', $locationIds)->get(); 
    
        return Inertia::render('Audit/AuditList', [
            'audits' => $audits,
            'locations' => $locations,
            'warehouses' => $warehouses
        ]);
    }    public function showDetailAuditCash($id)
    {
        $location = Location::all();
        $audits = Audit::with('user')->find($id);
        return Inertia::render('Audit/AuditDetailCash', ['audits' => $audits, 'locations' => $location]);
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

    public function showCashAudit()
    {
        $location = Location::all();
        return Inertia::render('Audit/AuditCash', ['locations' => $location]);
    }

    public function storeCashAudit(Request $request)
    {
        
    }
}
