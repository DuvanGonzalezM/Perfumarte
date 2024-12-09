<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestPrais;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestPraisController extends Controller
{
    /**
     * Mostrar lista de solicitudes de insumos
     */
    public function getAllRequest()
    {
        $suppliesRequest = RequestPrais::with('user.location')
            ->where('request_type', '1')
            ->get();

        return Inertia::render('Requests/SuppliesRequestList', [
            'suppliesRequest' => $suppliesRequest
        ]);
    }

    /**
     * Mostrar detalles de una solicitud específica
     */
    public function detailRequest($requestId)
    {
        $suppliesRequest = RequestPrais::with([
            'user.location',
            'detailRequest.inventory.product',
        ])->findOrFail($requestId);

        return Inertia::render('Requests/SuppliesRequestDetails', [
            'requestPrais' => $suppliesRequest
        ]);
    }

    /**
     * Mostrar la vista de detalle de una solicitud
     */
    public function showDetail($requestId)
    {
        
            $requestPrais = RequestPrais::with([
                'detailRequest.inventory.product',
                'user.location'
            ])->findOrFail($requestId);

            $inventories = Inventory::with('product')
                ->where('status', 1)
                ->get()
                ->map(function ($inventory) {
                    return [
                        'id' => $inventory->id,
                        'name' => $inventory->product->name
                    ];
                });

            return Inertia::render('Requests/DetailSuppliesRequest', [
                'requestPrais' => $requestPrais,
                'inventories' => $inventories
            ]);
        
    }

    /**
     * Mostrar el formulario de edición de una solicitud
     */
    public function edit($requestId)
    {
        try {
            $requestPrais = RequestPrais::with([
                'detailRequest.inventory.product',
                'user.location'
            ])->findOrFail($requestId);

            if (!$requestPrais->detailRequest) {
                throw new \Exception('No se encontraron detalles de la solicitud');
            }

            $inventories = Inventory::with('product')->get();

            return Inertia::render('Requests/EditSuppliesRequest', [
                'requestPrais' => $requestPrais,
                'inventories' => $inventories
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('suppliesrequest.validation')
                ->with('error', 'No se pudo cargar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una solicitud existente
     */
    public function update(Request $request, $requestId)
    {
        $request->validate([
            'references.*.reference' => 'required|exists:inventories,id',
            'references.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            $requestPrais = RequestPrais::findOrFail($requestId);
            
            // Eliminar detalles existentes
            RequestDetail::where('request_id', $requestId)->delete();
            
            // Crear nuevos detalles
            foreach ($request->references as $reference) {
                RequestDetail::create([
                    'request_id' => $requestId,
                    'inventory_id' => $reference['reference'],
                    'quantity' => $reference['quantity']
                ]);
            }

            return redirect()
                ->route('suppliesrequest.validation')
                ->with('success', 'Solicitud actualizada correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Validar una solicitud
     */
    public function validateRequest($requestId)
    {
        try {
            $request = RequestPrais::findOrFail($requestId);
            $request->status = 'approved';
            $request->save();

            return redirect()
                ->back()
                ->with('success', 'Solicitud aprobada correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function rejectRequest(Request $request, $requestId)
    {
        try {
            $suppliesRequest = RequestPrais::findOrFail($requestId);
            $suppliesRequest->status = 'rejected';
            $suppliesRequest->rejection_reason = $request->reason;
            $suppliesRequest->save();

            return redirect()
                ->back()
                ->with('success', 'Solicitud rechazada correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar la vista de validación de solicitudes
     */
    public function getValidationView()
    {
        $suppliesRequest = RequestPrais::with('user.location')
            ->where('request_type', '1')
            ->get()
            ->map(function ($request) {
                // Si no tiene estado, asumimos que está pendiente de aprobación
                if (empty($request->status)) {
                    $request->status = 'pending_approval';
                }
                return $request;
            });

        return Inertia::render('Requests/ValidSuppliesRequest', [
            'suppliesRequest' => $suppliesRequest
        ]);
    }

    /**
     * Mostrar lista de solicitudes de transformación
     */
    public function getAllRequestTransformation()
    {
        $transformationRequest = RequestPrais::where('request_type', '2')->get();
        return Inertia::render('RequestTransformation/TransformationRequestList', [
            'transformationRequest' => $transformationRequest
        ]);
    }

    /**
     * Mostrar detalles de una solicitud de transformación
     */
    public function detailTransformation($requestId)
    {
        $tranformationRequest = RequestPrais::with([
            'user.location',
            'detailRequest.inventory.product',
        ])->findOrFail($requestId);

        return Inertia::render('RequestTransformation/TransformationDetail', [
            'transformationRequest' => $tranformationRequest
        ]);
    }

    /**
     * Crear una solicitud de transformación
     */
    public function createTransformation()
    {
        $inventories = Inventory::with('product')->where('warehouse_id', '1')->get();

        return Inertia::render('RequestTransformation/CreateTransformation', [
            'inventories' => $inventories
        ]);
    }

    /**
     * Guardar una solicitud de transformación
     */
    public function storeTransformation(Request $request)
    {
        $request->validate([
            'references.*.reference' => 'required',
            'references.*.quantity' => 'required',
        ]);

        $transformationRequest = RequestPrais::create([
            'request_type' => '2',
            'user_id' => $request->user()->user_id,
            'status' => 'Pendiente'
        ]);

        foreach ($request->references as $reference) {
            RequestDetail::create([
                'request_id' => $transformationRequest->request_id,
                'inventory_id' => $reference['reference'],
                'quantity' => $reference['quantity']
            ]);
        }
        return redirect()->route('transformationRequest.list', [
            'message' => '',
            'status' => 200
        ]);
    }
}