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
        $audits = Audit::with('user.location')->get();
        $locations = Location::all();
        $warehouses = Warehouse::all();

        return Inertia::render('Audit/AuditList', ['audits' => $audits, 'locations' => $locations, 'warehouses' => $warehouses]);
    }
    public function showDetailAuditCash($id)
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
       
    }

    public function storeCashAudit(Request $request)
    {
        
    }
}
