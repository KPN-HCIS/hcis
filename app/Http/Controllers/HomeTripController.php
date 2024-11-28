<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dependents;
// use App\Models\HomeTrip;
// use App\Models\HomeTripPlan;
// use App\Models\HomeTripApproval;
use App\Models\Location;
use App\Models\Employee;
use App\Models\MasterBusinessUnit;
use Illuminate\Support\Facades\Auth;

class HomeTripController extends Controller
{
    public function homeTrip()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'asc')->where('employee_id', $employee_id)->get();
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';


        return view('hcis.reimbursements.homeTrip.homeTrip', compact( 'family', 'parentLink', 'link'));
    }

    public function homeTripAdmin(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'asc')->where('employee_id', $employee_id)->get();
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';

        $userRole = auth()->user()->roles->first();
        $roleRestriction = json_decode($userRole->restriction, true);
        $restrictedWorkAreas = $roleRestriction['work_area_code'] ?? [];
        $restrictedGroupCompanies = $roleRestriction['group_company'] ?? [];

        if (!empty($restrictedWorkAreas)) {
            $locations = Location::orderBy('area')->whereIn('work_area', $restrictedWorkAreas)->get();
        } else {
            $locations = Location::orderBy('area')->get();
        }

        $currentYear = date('Y');

        $query = Employee::with(['employee', 'statusReqEmployee', 'statusSettEmployee']);

        $ht_employee = collect();
        $hasFilter = false;

        if (request()->get('stat') == '') {
        } else {
            if ($request->has('stat') && $request->input('stat') !== '') {
                $status = $request->input('stat');
                $query->where('work_area_code', $status);
                $hasFilter = true;
            }
        }

        if (request()->get('customsearch') == '') {
        } else {
            if ($request->has('customsearch') && $request->input('customsearch') !== '') {
                $customsearch = $request->input('customsearch');
                if (!empty($restrictedWorkAreas)) {
                    $query->where('fullname', 'LIKE', '%' . $customsearch . '%')
                        ->where('work_area_code', $restrictedWorkAreas);
                } else {
                    $query->where('fullname', 'LIKE', '%' . $customsearch . '%');
                }
                $hasFilter = true;
            }
        }

        // Hanya jalankan query jika ada salah satu filter
        if ($hasFilter) {
            $ht_employee = $query->orderBy('created_at', 'desc')->get();
        }

        // $medical_plans = HealthPlan::where('period', $currentYear)->get();


        return view('hcis.reimbursements.homeTrip.admin.homeTripAdmin', compact( 
            'link', 
            'parentLink', 
            'ht_employee',
            'locations',
            // 'family',
        ));
    }

    public function homeTripAdminDetail(Request $request, $key)
    {
        // Gunakan findByRouteKey untuk mendekripsi $key
        // dd($key);
        $employee_id = decrypt($key);

        // Ambil data dependents, medical, dan medical_plan berdasarkan employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        // $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        // $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();
        // $medicalGroup = HealthCoverage::select(
        //     'no_medic',
        //     'date',
        //     'period',
        //     'hospital_name',
        //     'patient_name',
        //     'disease',
        //     'verif_by',
        //     'balance_verif',
        //     'approved_by',
        //     DB::raw('SUM(CASE WHEN medical_type = "Maternity" THEN balance ELSE 0 END) as maternity_total'),
        //     DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance ELSE 0 END) as inpatient_total'),
        //     DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance ELSE 0 END) as outpatient_total'),
        //     DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance ELSE 0 END) as glasses_total'),
        //     'status',
        //     DB::raw('MAX(created_at) as latest_created_at')

        // )
        //     ->where('employee_id', $employee_id)
        //     ->where('status', '!=', 'Draft')
        //     ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status', 'verif_by', 'balance_verif', 'approved_by')
        //     ->orderBy('latest_created_at', 'desc')
        //     ->get();

        // $medicalGroup->map(function ($item) {
        //     $approvedEmployee = Employee::where('employee_id', $item->approved_by)->first();

        //     $item->approved_by_fullname = $approvedEmployee ? $approvedEmployee->fullname : null;
        //     $item->total_per_no_medic = $item->maternity_total + $item->inpatient_total + $item->outpatient_total + $item->glasses_total;
        //     return $item;
        // });

        $bissnisUnit = Employee::where('employee_id', $employee_id)
            ->pluck('group_company');
        $gaApproval = MasterBusinessUnit::where('nama_bisnis', $bissnisUnit)
            ->first();
        $gaFullname = Employee::where('employee_id', $gaApproval->approval_medical)
            ->pluck('fullname');

        // $rejectMedic = HealthCoverage::where('employee_id', $employee_id)
        //     ->where('status', 'Rejected')  // Filter for rejected status
        //     ->get()
        //     ->keyBy('no_medic');

        // Get employee IDs from both 'employee_id' and 'rejected_by'
        // $employeeIds = $rejectMedic->pluck('employee_id')->merge($rejectMedic->pluck('rejected_by'))->unique();

        // Fetch employee names for those IDs
        // $employees = Employee::whereIn('employee_id', $employeeIds)
        //     ->pluck('fullname', 'employee_id');

        // Now map the full names to the respective HealthCoverage records
        // $rejectMedic->transform(function ($item) use ($employees) {
        //     $item->employee_fullname = $employees->get($item->employee_id);
        //     $item->rejected_by_fullname = $employees->get($item->rejected_by);
        //     return $item;
        // });

        // dd($rejectMedic);
        // $medical = $medicalGroup->map(function ($item) use ($employee_id) {
        //     // Fetch the usage_id based on no_medic
        //     $usageId = HealthCoverage::where('no_medic', $item->no_medic)
        //         ->where('employee_id', $employee_id)
        //         ->value('usage_id'); // Assuming there's one usage_id per no_medic

        //     // Add usage_id to the current item
        //     $item->usage_id = $usageId;

        //     return $item;
        // });

        // $master_medical = MasterMedical::where('active', 'T')->get();

        // Format data medical_plan
        // $formatted_data = [];
        // foreach ($medical_plan as $plan) {
        //     $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        // }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        // Kirim data ke view
        return view('hcis.reimbursements.homeTrip.admin.homeTripDetailAdmin', compact(
            'family', 
            // 'medical_plan', 
            // 'medical', 
            'parentLink', 
            'link', 
            // 'rejectMedic', 
            // 'employees', 
            'employee_id', 
            // 'master_medical', 
            // 'formatted_data', 
            // 'medicalGroup', 
            // 'gaFullname'
        ));
    }
}
