<?php

namespace App\Http\Controllers;

use App\Exports\MultiSheetExport;
use App\Models\Warehouse;
use App\Models\Report;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getReports()
    {
        $reports = Report::all();
        $warehouses = Warehouse::whereNotIn('warehouse_id', [1,2,3])->get();
        return Inertia::render('Reports/ReportsList', ['reports' => $reports, 'warehouses' => $warehouses]);
    }

    public function generateReport($typeReport, $range_date, $warehouseIds)
    {        
        if (is_string($range_date) && strpos($range_date, ',') !== false) {
            $range_date = explode(',', $range_date);
        }

        $startDate = Carbon::parse($range_date[0])->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($range_date[1])->format('Y-m-d H:i:s');

        if (is_string($warehouseIds) && strpos($warehouseIds, ',') !== false) {
            $warehouseIds = explode(',', $warehouseIds);
        }
        if ($warehouseIds == 1 || in_array(1, $warehouseIds)) {
            $warehouseIds = null;
        }
        
        switch ($typeReport) {
            case '1':
                $data = $this->totalSoldReport($startDate, $endDate, $warehouseIds);
                break;
            case '2':
                $data = $this->topReferenceReport($startDate, $endDate, $warehouseIds, 'desc', 'category');
                break;
            case '3':
                $data = $this->topReferenceReport($startDate, $endDate, $warehouseIds, 'asc', 'category');
                break;
            case '4':
                $data = $this->peakHoursReport($startDate, $endDate, $warehouseIds);
                break;
            case '5':
                $data = $this->topBranchesReport($startDate, $endDate);
                break;
        }

        return $this->downloadFile($typeReport, $data, $startDate, $endDate);
    }

    private function totalSoldReport($startDate, $endDate, $warehouseIds)
    {
        $query = Sale::select('locations.name as location_name', DB::raw('SUM(total) as total'))
            ->join('cash_registers', 'sales.cash_register_id', '=', 'cash_registers.cash_register_id')
            ->join('locations', 'cash_registers.location_id', '=', 'locations.location_id')
            ->join('warehouses', 'warehouses.location_id', '=', 'locations.location_id')
            ->groupBy('locations.name')
            ->limit(1000)
            ->whereBetween('sales.created_at', [$startDate, $endDate]);

        if ($warehouseIds) {
            $query->whereIn('warehouses.warehouse_id', $warehouseIds);
        }

        $totalSold = $query->get();

        return $totalSold;
    }

    private function topReferenceReport($startDate, $endDate, $warehouseIds, $orderBy = 'desc', $where = 'category')
    {
        $locationsQuery = [];
        if ($warehouseIds) {
            foreach ($warehouseIds as $warehouseId) {
                $query = SaleDetail::selectRaw('products.reference, SUM(sale_details.quantity) as total_quantity_sold, locations.name as location_name')
                    ->join('inventories', 'sale_details.inventory_id', '=', 'inventories.inventory_id')
                    ->join('products', 'inventories.product_id', '=', 'products.product_id')
                    ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.warehouse_id')
                    ->join('locations', 'warehouses.location_id', '=', 'locations.location_id');
                if($where == 'category'){
                    $query->whereIn('category', ['Dama', 'Caballero', 'Unisex']);
                }else{
                    $query->where('products.reference', 'like', '%'. $where .'%');
                }
                $query->groupBy('products.product_id', 'locations.name', 'warehouses.warehouse_id')
                    ->orderBy('total_quantity_sold', $orderBy)
                    ->whereBetween('sale_details.created_at', [$startDate, $endDate])
                    ->limit(10)
                    ->whereHas('inventory', function($q) use ($warehouseId) {
                        $q->where('warehouse_id', $warehouseId);
                    });
                $locationsQuery[] = $query->get();
            }
        }else{
            $query = SaleDetail::selectRaw('products.reference, SUM(sale_details.quantity) as total_quantity_sold')
                ->join('inventories', 'sale_details.inventory_id', '=', 'inventories.inventory_id')
                ->join('products', 'inventories.product_id', '=', 'products.product_id')
                ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.warehouse_id')
                ->join('locations', 'warehouses.location_id', '=', 'locations.location_id');
            if($where == 'category'){
                $query->whereIn('category', ['Dama', 'Caballero', 'Unisex']);
            }else{
                $query->where('products.reference', 'like', '%' . $where . '%');
            }
            $query->groupBy('products.product_id')
                ->orderBy('total_quantity_sold', $orderBy)
                ->whereBetween('sale_details.created_at', [$startDate, $endDate])
                ->limit(10)
                ->get();

            $locationsQuery[] = $query->get();
        }

        return $locationsQuery;
    }

    private function topBranchesReport($startDate, $endDate)
    {
        $topBranches = Sale::selectRaw('locations.name, SUM(sales.total) as total_sales')
            ->join('cash_registers', 'sales.cash_register_id', '=', 'cash_registers.cash_register_id')
            ->join('locations', 'cash_registers.location_id', '=', 'locations.location_id')
            ->groupBy('locations.name')
            ->orderByDesc('total_sales')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->limit(10)
            ->get();

        return $topBranches;
    }

    private function peakHoursReport($startDate, $endDate, $warehouseIds)
    {
        $locationsQuery = [];
        if ($warehouseIds) {
            foreach ($warehouseIds as $warehouseId) {
                $query = Sale::select(
                        'locations.name as location_name',
                        DB::raw("DATE_FORMAT(sales.created_at, '%h:%i %p') as formatted_hour"),
                        DB::raw('COUNT(sales.sale_id) as number_of_sales')
                    )
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->join('cash_registers', 'sales.cash_register_id', '=', 'cash_registers.cash_register_id')
                    ->join('locations', 'cash_registers.location_id', '=', 'locations.location_id')
                    ->join('warehouses', 'warehouses.location_id', '=', 'locations.location_id')
                    ->groupBy('locations.name', 'formatted_hour')
                    ->orderBy('locations.name', 'asc')
                    ->orderBy('number_of_sales', 'desc')
                    ->orderByDesc('number_of_sales')
                    ->where('warehouses.warehouse_id', $warehouseId)
                    ->limit(10);

                $locationsQuery[] = $query->get()
                    ->groupBy('location_name')
                    ->map(function ($items) {
                        return $items->take(10);
                    })
                    ->flatten();
            }
        }else{
            $locationsQuery[] = Sale::select(
                    DB::raw("DATE_FORMAT(sales.created_at, '%h:%i %p') as formatted_hour"),
                    DB::raw('COUNT(sales.sale_id) as number_of_sales')
                )
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->join('cash_registers', 'sales.cash_register_id', '=', 'cash_registers.cash_register_id')
                ->join('locations', 'cash_registers.location_id', '=', 'locations.location_id')
                ->groupBy('formatted_hour')
                ->orderBy('number_of_sales', 'desc')
                ->orderByDesc('number_of_sales')
                ->limit(10)
                ->get();
        }

        return $locationsQuery;
    }

    public function downloadFile($typeReport, $data, $startDate, $endDate){

        $nameReport = '';
        $dataForExcel = [];

        switch ($typeReport) {
            case '1':
                $nameReport = 'Total vendido';
                $dataForExcel = [[
                    'collection' => collect($data->map(function ($sale) {
                        return [
                            'location' => $sale->location_name ?? '',
                            'total' => $sale->total ?? 0,
                        ];
                    })), 
                    'title' => 'Total vendido', 
                    'headings' => ['Sucursal', 'Total'],
                    'columnFormat' => 'B'
                ]];
                break;
            case '2':
                $nameReport = 'Top 10 Fragancias';
                foreach ($data as $location) {
                    $title = $location[0]->location_name ?? 'Top 10 Fragancias';
                    $dataForExcel[] = [
                        'collection' => collect($location->map(function ($reference) {
                            return [
                                'reference' => $reference->reference ?? '',
                                'total' => ($reference->total_quantity_sold ?? 0) . ' ml',
                            ];
                        })), 
                        'title' => $title, 
                        'headings' => ['Fragancia', 'Cantidad Vendida'],
                        'columnFormat' => null
                    ];
                }
                break;
            case '3':
                $nameReport = 'Top 10 Fragancias con menor venta';
                foreach ($data as $location) {
                    $title = $location[0]->location_name ?? 'Top 10 Fragancias con menor venta';
                    $dataForExcel[] = [
                        'collection' => collect($location->map(function ($reference) {
                            return [
                                'reference' => $reference->reference ?? '',
                                'total' => ($reference->total_quantity_sold ?? 0) . ' ml',
                            ];
                        })), 
                        'title' => $title, 
                        'headings' => ['Fragancia', 'Cantidad Vendida'],
                        'columnFormat' => null
                    ];
                }
                break;
            case '4':
                $nameReport = 'Picos altos de venta por hora y sucursal';
                foreach ($data as $location) {
                    $title = $location[0]->location_name ?? 'Picos altos de venta por hora general';
                    $dataForExcel[] = [
                        'collection' => collect($location->map(function ($hour) {
                            return [
                                'hour' => $hour->formatted_hour ?? '',
                                'total' => $hour->number_of_sales ?? 0,
                            ];
                        })), 
                        'title' => $title, 
                        'headings' => ['Pico de Hora', 'N° Total Ventas'],
                        'columnFormat' => null
                    ];
                }
                break;
            case '5':
                $nameReport = 'Top 10 Sucursales con mayor venta';
                $dataForExcel[] = [
                    'collection' => collect($data->map(function ($sale) {
                        return [
                            'location' => $sale->name ?? '',
                            'total' => $sale->total_sales ?? 0,
                        ];
                    })), 
                    'title' => 'Top 10 Sucursales con mayor venta', 
                    'headings' => ['Sucursal', 'Total Ventas'],
                    'columnFormat' => 'B'
                ];
                break;
        }
        
        $report = Report::create([
            'type_report' => $nameReport,
            'start_date_report' => $startDate,
            'end_date_report' => $endDate
        ]);

        return Excel::download(
            new MultiSheetExport($dataForExcel),
            $nameReport.'_report_'.$report->report_id.'.xlsx'
        );
    }
}
