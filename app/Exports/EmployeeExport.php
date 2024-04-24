<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class EmployeeExport implements FromView
{
    // /** Collection
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     return Employee::all();
    // }

    // /** View
    public function view(): View
    {
        return view('exports.employee', [
            'employees' => Employee::all()
        ]);
    }
    
}
