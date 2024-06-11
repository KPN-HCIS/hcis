<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Models\Company;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    function index() {
        $link = 'Reports';

        $user = Auth::user()->employee_id;

        $locations = Location::select('company_name', 'area', 'work_area')->orderBy('area')->get();
        $groupCompanies = Location::select('company_name')
        ->orderBy('company_name')
        ->distinct()
        ->pluck('company_name');
        $companies = Company::select('contribution_level', 'contribution_level_code')->orderBy('contribution_level_code')->get();

        $selectYear = ApprovalRequest::where('employee_id', $user)->select('created_at')->get();

        $selectYear->transform(function ($req) {
            $req->year = Carbon::parse($req->created_at)->format('Y');
            return $req;
        });

        return view('reports.app', compact('locations', 'companies', 'groupCompanies', 'selectYear'),  [
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
            $locationsQuery->whereIn('company_name', $selectedGroupCompany);
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
        // Get the authenticated user's employee_id
        $user = Auth::user();

        $filterYear = $request->input('filterYear') ? $request->input('filterYear') : now()->year;

        $employeeId = $user->employee_id;
        $report_type = $request->report_type;
        $group_company = $request->input('group_company');
        $location = $request->input('location');
        $company = $request->input('company');

        $filters = compact('report_type', 'group_company', 'location', 'company');

        // Start building the query
        $query = ApprovalRequest::with(['employee', 'manager', 'goal', 'initiated']);

        // Apply filters based on request parameters
        if ($request->filled('group_company')) {
            $query->whereHas('employee', function ($query) use ($group_company) {
                $query->whereIn('group_company', $group_company);
            });
        }
        if ($request->filled('location')) {
            $query->whereHas('employee', function ($query) use ($location) {
                $query->whereIn('work_area_code', $location);
            });
        }

        if ($request->filled('company')) {
            $query->whereHas('employee', function ($query) use ($company) {
                $query->whereIn('contribution_level_code', $company);
            });
        }

        $query->whereYear('created_at', $filterYear);
        
        // Fetch the data based on the constructed query
        $data = $query->get();
        $data->map(function($item) {
            // Format created_at
            $createdDate = Carbon::parse($item->created_at);

                $item->formatted_created_at = $createdDate->format('d M Y');

            // Format updated_at
            $updatedDate = Carbon::parse($item->updated_at);

                $item->formatted_updated_at = $updatedDate->format('d M Y');
        });

        // Determine the report type and return the appropriate view
        if ($report_type === 'Goal') {
            return view('reports.goal', compact('data'))->render();
        } else {
            return view('reports.empty')->render();
        }
    }

}
