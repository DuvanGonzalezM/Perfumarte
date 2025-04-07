<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Dispatch;
use App\Models\Inventory;
use App\Models\Request;
use App\Models\RequestPrais;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Assign;


class DashboardController extends Controller
{
    public function getDataInventory()
    {
        // Si es asesor comercial, redirigir a inventario actual
        if (Auth::user()->hasRole('Asesor comercial')) {
            return redirect()->route('inventory.current');
        }

        $stock1 = Inventory::where('warehouse_id', '1')->get();
        $stock2 = Inventory::where('warehouse_id', '2')->get();
        $stockAlmacen = Inventory::where('warehouse_id', '3')->get();
        $despachos = Dispatch::where('status', 'Finalizado')->get();
        $despachosPendientes = Dispatch::where('status', 'en ruta')->get();
        $transformacionPendiente = RequestPrais::where('request_type', '2')->where('status', 'Pendiente')->get();
        $personalAsignado = User::where('boss_user', Auth::user()->user_id)->get();
        $getAdvisorsSubdirector = [];
        if (Auth::user()->hasRole('Subdirector')) {
          
            $subdirectorId = Auth::user()->user_id; 
            $supervisores = User::where('boss_user', $subdirectorId)->get();
            $supervisorIds = $supervisores->pluck('user_id'); 
            $asesores = User::whereIn('boss_user', $supervisorIds)->get();
            $getAdvisorsSubdirector = $asesores;
        }
       
        $SolicitudInsumosSupervisor = RequestPrais::where('request_type', '1')->where('status', 'por solicitar')->get();
        $auditoria = Audit::where('type_audit', '2')->where('user_id', Auth::user()->user_id)->get();
        $arqueo = Audit::where('type_audit', '1')->where('user_id', Auth::user()->user_id)->get();

        return Inertia::render('Dashboard', [
            'inventory' => $stock1,
            'inventory2' => $stock2,
            'inventoryAlmacen' => $stockAlmacen,
            'finalized' => $despachos,
            'pendingDispatches' => $despachosPendientes,
            'pendingTransformation' => $transformacionPendiente,
            'assignedPersonnel' => $personalAsignado,
            'pendingRequestSupervisor' => $SolicitudInsumosSupervisor,
            'getAuditInventory' => $auditoria,
            'getAuditCash' => $arqueo,
            'asesores' => $getAdvisorsSubdirector
        ]);

    }
}