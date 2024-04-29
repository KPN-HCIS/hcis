<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeDetailExport;
use App\Exports\GoalExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ExportExcelController extends Controller
{
    public function export(Request $request) 
    {
        $reportType = $request->export_report_type;
        $groupCompany = $request->export_group_company;
        $company = $request->export_company;
        $location = $request->export_location;

        if($reportType==='Goal'){
            $export = new GoalExport($groupCompany, $location, $company);
            return Excel::download($export, 'goals.xlsx');
        }
        return;

    }
    public function exportreportemp() 
    {
        return Excel::download(new EmployeeDetailExport, 'employees_detail.xlsx');
    }
}
