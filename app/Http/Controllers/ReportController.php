<?php

namespace App\Http\Controllers;

use App\Exports\FraganceReport;
use App\Exports\UsersExport;
use App\Models\Report;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function getReports()
    {
        $reports = Report::all();
        return Inertia::render('Reports/ReportsList', ['reports' => $reports]);
    }

    public function storeReports(Request $request)
    {

        // $request->validate([
        //     'type_report' => 'required',
        //     'start_date_report' => 'required',
        //     'end_date_report' => 'required',
        // ]);

        return redirect()->route('download.report');
        dd("test");

        if($request->type_report == 'Reporte de venta por fragancia') {        

            return Excel::download(new FraganceReport, 'users.xlsx');
        }
    

        dd($request->all());
        
        Report::create($request->all());
        return redirect()->route('reports');
    }

    public function downloadFile(){
        Report::create(['type_report' => 'Reporte de venta por fragancia']);

        return Excel::download(new FraganceReport, 'fragances_per_location.xlsx');
    }
}
