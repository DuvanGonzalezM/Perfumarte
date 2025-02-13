<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditInventory;
use App\Models\Location;
use App\Models\Warehouse;
use App\Models\Inventory;
use Inertia\Inertia;

use Illuminate\Http\Request;

class AuditController extends Controller
{
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
        $location = Location::all();
        $audits = Audit::with('user')->find($id);
        return Inertia::render('Audit/AuditDetailCash', ['audits' => $audits, 'locations' => $location]);
    }
    public function getAllProducts(Request $request)
    {
        $locationId = $request->input('location_id');

        if (!$locationId) {
            return response()->json([
                'products' => [],
                'error' => 'No se ha seleccionado una sede válida.'
            ]);
        }

        $productsAudit = Inventory::whereHas('warehouse', function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        })
            ->with('product')
            ->get();

            $location = Location::find($locationId);

        return Inertia::render('Audit/AuditInventory', [
            'productsAudit' => $productsAudit,
            'location_id' => $locationId, 
            'location_name' => $location ? $location->name : 'Ubicación no encontrada', 
        ]);
    }
    public function storeAuditInventory(Request $request)
    {
        
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.inventory_id' => 'required|integer',
            'products.*.quantity' => 'required|numeric',
            'products.*.confirmed' => 'required|boolean',
            'products.*.observations' => 'nullable',
            'location_id' => 'required|integer', 
        ]);
        
        
        $audit = Audit::create([
            'user_id' => auth()->user()->user_id,
            'type_audit' => 2, 
            'location_id' => $validatedData['location_id'],
        ]);

        foreach ($validatedData['products'] as $product) {
            AuditInventory::create([
                'id_audits' => $audit->id_audits,
                'inventory_id' => $product['inventory_id'],
                'quantity_system' => $product['quantity'],
                'confirmation_inventory' => $product['confirmed'],
                'observation' => $product['observations'],
            ]);
        }

        return redirect()->route('audits')->with('success', 'Auditoría registrada exitosamente.');
    }

    public function auditInventoryDetail($id_audits)
    {
        $auditInventoryDetail = Audit::with('auditInventory.inventory.product', 'location')->where('id_audits', $id_audits)->first();
        return Inertia::render('Audit/AuditDetailInventory', ['auditInventoryDetail' => $auditInventoryDetail]);
        
    }













}
