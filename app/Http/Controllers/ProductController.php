<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{
    public function getAllProducts()
    {
       $getproducts = Product::with('supplier')->where('status', 1)->get();
       $supplierProduct = Supplier::all();
     
       return Inertia::render('Products/ProductsList', [
        'getProducts' => $getproducts,
        'supplierProduct' => $supplierProduct,
        'operationalRoles' => $this->operationalRoleOptions(),

    ]);
    }

    public function createProduct()
    {
        $supplierProduct = Supplier::all();

        return Inertia::render('Products/CreateProduct', [
            'supplierProduct' => $supplierProduct,
            'operationalRoles' => $this->operationalRoleOptions(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'reference' => 'required',
            'measurement_unit' => 'required',
            'commercial_reference' => 'required',
            'category' => 'required',
            'supplier_id' => 'required',
            'code' => 'required',
            'operational_role' => ['nullable', Rule::in(array_keys(config('prais.product_roles.labels', [])))],
        ]);

        $this->assertRoleIsAvailable($request->input('operational_role'));

        Product::create([
            'reference' => $request['reference'],
            'measurement_unit' => $request['measurement_unit'],
            'commercial_reference' => $request['commercial_reference'],
            'category' => $request['category'],
            'supplier_id' => $request['supplier_id'],
            'code' => $request['code'],
            'operational_role' => $request->filled('operational_role') ? $request['operational_role'] : null,
            'status' => 1,
        ]);

        return redirect()->route('products.list', ['message' => '', 'status' => 200]);
    }

    public function editProduct(Request $request, $product_id)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
            'measurement_unit' => 'required|string|max:255',
            'commercial_reference' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'supplier_id' => 'required',
            'code' => 'required',
            'operational_role' => ['nullable', Rule::in(array_keys(config('prais.product_roles.labels', [])))],
        ]);
        $product = Product::findOrFail($product_id);

        $this->assertRoleIsAvailable($request->input('operational_role'), (int) $product_id);

        $product->update([
            'reference' => (string) $request->reference,
            'measurement_unit' => (string) $request->measurement_unit,
            'commercial_reference' => (string) $request->commercial_reference,
            'category' => (string) $request->category,
            'supplier_id' => (int) $request->supplier_id,
            'code' => (string) $request->code,
            'operational_role' => $request->filled('operational_role') ? (string) $request->operational_role : null,
        ]);
        return redirect()->route('products.list');
    }

    public function disableProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->update([
            'status' => 0
        ]);
        return redirect()->route('products.list');
    }

    /**
     * Opciones que ve el administrador. El rol operativo es lo que permite que
     * venta y laboratorio encuentren sus productos sin depender del id.
     */
    private function operationalRoleOptions(): array
    {
        $options = [];

        foreach (config('prais.product_roles.labels', []) as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    /**
     * Roles como "bolsa de regalo" o "disolvente" identifican a un único
     * producto: si dos lo tuvieran, la venta y el laboratorio elegirían uno al
     * azar.
     */
    private function assertRoleIsAvailable(?string $role, ?int $ignoreProductId = null): void
    {
        if (! $role || ! in_array($role, config('prais.product_roles.exclusive', []), true)) {
            return;
        }

        $taken = Product::where('operational_role', $role)
            ->when($ignoreProductId, fn ($query) => $query->where('product_id', '!=', $ignoreProductId))
            ->first();

        if ($taken) {
            $label = config("prais.product_roles.labels.{$role}", $role);

            throw ValidationException::withMessages([
                'operational_role' => "El rol «{$label}» ya lo tiene el producto «{$taken->reference}». Quíteselo antes de asignarlo aquí.",
            ]);
        }
    }
}