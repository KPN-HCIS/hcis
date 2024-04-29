<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeDetailExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
{
    public function export() 
    {
        return Excel::download(new EmployeeExport, 'employees.xlsx');
    }
    public function exportreportemp() 
    {
        return Excel::download(new EmployeeDetailExport, 'employees_detail.xlsx');
    }
}
