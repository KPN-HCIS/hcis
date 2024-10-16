<?php

namespace App\Http\Controllers;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalController extends Controller
{
    public function medical()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'parentLink', 'link'));
    }
    public function medicalForm()
    {
        $employee_id = Auth::user()->employee_id;
        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();

        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();

        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();
        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact('diseases', 'families', 'parentLink', 'link', 'employee_name'));
    }

    // public function exportExcel(Request $request)
    // {
    //     // Retrieve query parameters
    //     $startDate = $request->query('start-date');
    //     $endDate = $request->query('end-date');
    //     $division = $request->input('division'); // Get the division input

    //     // Initialize query builders
    //     $query = BusinessTrip::query();
    //     $queryCA = CATransaction::query();

    //     // Apply filters if both dates are present
    //     if ($startDate && $endDate) {
    //         $query->whereBetween('mulai', [$startDate, $endDate]);
    //     }

    //     // Apply division filter if it is selected
    //     if ($division) {
    //         $query->where('divisi', 'LIKE', '%' . $division . '%');
    //     }
    //     // Exclude drafts
    //     $query->where(function ($subQuery) {
    //         $subQuery->where('status', '<>', 'draft')
    //             ->where('status', '<>', 'declaration draft'); // Adjust if 'declaration draft' is the exact status name
    //     });
    //     $queryCA->where('approval_status', '<>', 'draft'); // Adjust 'status' and 'draft' as needed

    //     // Fetch the filtered BusinessTrip data
    //     $medical = $query->get();

    //     // Extract the no_sppd values from the filtered BusinessTrip records
    //     $noSppds = $medical->pluck('no_sppd')->unique();

    //     // Fetch CA data where no_sppd matches the filtered BusinessTrip records

    //     // Pass the filtered data to the export class
    //     return Excel::download(new MedicalExport($medical), 'Medical_Data.xlsx');
    // }

}
