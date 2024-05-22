<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GoalExport;
use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    function index() {
        $link = 'reports';

        $locations = Location::select('company_name', 'area', 'work_area')->orderBy('area')->get();
        $groupCompanies = Location::select('company_name')
        ->orderBy('company_name')
        ->distinct()
        ->pluck('company_name');
        $companies = Company::select('contribution_level', 'contribution_level_code')->orderBy('contribution_level_code')->get();

        return view('reports.admin.app', compact('locations', 'companies', 'groupCompanies'),  [
            'link' => $link
        ]);
    }

    public function changesGroupCompany(Request $request)
    {
        $selectedGroupCompany = $request->input('groupCompany');

        // Initialize query to fetch locations
        $locationsQuery = Location::query();

        // Check if a specific group company is selected
        if ($selectedGroupCompany) {
            // Filter locations by the selected group company
            $locationsQuery->where('company_name', $selectedGroupCompany);
        }

        // Fetch locations based on the modified query
        $locations = $locationsQuery->get();

        // Return JSON response with locations
        return response()->json([
            'locations' => $locations,
        ]);
    }
    
    public function getReportContent(Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;
        $report_type = $request->report_type;
        $group_company = $request->input('group_company');
        $location = $request->input('location');
        $company = $request->input('company');

        $filters = compact('report_type', 'group_company', 'location', 'company');

        // Start building the query
        if ($report_type === 'Goal') {
            $query = ApprovalRequest::with(['employee', 'manager', 'goal', 'initiated']);

            if ($group_company) {
                $query->whereHas('employee', function ($query) use ($group_company) {
                    $query->where('group_company', $group_company)->orderBy('fullname');
                });
            }
            if ($location) {
                $query->whereHas('employee', function ($query) use ($location) {
                    $query->where('work_area_code', $location);
                });
            }
            if ($company) {
                $query->whereHas('employee', function ($query) use ($company) {
                    $query->where('contribution_level_code', $company);
                });
            }

            // Apply employee filters
            $data = $query->get();
            $route = 'reports.admin.goal';
        } elseif ($report_type === 'Employee') {
            $query = Employee::query()->orderBy('fullname'); // Start with Employee model

            if ($group_company) {
                    $query->where('group_company', $group_company)->orderBy('fullname');
            }
            if ($location) {
                    $query->where('work_area_code', $location);
            }
            if ($company) {
                    $query->where('contribution_level_code', $company);
            }

            $data = $query->get();
            foreach ($data as $employee) {
                $employee->access_menu = json_decode($employee->access_menu, true);
            }
            $route = 'reports.admin.employee';
        } else {
            $data = collect(); // Empty collection for unknown report types
            return false;
        }


        $link = 'reports';

        return view($route, compact('data', 'link', 'filters'));
    }


    public function generateReportExcel(Request $request)
    {
        // Logika untuk generate report
        
        $reportType = $request->export_report_type;
        $groupCompany = $request->export_group_company;
        $company = $request->export_company;
        $location = $request->export_location;

        $directory = 'report/excel'; // Direktori tempat file akan disimpan
        $date = now()->format('dmY');
        $reportName = 'Nama Report';
        $fileName = $reportType.'_'.$date.'.xlsx'; // Nama file yang akan disimpan

        if($reportType==='Goal'){
            $export = new GoalExport($groupCompany, $location, $company);
            $fileContent = Excel::download($export, $fileName)->getFile();
        }
        return false;

        // Mengecek dan membuat direktori jika belum ada
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true); // Buat direktori dengan izin 0755 (opsional)
        }

        // Menyimpan file ke dalam direktori yang sudah ada
        Storage::disk('public')->put($directory . '/' . $fileName, $fileContent);

        // Simpan informasi report ke dalam database
        $filePath = $directory . '/' . $fileName;
        $report = new Report();
        $report->name = $reportName;
        $report->file_path = $filePath;
        $report->save();

        return redirect()->back()->with('success', 'Report berhasil di-generate dan disimpan.');
    }

}
