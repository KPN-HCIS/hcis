<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    function employee() {
        $link = 'employee';
        $employees = employee::all();
        return view('pages.employees.employee', [
            'link' => $link,
            'employees' => $employees,
        ]);
    }
}
