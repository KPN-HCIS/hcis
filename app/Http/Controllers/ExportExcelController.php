<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
{
    public function export() 
    {
        return Excel::download(new EmployeeExport, 'employees.xlsx');
    }
}
