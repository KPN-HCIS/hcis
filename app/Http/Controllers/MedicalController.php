<?php

namespace App\Http\Controllers;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use App\Models\MasterMedical;
use App\Models\Company;
use App\Models\Location;
use App\Models\MasterPlafond;
use App\Models\MasterBusinessUnit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Imports\ImportHealthCoverage;
use App\Exports\MedicalExport;
use App\Exports\MedicalDetailExport;


class MedicalController extends Controller
{
    protected $permissionLocations;
    protected $permissionCompanies;
    protected $permissionGroupCompanies;

    public function medical()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();
        $medicalGroup = HealthCoverage::select(
            'no_medic',
            'date',
            'period',
            'hospital_name',
            'patient_name',
            'disease',
            DB::raw('SUM(CASE WHEN medical_type = "Child Birth" THEN balance ELSE 0 END) as child_birth_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance ELSE 0 END) as inpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance ELSE 0 END) as outpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance ELSE 0 END) as glasses_total'),
            'status',
            DB::raw('MAX(created_at) as latest_created_at')

        )
            ->where('employee_id', $employee_id)
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('latest_created_at', 'desc')
            ->get();

        $rejectMedic = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')
            ->get()
            ->keyBy('no_medic');

        $employeeIds = $rejectMedic->pluck('employee_id')->merge($rejectMedic->pluck('rejected_by'))->unique();

        $employees = Employee::whereIn('employee_id', $employeeIds)
            ->pluck('fullname', 'employee_id');

        $rejectMedic->transform(function ($item) use ($employees) {
            $item->employee_fullname = $employees->get($item->employee_id);
            $item->rejected_by_fullname = $employees->get($item->rejected_by);
            return $item;
        });

        $medical = $medicalGroup->map(function ($item) use ($employee_id) {
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)
                ->where('employee_id', $employee_id)
                ->value('usage_id');

            $item->usage_id = $usageId;

            return $item;
        });

        $master_medical = MasterMedical::where('active', 'T')->get();

        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $employees_cast = Employee::where('employee_id', $employee_id)->get();
        $currentYear = date('Y');

        foreach ($employees_cast as $employee) {
            $startDate = $employee->date_of_joining;
            $job_level = $employee->job_level;
            $endDate = date('Y-12-31');

            $startDate = date_create($startDate);
            $endDate = date_create($endDate);
            $difference = date_diff($startDate, $endDate);
            $yearsWorked = $difference->y;

            $plafond_list = MasterPlafond::where('group_name', $job_level)->get();

            if ($yearsWorked > 0) {
                foreach ($plafond_list as $plafond_lists) {
                    $existingHealthPlan = HealthPlan::where('employee_id', $employee->employee_id)
                        ->where('period', $currentYear)
                        ->where('medical_type', $plafond_lists->medical_type)
                        ->first();

                    if (!$existingHealthPlan) {
                        $newHealthPlan = HealthPlan::create([
                            'plan_id' => (string) Str::uuid(),
                            'employee_id' => $employee->employee_id,
                            'medical_type' => $plafond_lists->medical_type,
                            'balance' => $plafond_lists->balance,
                            'period' => $currentYear,
                            'created_by' => $employee_id,
                            'created_at' => now(),
                        ]);

                        if ($newHealthPlan) {
                            session()->flash('refresh', true);
                        }
                    }
                }
            } elseif ($yearsWorked == 0) {
                $startDate = date_create($employee->date_of_joining);
                $bulan_awal = date_format($startDate, "m");
                $bulan_akhir = date('m');
                $bulan = ($bulan_akhir - $bulan_awal) + 1;

                foreach ($plafond_list as $plafond_lists) {
                    $balance = 0;

                    $existingHealthPlan = HealthPlan::where('employee_id', $employee->employee_id)
                        ->where('period', $currentYear)
                        ->where('medical_type', $plafond_lists->medical_type)
                        ->first();

                    if (!$existingHealthPlan) {
                        if ($plafond_lists->medical_type == 'Child Birth') {
                            $balance = $plafond_lists->balance * ($bulan / 12);
                        } elseif ($plafond_lists->medical_type == 'Inpatient') {
                            $balance = $plafond_lists->balance * ($bulan / 12);
                        } elseif ($plafond_lists->medical_type == 'Outpatient') {
                            $balance = $plafond_lists->balance * ($bulan / 12);
                        } elseif ($plafond_lists->medical_type == 'Glasses') {
                            $balance = $plafond_lists->balance * ($bulan / 12);
                        }

                        $newHealthPlan = HealthPlan::create([
                            'plan_id' => (string) Str::uuid(),
                            'employee_id' => $employee->employee_id,
                            'medical_type' => $plafond_lists->medical_type,
                            'balance' => $balance,
                            'period' => $currentYear,
                            'created_by' => $employee_id,
                            'created_at' => now(),
                        ]);

                        if ($newHealthPlan) {
                            session()->flash('refresh', true);
                        }
                    }
                }
            }
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employees', 'master_medical', 'formatted_data'));
    }

    public function medicalForm()
    {
        $employee_id = Auth::user()->employee_id;
        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical_type = MasterMedical::orderBy('id', 'desc')->where(
            'active',
            'T'
        )->get();

        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();

        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();
        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact('diseases', 'medical_type', 'families', 'parentLink', 'link', 'employee_name'));
    }

    public function medicalCreate(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        $no_medic = $this->generateNoMedic();

        $statusValue = $request->has('action_draft') ? 'Draft' : 'Pending';

        $medical_proof_path = null;
        if ($request->hasFile('medical_proof')) {
            $file = $request->file('medical_proof');
            $medical_proof_path = $file->store('public/storage/proofs');
        }

        $medical_costs = $request->input('medical_costs', []);
        $date = Carbon::parse($request->date);
        $period = $date->year;

        foreach ($medical_costs as $medical_type => $cost) {
            $cost = (int) str_replace('.', '', $cost);

            $medical_plan = HealthPlan::where('employee_id', $employee_id)
                ->where('period', $period)
                ->where('medical_type', $medical_type)
                ->first();

            if (!$medical_plan) {
                continue;
            }

            if ($statusValue !== 'Draft') {
                $medical_plan->balance -= $cost;
                $medical_plan->save();
            }

            HealthCoverage::create([
                'usage_id' => (string) Str::uuid(),
                'employee_id' => $employee_id,
                'no_medic' => $no_medic,
                'no_invoice' => $request->no_invoice,
                'hospital_name' => $request->hospital_name,
                'patient_name' => $request->patient_name,
                'disease' => $request->disease,
                'date' => $date,
                'coverage_detail' => $request->coverage_detail,
                'period' => $period,
                'medical_type' => $medical_type,
                'balance' => $cost,
                'created_by' => $employee_id,
                'balance_uncoverage' => ($medical_plan->balance < 0) ? abs($medical_plan->balance) : 0,
                'status' => $statusValue,
                'medical_proof' => $medical_proof_path,
            ]);
        }

        return redirect()->route('medical')->with('success', 'Medical successfully added.');
    }


    public function medicalFormUpdate($id)
    {
        $employee_id = Auth::user()->employee_id;

        // Fetch the HealthCoverage record by ID
        $medic = HealthCoverage::findOrFail($id);
        $medical_type = MasterMedical::orderBy('id', 'desc')->where(
            'active',
            'T'
        )->get();

        // Find all records with the same no_medic (group of medical types)
        $medicGroup = HealthCoverage::where('no_medic', $medic->no_medic)
            ->where('employee_id', $employee_id)
            ->get();

        // Extract the medical types from medicGroup
        $selectedMedicalTypes = $medicGroup->pluck('medical_type')->unique();
        $balanceMapping = $medicGroup->pluck('balance', 'medical_type');
        $selectedDisease = $medic->disease;

        // Fetch related data as before
        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $employee_name = Employee::select('fullname')->where('employee_id', $employee_id)->first();
        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();

        $parentLink = 'Medical';
        $link = 'Edit Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalEditForm', compact('selectedDisease', 'balanceMapping', 'medic', 'medical_type', 'diseases', 'families', 'parentLink', 'link', 'employee_name', 'medicGroup', 'selectedMedicalTypes'));
    }

    public function medicalUpdate(Request $request, $id)
    {
        $employee_id = Auth::user()->employee_id;
        $existingMedical = HealthCoverage::where('usage_id', $id)->first();

        if (!$existingMedical) {
            return redirect()->route('medical')->with('error', 'Medical record not found.');
        }

        $no_medic = $existingMedical->no_medic;

        // Handle status value
        $statusValue = $request->has('action_draft') ? 'Draft' : 'Pending';

        // Handle medical proof file upload
        $medical_proof_path = null;
        if ($request->hasFile('medical_proof')) {
            $file = $request->file('medical_proof');
            $medical_proof_path = $file->store('public/storage/proofs');
        }

        $medical_costs = $request->input('medical_costs', []);
        $date = Carbon::parse($request->date);
        $period = $date->year;

        // Fetch all existing health coverages for this no_medic
        $existingCoverages = HealthCoverage::where('no_medic', $no_medic)->get();

        // Update common fields for all records with the same no_medic
        $commonUpdateData = [
            'no_invoice' => $request->no_invoice,
            'hospital_name' => $request->hospital_name,
            'patient_name' => $request->patient_name,
            'disease' => $request->disease,
            'date' => $date,
            'coverage_detail' => $request->coverage_detail,
            'status' => $statusValue,
            'medical_proof' => $medical_proof_path ?? $existingMedical->medical_proof,
            'created_by' => $employee_id,
            'balance_verif' => null,
            'verif_by' => null,
            'reject_info' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ];

        HealthCoverage::where('no_medic', $no_medic)->update($commonUpdateData);

        // Process each medical type
        foreach ($medical_costs as $medical_type => $cost) {
            $cost = (int) str_replace('.', '', $cost); // Clean the currency format
            Log::info("Existing cost: " . $cost);

            // Fetch the specific health plan for the employee and medical type
            $medical_plan = HealthPlan::where('employee_id', $employee_id)
                ->where('period', $period)
                ->where('medical_type', $medical_type)
                ->first();

            if (!$medical_plan) {
                continue; // If no health plan found, skip to the next medical type
            }

            // Find existing coverage for this medical type
            $existingCoverage = $existingCoverages->where('medical_type', $medical_type)->first();

            if ($existingCoverage) {
                // Update balance for existing coverage
                $oldCost = $existingCoverage->balance;
                $costDifference = $cost - $oldCost;
                // dd($existingCoverage, $costDifference, $statusValue);

                if ($statusValue !== 'Draft') {
                    $medical_plan->balance -= $cost;
                    $medical_plan->save();
                }
                // dd($cost);
                $existingCoverage->update([
                    'balance' => $cost,
                    'balance_uncoverage' => ($medical_plan->balance < 0) ? abs($medical_plan->balance) : 0,
                ]);
            } else {
                // Create new coverage for new medical type
                HealthCoverage::create(array_merge($commonUpdateData, [
                    'usage_id' => (string) Str::uuid(),
                    'employee_id' => $employee_id,
                    'no_medic' => $no_medic,
                    'period' => $period,
                    'medical_type' => $medical_type,
                    'balance' => $cost,
                    'created_by' => $employee_id,
                    'balance_uncoverage' => ($medical_plan->balance < 0) ? abs($medical_plan->balance) : 0,
                    'balance_verif' => null,
                    'verif_by' => null,
                    'reject_info' => null,
                    'rejected_at' => null,
                    'rejected_by' => null,

                ]));

                if ($statusValue !== 'Draft') {
                    $medical_plan->balance -= $cost;
                    $medical_plan->save();
                }
            }
        }
        // dd($medical_plan->balance -= $cost);

        // Remove any coverages that are no longer present in the update
        $existingCoverages->whereNotIn('medical_type', array_keys($medical_costs))->each(function ($coverage) {
            $coverage->delete();
        });

        return redirect()->route('medical')->with('success', 'Medical data successfully updated.');
    }


    public function medicalDelete($id)
    {
        $medical = HealthCoverage::findOrFail($id);
        $noMedic = $medical->no_medic; // Get the no_medic value from the record
        HealthCoverage::where('no_medic', $noMedic)->delete();

        // Redirect back with a success message
        return redirect()->route('medical')->with('success', 'Medical Draft Deleted');
    }

    public function medicalAdminTable()
    {
        // Fetch all dependents, no longer filtered by employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->get();

        // Fetch grouped medical data for all employees
        $medicalGroup = HealthCoverage::select(
            'no_medic',
            'date',
            'period',
            'hospital_name',
            'patient_name',
            'disease',
            DB::raw('SUM(CASE WHEN medical_type = "Child Birth" THEN balance ELSE 0 END) as child_birth_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance ELSE 0 END) as inpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance ELSE 0 END) as outpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance ELSE 0 END) as glasses_total'),
            'status'
        )
            ->where('status', '!=', 'Draft')
            ->whereNull('verif_by')
            ->whereNull('balance_verif')
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add usage_id for each medical record without filtering by employee_id
        $medical = $medicalGroup->map(function ($item) {
            // Fetch the usage_id based on no_medic (for any employee)
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)->value('usage_id');

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

        // Fetch medical plans for all employees
        $medical_plan = HealthPlan::orderBy('period', 'desc')->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.admin.medicalAdmin', compact('family', 'medical_plan', 'medical', 'parentLink', 'link'));
    }


    public function medicalAdminForm($id)
    {
        // Fetch the HealthCoverage record by ID
        $medic = HealthCoverage::findOrFail($id);
        $medical_type = MasterMedical::orderBy('id', 'desc')->get();

        // Find all records with the same no_medic (group of medical types)
        $medicGroup = HealthCoverage::where('no_medic', $medic->no_medic)
            ->get();

        // Extract the medical types from medicGroup
        $selectedMedicalTypes = $medicGroup->pluck('medical_type')->unique();
        $balanceMapping = $medicGroup->pluck('balance', 'medical_type');
        $selectedDisease = $medic->disease;

        // Fetch related data as before
        $families = Dependents::orderBy('date_of_birth', 'desc')->get();
        $employee_name = Employee::select('fullname')->first();
        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();

        $parentLink = 'Medical (Admin)';
        $link = 'Medical Details';
        $key = 'key';

        return view('hcis.reimbursements.medical.admin.medicalAdminForm', compact('key', 'selectedDisease', 'balanceMapping', 'medic', 'medical_type', 'diseases', 'families', 'parentLink', 'link', 'employee_name', 'medicGroup', 'selectedMedicalTypes'));
    }

    public function medicalAdminUpdate(Request $request, $id)
    {
        $employee_id = Auth::user()->employee_id;
        $existingMedical = HealthCoverage::where('usage_id', $id)->first();

        if (!$existingMedical) {
            return redirect()->route('medical.admin')->with('error', 'Medical record not found.');
        }

        $no_medic = $existingMedical->no_medic;

        // Handle medical proof file upload (if needed)
        $medical_proof_path = null;
        if ($request->hasFile('medical_proof')) {
            $file = $request->file('medical_proof');
            $medical_proof_path = $file->store('public/storage/proofs');
        }

        // Process the medical verification costs
        $medical_costs = $request->input('medical_costs', []);
        $existingCoverages = HealthCoverage::where('no_medic', $no_medic)->get();

        // Update the medical proof and common fields (if needed)
        $commonUpdateData = [
            'medical_proof' => $medical_proof_path ?? $existingMedical->medical_proof,
        ];

        HealthCoverage::where('no_medic', $no_medic)->update($commonUpdateData);

        // Process each medical type and update balance_verif
        foreach ($medical_costs as $medical_type => $verif_cost) {
            $verif_cost = (int) str_replace('.', '', $verif_cost);
            $date = Carbon::parse($request->date);
            $period = $date->year;

            $medical_plan = HealthPlan::where('employee_id', $employee_id)
                ->where('period', $period)
                ->where('medical_type', $medical_type)
                ->first();

            if (!$medical_plan) {
                continue;
            }

            // Find existing coverage for this medical type
            $existingCoverage = $existingCoverages->where('medical_type', $medical_type)->first();

            if ($existingCoverage) {
                // Always update balance_verif, verif_by, and status
                $existingCoverage->update([
                    'balance_verif' => $verif_cost,
                    'verif_by' => $employee_id,
                    'status' => 'Pending',
                ]);
                if ($medical_plan->balance < $verif_cost) {
                    $balance_diff = $verif_cost - $medical_plan->balance;

                    // Update balance_uncoverage only if the balance is lower
                    $existingCoverage->update([
                        'balance_uncoverage' => $existingCoverage->balance_uncoverage + $balance_diff,
                    ]);

                    Log::info("Updated balance_uncoverage for medical_type: $medical_type, balance_diff: $balance_diff");
                } else {
                    Log::info("No uncoverage update needed for medical_type: $medical_type, HealthPlan balance is greater than or equal to verif_cost.");
                }
            } else {
                Log::info("No existing coverage found for medical_type: $medical_type");
            }
        }

        return redirect()->route('medical.admin')->with('success', 'Medical verification data successfully updated.');
    }


    public function medicalAdminDelete($id)
    {
        $medical = HealthCoverage::findOrFail($id);
        $noMedic = $medical->no_medic; // Get the no_medic value from the record
        HealthCoverage::where('no_medic', $noMedic)->delete();

        // Redirect back with a success message
        return redirect()->route('medical')->with('success', 'Medical Deleted');
    }

    public function medicalApproval()
    {
        // Fetch all dependents, no longer filtered by employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->get();
        $employeeId = auth()->user()->employee_id;
        $employee = Employee::where('employee_id', $employeeId)->first();

        // Check if the user has approval rights
        $hasApprovalRights = DB::table('master_bisnisunits')
            ->where('approval_medical', $employee->employee_id)
            ->where('nama_bisnis', $employee->group_company)
            ->exists();

        if ($hasApprovalRights) {
            $medicalGroup = HealthCoverage::select(
                'no_medic',
                'date',
                'period',
                'hospital_name',
                'patient_name',
                'disease',
                DB::raw('SUM(CASE WHEN medical_type = "Child Birth" THEN balance_verif ELSE 0 END) as child_birth_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance_verif ELSE 0 END) as inpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance_verif ELSE 0 END) as outpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance_verif ELSE 0 END) as glasses_balance_verif'),
                'status'
            )
                ->whereNotNull('verif_by')   // Only include records where verif_by is not null
                ->whereNotNull('balance_verif')
                ->where('status', 'Pending')
                ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
                ->orderBy('created_at', 'desc')
                ->get();

            // Add usage_id for each medical record without filtering by employee_id
            $medical = $medicalGroup->map(function ($item) {
                // Fetch the usage_id based on no_medic (for any employee)
                $usageId = HealthCoverage::where('no_medic', $item->no_medic)->value('usage_id');
                $item->usage_id = $usageId;

                return $item;
            });
        } else {
            $medical = collect(); // Empty collection if user doesn't have approval rights
        }

        // Fetch medical plans for all employees
        $medical_plan = HealthPlan::orderBy('period', 'desc')->get();
        $master_medical = MasterMedical::where('active', 'T')->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical Approval';

        return view('hcis.reimbursements.medical.approval.medicalApproval', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'master_medical'));
    }


    public function medicalApprovalForm($id)
    {
        // Fetch the HealthCoverage record by ID
        $medic = HealthCoverage::findOrFail($id);
        $medical_type = MasterMedical::orderBy('id', 'desc')->where(
            'active',
            'T'
        )->get();

        // Find all records with the same no_medic (group of medical types)
        $medicGroup = HealthCoverage::where('no_medic', $medic->no_medic)
            ->get();

        // Extract the medical types from medicGroup
        $selectedMedicalTypes = $medicGroup->pluck('medical_type')->unique();
        $balanceMapping = $medicGroup->pluck('balance_verif', 'medical_type');
        $selectedDisease = $medic->disease;

        // Fetch related data as before
        $families = Dependents::orderBy('date_of_birth', 'desc')->get();
        $employee_name = Employee::select('fullname')->first();
        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();

        $parentLink = 'Medical Approval';
        $link = 'Medical Details';

        return view('hcis.reimbursements.medical.approval.medicalApprovalDetail', compact('selectedDisease', 'balanceMapping', 'medic', 'medical_type', 'diseases', 'families', 'parentLink', 'link', 'employee_name', 'medicGroup', 'selectedMedicalTypes'));
    }

    public function medicalApprovalUpdate($id, Request $request)
    {
        // Find the medical record by ID
        $employee_id = Auth::user()->employee_id;
        $medical = HealthCoverage::findOrFail($id);

        // Determine the new status based on the action
        $action = $request->input('status_approval');
        $rejectInfo = $request->input('reject_info');

        if ($action == 'Rejected') {
            $statusValue = 'Rejected';

            // Fetch all records with the same 'no_medic'
            $healthCoverages = HealthCoverage::where('no_medic', $medical->no_medic)->get();

            // Loop through each health coverage record and update accordingly
            foreach ($healthCoverages as $coverage) {
                $medicalType = $coverage->medical_type;
                $balance = $coverage->balance;
                $employeeId = $coverage->employee_id;
                $date = Carbon::parse($request->date);
                $period = $date->year;

                // Fetch the health plan for this employee and medical type
                $healthPlan = HealthPlan::where('employee_id', $medical->employee_id)
                    ->where('medical_type', $medicalType)
                    ->where('period', $period)
                    ->first();
                // dd($healthPlan);

                if ($healthPlan) {
                    // Add the balance from the health coverage back to the health plan
                    $healthPlan->balance += $balance;
                    $healthPlan->save();
                }

                // Update the health coverage record to reflect rejection
                $coverage->update([
                    'status' => $statusValue,
                    'reject_info' => $rejectInfo,
                    'rejected_by' => $employee_id,
                    'rejected_at' => now(),
                ]);
            }

            return redirect()->route('medical.approval')->with('success', 'Medical request rejected and balances updated.');
        } elseif ($action == 'Done') {
            $statusValue = 'Done';

            // Fetch all records with the same 'no_medic'
            $healthCoverages = HealthCoverage::where('no_medic', $medical->no_medic)->get();

            // Loop through each health coverage record and update accordingly
            foreach ($healthCoverages as $coverage) {
                $medicalType = $coverage->medical_type;
                $balance = $coverage->balance;
                $balanceVerif = $coverage->balance_verif;
                $employeeId = $coverage->employee_id;
                $date = Carbon::parse($request->date);
                $period = $date->year;


                // Calculate the difference
                $balanceDifference = $balance - $balanceVerif;

                // Fetch the health plan for this employee and medical type
                $healthPlan = HealthPlan::where('employee_id', $employeeId)
                    ->where('medical_type', $medicalType)
                    ->where('period', $period)
                    ->first();

                if ($healthPlan) {
                    // If the result is positive, add the difference to the health plan balance
                    if ($balanceDifference > 0) {
                        $healthPlan->balance += $balanceDifference;
                    }
                    // If the result is negative or zero, subtract the absolute difference from the health plan balance
                    elseif ($balanceDifference < 0) {
                        $healthPlan->balance -= abs($balanceDifference);
                    }
                    $healthPlan->save();
                }
                // Update the medical record to mark it as done and store verification info
                $coverage->update([
                    'status' => $statusValue,
                    'verif_by' => Auth::user()->employee_id,
                    'approved_by' => $employee_id,
                    'approved_at' => now(),
                ]);
            }

            return redirect()->route('medical.approval')->with('success', 'Medical request approved and balances updated.');
        }
    }


    public function generateNoMedic()
    {
        $currentYear = date('y');
        // Fetch the last no_medic number
        $lastCoverage = HealthCoverage::withTrashed() // Include soft-deleted records
            ->orderBy('no_medic', 'desc')
            ->first();

        // Determine the next no_medic number
        if ($lastCoverage && substr($lastCoverage->no_medic, 2, 2) == $currentYear) {
            // Extract the last 6 digits (the sequence part) and increment it by 1
            $lastNumber = (int) substr($lastCoverage->no_medic, 4); // Extract the last 6 digits
            $nextNumber = $lastNumber + 1;
        } else {
            // If no records for this year or no records at all, start from 000001
            $nextNumber = 1;
        }

        // Format the next number as a 9-digit number starting with '6'
        $newNoMedic = 'MD' . $currentYear . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $newNoMedic;
    }

    public function medicalAdmin(Request $request)
    {
        $parentLink = 'Reimbursement';
        $link = 'Medical Data Employee';
        $userId = Auth::id();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();

        // Ambil tahun saat ini
        $currentYear = date('Y');

        // Inisialisasi query untuk karyawan
        $query = Employee::with(['employee', 'statusReqEmployee', 'statusSettEmployee']);

        // Inisialisasi variabel untuk menyimpan data yang akan dikirimkan ke view
        $med_employee = collect(); // Kosongkan med_employee jika tidak ada filter

        // Hanya ambil data jika request memiliki parameter 'stat' dan tidak kosong
        if ($request->has('stat') && $request->input('stat') !== '') {
            $status = $request->input('stat');
            $query->where('office_area', $status);

            // Eksekusi query untuk mendapatkan data yang difilter
            $med_employee = $query->orderBy('created_at', 'desc')->get();
        }

        // Ambil semua rencana kesehatan untuk tahun saat ini
        $medical_plans = HealthPlan::where('period', $currentYear)->get();

        // Format rencana kesehatan ke dalam array berdasarkan employee_id
        $balances = [];
        foreach ($medical_plans as $plan) {
            $balances[$plan->employee_id][$plan->medical_type] = $plan->balance;
        }

        // Siapkan nama lengkap (fullname) dan tanggal bergabung (date_of_joining)
        foreach ($med_employee as $transaction) {
            $transaction->ReqName = $transaction->statusReqEmployee ? $transaction->statusReqEmployee->fullname : '';
            $transaction->settName = $transaction->statusSettEmployee ? $transaction->statusSettEmployee->fullname : '';

            $employeeMedicalPlan = $medical_plans->where('employee_id', $transaction->employee_id)->first();
            $transaction->period = $employeeMedicalPlan ? $employeeMedicalPlan->period : '-';
        }

        return view('hcis.reimbursements.medical.adminMedical', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'med_employee' => $med_employee,
            'companies' => $companies,
            'locations' => $locations,
            'master_medical' => MasterMedical::where('active', 'T')->get(),
            'balances' => $balances, // Kirim balances ke view
        ]);
    }

    public function medicalAdminDetail(Request $request, $key)
    {
        // Gunakan findByRouteKey untuk mendekripsi $key
        $employee = Employee::findByRouteKey($key);

        // Ambil employee_id yang telah didekripsi
        $employee_id = $employee->employee_id;

        // Ambil data dependents, medical, dan medical_plan berdasarkan employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();
        $medicalGroup = HealthCoverage::select(
            'no_medic',
            'date',
            'period',
            'hospital_name',
            'patient_name',
            'disease',
            DB::raw('SUM(CASE WHEN medical_type = "Child Birth" THEN balance ELSE 0 END) as child_birth_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance ELSE 0 END) as inpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance ELSE 0 END) as outpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance ELSE 0 END) as glasses_total'),
            'status',
            DB::raw('MAX(created_at) as latest_created_at')

        )
            ->where('employee_id', $employee_id)
            ->where('status', '!=', 'Draft')
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('latest_created_at', 'desc')
            ->get();

        $rejectMedic = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')  // Filter for rejected status
            ->get()
            ->keyBy('no_medic');

        // Get employee IDs from both 'employee_id' and 'rejected_by'
        $employeeIds = $rejectMedic->pluck('employee_id')->merge($rejectMedic->pluck('rejected_by'))->unique();

        // Fetch employee names for those IDs
        $employees = Employee::whereIn('employee_id', $employeeIds)
            ->pluck('fullname', 'employee_id');

        // Now map the full names to the respective HealthCoverage records
        $rejectMedic->transform(function ($item) use ($employees) {
            $item->employee_fullname = $employees->get($item->employee_id);
            $item->rejected_by_fullname = $employees->get($item->rejected_by);
            return $item;
        });

        // dd($rejectMedic);
        $medical = $medicalGroup->map(function ($item) use ($employee_id) {
            // Fetch the usage_id based on no_medic
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)
                ->where('employee_id', $employee_id)
                ->value('usage_id'); // Assuming there's one usage_id per no_medic

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

        $master_medical = MasterMedical::where('active', 'T')->get();

        // Format data medical_plan
        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        // Kirim data ke view
        return view('hcis.reimbursements.medical.admin.medicalAdmin', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employees', 'employee_id', 'master_medical', 'formatted_data'));
    }
    public function medicalAdminConfirmation(Request $request, $key)
    {
        // Gunakan findByRouteKey untuk mendekripsi $key
        $employee = Employee::findByRouteKey($key);

        // Ambil employee_id yang telah didekripsi
        $employee_id = $employee->employee_id;

        // Ambil data dependents, medical, dan medical_plan berdasarkan employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();
        $medicalGroup = HealthCoverage::select(
            'no_medic',
            'date',
            'period',
            'hospital_name',
            'patient_name',
            'disease',
            DB::raw('SUM(CASE WHEN medical_type = "Child Birth" THEN balance ELSE 0 END) as child_birth_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance ELSE 0 END) as inpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance ELSE 0 END) as outpatient_total'),
            DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance ELSE 0 END) as glasses_total'),
            'status',
            DB::raw('MAX(created_at) as latest_created_at')

        )
            ->where('status', '!=', 'Draft')
            ->where('status', '!=', 'Done')
            ->whereNull('verif_by')
            ->whereNull('balance_verif')
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('latest_created_at', 'desc')
            ->get();

        $rejectMedic = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')  // Filter for rejected status
            ->get()
            ->keyBy('no_medic');

        // Get employee IDs from both 'employee_id' and 'rejected_by'
        $employeeIds = $rejectMedic->pluck('employee_id')->merge($rejectMedic->pluck('rejected_by'))->unique();

        // Fetch employee names for those IDs
        $employees = Employee::whereIn('employee_id', $employeeIds)
            ->pluck('fullname', 'employee_id');

        // Now map the full names to the respective HealthCoverage records
        $rejectMedic->transform(function ($item) use ($employees) {
            $item->employee_fullname = $employees->get($item->employee_id);
            $item->rejected_by_fullname = $employees->get($item->rejected_by);
            return $item;
        });

        // dd($rejectMedic);
        $medical = $medicalGroup->map(function ($item) use ($employee_id) {
            // Fetch the usage_id based on no_medic
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)
                ->where('employee_id', $employee_id)
                ->value('usage_id'); // Assuming there's one usage_id per no_medic

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

        $master_medical = MasterMedical::where('active', 'T')->get();

        // Format data medical_plan
        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        // Kirim data ke view
        return view('hcis.reimbursements.medical.admin.medicalAdminConfirmation', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employees', 'employee_id', 'master_medical', 'formatted_data'));
    }

    public function importAdminExcel(Request $request)
    {
        $userId = Auth::id();
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Mengimpor data menggunakan Maatwebsite Excel
        Excel::import(
            new ImportHealthCoverage,
            $request->file('file')
        );

        return redirect()->route('medical.admin')->with('success', 'Transaction successfully added From Excell.');
    }

    public function exportAdminExcel(Request $request)
    {
        $stat = $request->input('stat');
        $customSearch = $request->input('customsearch');

        return Excel::download(new MedicalExport($stat, $customSearch), 'medical_report.xlsx');
    }

    public function exportDetailExcel($employee_id)
    {
        return Excel::download(new MedicalDetailExport($employee_id), 'medical_detail.xlsx');
    }
}
