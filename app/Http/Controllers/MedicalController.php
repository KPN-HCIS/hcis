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

        $medical_costs = $request->input('medical_costs', []);
        // dd($medical_costs);

        $date = Carbon::parse($request->date);
        $period = $date->year;

        // Save data to HealthCoverage
        // Iterate through each medical type and save
        foreach ($medical_costs as $medical_type => $cost) {
            $cost = (int) str_replace('.', '', $cost); // Clean the currency format
            $medical_type_lower = strtolower($medical_type); // Convert medical type to lowercase for comparison

            // Fetch the specific health plan for the employee and medical type
            $medical_plan = HealthPlan::where('employee_id', $employee_id)
                ->whereYear('period', now()->year)
                ->first();

            if (!$medical_plan) {
                continue; // If no medical plan found, skip to the next medical type
            }

            // Use dynamic column names for the medical type balance fields
            $balance_field = $medical_type_lower . '_balance';
            $balance_uncoverage = 0;

            // Check if the balance field exists in the health plan
            if (isset($medical_plan->$balance_field)) {
                // Update the balance and calculate uncovered balance
                $medical_plan->$balance_field -= $cost;
                $balance_uncoverage = $medical_plan->$balance_field < 0 ? abs($medical_plan->$balance_field) : 0;

                // Save updated health plan balance
                $medical_plan->save();
            }

            // dd($medical_plan);

            // Create the HealthCoverage entry for each medical type
            HealthCoverage::create([
                'usage_id' => (string) Str::uuid(),
                'employee_id' => $employee_id,
                'no_medic' => $this->generateNoMedic(),
                'no_invoice' => $request->no_invoice,
                'hospital_name' => $request->hospital_name,
                'patient_name' => $request->patient_name,
                'disease' => $request->disease,
                'date' => $date,
                'coverage_detail' => $request->coverage_detail,
                'period' => $period,
                'medical_type' => $medical_type,
                'balance' => $cost,
                'balance_uncoverage' => $balance_uncoverage,
                'status' => $statusValue,
                'medical_proof' => $medical_proof_path,
            ]);
        }

        return redirect()->route('medical')->with('success', 'Medical successfully added.');
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
