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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Imports\ImportHealthCoverage;
use App\Exports\MedicalExport;


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
            ->where('status', 'Rejected')  // Filter for rejected status
            ->select('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'reject_info')
            ->get();
        $rejectMedic = $rejectMedic->keyBy('no_medic');

        $employeeName = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')  // Filter for rejected status
            ->select('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'reject_info')
            ->get();
        $employeeName = $employeeName->keyBy('no_medic');

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

        $master_medical = MasterMedical::all();

        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employeeName', 'master_medical', 'formatted_data'));
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

        // Iterate through each medical type and update the health plan balance
        foreach ($medical_costs as $medical_type => $cost) {
            $cost = (int) str_replace('.', '', $cost); // Clean the currency format

            // Fetch the specific health plan for the employee and medical type
            $medical_plan = HealthPlan::where('employee_id', $employee_id)
                ->where('period', $period)
                ->where('medical_type', $medical_type)
                ->first();

            if (!$medical_plan) {
                continue; // If no health plan found, skip to the next medical type
            }

            // Update the balance if the status is not 'Draft'
            if ($statusValue !== 'Draft') {
                $medical_plan->balance -= $cost;
                // dd($medical_plan->balance -= $cost);
                $medical_plan->save();
            }

            // Create the HealthCoverage entry for each medical type
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
        $link = 'Add Medical Coverage Usage';

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
        Log::info("Received medical_costs: " . json_encode($medical_costs));
        $date = Carbon::parse($request->date);
        $period = $date->year;

        // Fetch all existing health coverages for this no_medic
        $existingCoverages = HealthCoverage::where('no_medic', $no_medic)->get();
        Log::info("Existing coverages: " . $existingCoverages->pluck('medical_type')->implode(', '));

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
                // dd( $existingCoverage->balance, $oldCost );
                $costDifference = $cost - $oldCost;
                // dd( $costDifference );

                if ($statusValue !== 'Draft') {
                    $medical_plan->balance -= $costDifference;
                    // dd( $medical_plan->balance -= $costDifference);
                    // dd( $statusValue);
                    $medical_plan->save();
                }

                $existingCoverage->update([
                    'balance' => $cost,
                    'balance_uncoverage' => ($medical_plan->balance < 0) ? abs($medical_plan->balance) : 0,
                ]);
                Log::info("Updated existing coverage for medical_type: $medical_type, new balance: $cost");
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

        return view('hcis.reimbursements.medical.admin.medicalAdminForm', compact('selectedDisease', 'balanceMapping', 'medic', 'medical_type', 'diseases', 'families', 'parentLink', 'link', 'employee_name', 'medicGroup', 'selectedMedicalTypes'));
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
        Log::info("Received medical_costs for verification: " . json_encode($medical_costs));

        // Fetch all existing health coverages for this no_medic
        $existingCoverages = HealthCoverage::where('no_medic', $no_medic)->get();
        Log::info("Existing coverages: " . $existingCoverages->pluck('medical_type')->implode(', '));

        // Update the medical proof and common fields (if needed)
        $commonUpdateData = [
            'medical_proof' => $medical_proof_path ?? $existingMedical->medical_proof,
        ];

        HealthCoverage::where('no_medic', $no_medic)->update($commonUpdateData);

        // Process each medical type and update balance_verif
        foreach ($medical_costs as $medical_type => $verif_cost) {
            $verif_cost = (int) str_replace('.', '', $verif_cost); // Clean the currency format

            // Find existing coverage for this medical type
            $existingCoverage = $existingCoverages->where('medical_type', $medical_type)->first();

            if ($existingCoverage) {
                // Update only the balance_verif for existing coverage
                $existingCoverage->update([
                    'balance_verif' => $verif_cost,
                    'verif_by' => $employee_id,
                    'status' => 'Pending',
                ]);
                Log::info("Updated balance_verif for medical_type: $medical_type, new balance_verif: $verif_cost");
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

        // Fetch grouped medical data for all employees, filtered by verif_by and balance_verif being null
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

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

        // Fetch medical plans for all employees
        $medical_plan = HealthPlan::orderBy('period', 'desc')->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical Approval';

        return view('hcis.reimbursements.medical.approval.medicalApproval', compact('family', 'medical_plan', 'medical', 'parentLink', 'link'));
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
        $medical = HealthCoverage::findOrFail($id);

        // Determine the new status based on the action
        $action = $request->input('status_approval');
        $rejectInfo = $request->input('reject_info');

        if ($action == 'Rejected') {
            $statusValue = 'Rejected';

            // Update all records with the same 'no_medic' to 'Rejected'
            HealthCoverage::where('no_medic', $medical->no_medic)->update([
                'status' => $statusValue,
                'reject_info' => $rejectInfo,
            ]);

            return redirect()->route('medical.approval')->with('success', 'Medical request rejected.');
        } elseif ($action == 'Done') {
            $statusValue = 'Done';

            // Fetch all records with the same 'no_medic'
            $healthCoverages = HealthCoverage::where('no_medic', $medical->no_medic)->get();

            // Loop through each health coverage record and update accordingly
            foreach ($healthCoverages as $coverage) {
                $medicalType = $coverage->medical_type;
                $balanceVerif = $coverage->balance_verif;
                $employeeId = $coverage->employee_id;  // Use employee_id from the current data

                // Fetch the health plan for this employee and medical type
                $healthPlan = HealthPlan::where('employee_id', $employeeId)
                    ->where('medical_type', $medicalType)
                    ->first();

                if ($healthPlan) {
                    // Deduct the verified balance from the health plan for this specific medical type
                    $healthPlan->balance -= $balanceVerif;
                    $healthPlan->save();
                }

                // Update the medical record to mark it as done and store verification info
                $coverage->update([
                    'status' => $statusValue,
                    'verif_by' => Auth::user()->employee_id,  // Verifying by the current user
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
            'master_medical' => MasterMedical::all(),
            'balances' => $balances, // Kirim balances ke view
        ]);
    }

    public function medicalDetail(Request $request, $key)
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
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('latest_created_at', 'desc')
            ->get();

        $rejectMedic = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')  // Filter for rejected status
            ->select('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'reject_info')
            ->get();
        $rejectMedic = $rejectMedic->keyBy('no_medic');

        $employeeName = HealthCoverage::where('employee_id', $employee_id)
            ->where('status', 'Rejected')  // Filter for rejected status
            ->select('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'reject_info')
            ->get();
        $employeeName = $employeeName->keyBy('no_medic');

        $medical = $medicalGroup->map(function ($item) use ($employee_id) {
            // Fetch the usage_id based on no_medic
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)
                ->where('employee_id', $employee_id)
                ->value('usage_id'); // Assuming there's one usage_id per no_medic

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

        $master_medical = MasterMedical::all();

        // Format data medical_plan
        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        // Kirim data ke view
        return view('hcis.reimbursements.medical.admin.medicalAdmin', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employeeName', 'master_medical', 'formatted_data'));
    }

    public function importExcel(Request $request)
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

    public function exportExcel(Request $request)
    {
        $stat = $request->input('stat');
        $customSearch = $request->input('customsearch');

        return Excel::download(new MedicalExport($stat, $customSearch), 'medical_report.xlsx');
    }
}
