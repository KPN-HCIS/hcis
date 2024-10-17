<?php

namespace App\Http\Controllers;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use App\Models\MasterMedical;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MedicalController extends Controller
{
    public function medical()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'medical', 'parentLink', 'link'));
    }
    public function medicalForm()
    {
        $employee_id = Auth::user()->employee_id;
        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical_type = MasterMedical::orderBy('id', 'desc')->get();

        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();

        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();
        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact('diseases', 'medical_type', 'families', 'parentLink', 'link', 'employee_name'));
    }
    public function medicalFormUpdate($id)
    {
        $employee_id = Auth::user()->employee_id;
        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medic = HealthCoverage::findOrFail($id);

        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();

        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();
        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalEditForm', compact('diseases', 'families', 'parentLink', 'link', 'employee_name', 'medic'));
    }

    public function medicalCreate(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        $medic = new HealthCoverage();
        $medic->id = (string) Str::uuid();

        // Get the latest health plan for the employee in the current year
        $medical_plan = HealthPlan::orderBy('period', 'desc')
            ->where('employee_id', $employee_id)
            ->whereYear('period', now()->year)
            ->first();

        // Handle status value
        $statusValue = $request->has('action_draft') ? 'Draft' : 'Pending';

        // Handle medical proof file upload
        $medical_proof_path = null;
        if ($request->hasFile('medical_proof')) {
            $file = $request->file('medical_proof');
            $medical_proof_path = $file->store('public/storage/proofs'); // Store file and get the path
        }

        // dd($medical_proof_path);
        // Format inputs
        $glasses = (int) str_replace('.', '', $request->glasses);
        $childBirth = (int) str_replace('.', '', $request->child_birth);
        $inpatient = (int) str_replace('.', '', $request->inpatient);
        $outpatient = (int) str_replace('.', '', $request->outpatient);

        // Initialize uncovered amounts
        $uncoveredInpatient = $uncoveredOutpatient = $uncoveredGlasses = $uncoveredChildBirth = 0;

        // Update balances and calculate uncovered amounts
        if ($statusValue !== 'Draft' && $medical_plan) {
            // Inpatient
            $medical_plan->inpatient_balance -= $inpatient;
            $uncoveredInpatient = $medical_plan->inpatient_balance < 0 ? abs($medical_plan->inpatient_balance) : 0;

            // Outpatient
            $medical_plan->outpatient_balance -= $outpatient;
            $uncoveredOutpatient = $medical_plan->outpatient_balance < 0 ? abs($medical_plan->outpatient_balance) : 0;

            // Glasses
            $medical_plan->glasses_balance -= $glasses;
            $uncoveredGlasses = $medical_plan->glasses_balance < 0 ? abs($medical_plan->glasses_balance) : 0;

            // Child Birth
            $medical_plan->child_birth_balance -= $childBirth;
            $uncoveredChildBirth = $medical_plan->child_birth_balance < 0 ? abs($medical_plan->child_birth_balance) : 0;

            // Save updated health plan balances
            $medical_plan->save();
        }

        // Calculate total uncovered amounts
        $totalUncovered = $uncoveredInpatient + $uncoveredOutpatient + $uncoveredGlasses + $uncoveredChildBirth;
        $totalCoverage = $glasses + $childBirth + $inpatient + $outpatient;

        $date = Carbon::parse($request->date);
        $period = $date->year;

        // Save data to HealthCoverage
        HealthCoverage::create([
            'usage_id' => $medic->id,
            'employee_id' => $employee_id,
            'no_medic' => $this->generateNoMedic(),
            'no_invoice' => $request->no_invoice,
            'hospital_name' => $request->hospital_name,
            'patient_name' => $request->patient_name,
            'disease' => $request->disease,
            'date' => $date,
            'coverage_detail' => $request->coverage_detail,
            'period' => $period,
            'glasses' => $glasses,
            'child_birth' => $childBirth,
            'inpatient' => $inpatient,
            'outpatient' => $outpatient,
            'total_coverage' => $totalCoverage,

            // Uncovered amounts (always positive)
            'glasses_uncover' => max(0, $uncoveredGlasses),
            'child_birth_uncover' => max(0, $uncoveredChildBirth),
            'inpatient_uncover' => max(0, $uncoveredInpatient),
            'outpatient_uncover' => max(0, $uncoveredOutpatient),
            'total_uncoverage' => $totalUncovered,

            // Others
            'status' => $statusValue,
            'medical_proof' => $medical_proof_path,
        ]);

        return redirect()->route('medical')->with('success', 'Medical Successfully Added');
    }

    public function medicalDelete($id)
    {
        // Find the business trip by ID
        $medical = HealthCoverage::findOrFail($id);
        $medical->delete();

        // Redirect back with a success message
        return redirect()->route('medical')->with('success', 'Medical Draft Deleted');
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

}
