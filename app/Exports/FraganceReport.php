<?php

namespace App\Exports;

use App\Models\SaleDetail;
use Maatwebsite\Excel\Concerns\FromCollection;

class FraganceReport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SaleDetail::query()
            ->join('inventories', 'sale_details.inventory_id', '=', 'inventories.inventory_id')
            ->join('products', 'inventories.product_id', '=', 'products.product_id')
            ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.warehouse_id')
            ->join('locations', 'warehouses.location_id', '=', 'locations.location_id')
            // ->whereBetween('sale_details.created_at', [$request->start_date_report, $request->end_date_report])
            ->selectRaw('
                products.commercial_reference,
                locations.name,
                SUM(sale_details.units * sale_details.quantity) as cantidad,
                SUM(sale_details.price) as total_price
            ')
            ->groupBy('products.commercial_reference', 'locations.name')
            ->get();
    }
}
