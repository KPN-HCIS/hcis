<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchNik(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $limit = 10; // Adjust as needed

        $employees = Employee::select('ktp', 'fullname')
            ->where(function ($query) use ($searchTerm) {
                $query->where('fullname', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('ktp', 'LIKE', '%' . $searchTerm . '%');
            })
            // ->where('employee_id', '!=', $request->input('employeeId')) // Exclude employee_id matching user_id
            ->limit($limit) // Limit the number of results returned
            ->get();

        return response()->json($employees);
    }

}
