<?php

namespace App\Http\Controllers;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalController extends Controller
{
    public function medical()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'parentLink', 'link'));
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

    public function medicalCreate(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        $medic = new HealthCoverage();

        if ($request->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($request->has('action_submit')) {
            $statusValue = 'Pending';  // When "Submit" is clicked
        }
        if ($request->hasFile('medical_proof')) {
            $file = $request->file('medical_proof');
            $path = $file->store('public/proofs'); // Store in 'public/proofs' directory
            $medic->medical_proof = $path;
        }

        $glasses = (int) str_replace('.', '', $request->glasses);
        $childBirth = (int) str_replace('.', '', $request->child_birth);
        $inpatient = (int) str_replace('.', '', $request->inpatient);
        $outpatient = (int) str_replace('.', '', $request->outpatient);
        $totalCoverage = $glasses + $childBirth + $inpatient + $outpatient;

        HealthCoverage::create([
            'employee_id' => $employee_id,
            'no_medic' => $this->generateNoMedic(),
            'no_invoice' => $request->no_invoice,
            'hospital_name' => $request->hospital_name,
            'patient_name' => $request->patient_name,
            'disease' => $request->disease,
            'date' => $request->date,
            'coverage_detail' => $request->coverage_detail,
            'period' => $request->period,
            'glasses' => $glasses,
            'child_birth' => $childBirth,
            'inpatient' => $inpatient,
            'outpatient' => $outpatient,
            'total_coverage' => $totalCoverage,

            //uncovered
            'glasses_uncover' => $request->glasses_uncover,
            'child_birth_uncover' => $request->child_birth_uncover,
            'inpatient_uncover' => $request->inpatient_uncover,
            'outpatient_uncover' => $request->outpatient_uncover,
            'total_uncoverage' => $request->total_uncoverage,

            //others
            'status' => $statusValue,
            'medical_proof' => $request->medical_proof,
        ]);


        return redirect()->route('medical')->with('success', 'Medical Successfully Added');
    }

    public function generateNoMedic()
    {
        // Fetch the last no_medic number
        $lastCoverage = HealthCoverage::withTrashed() // Include soft-deleted records
            ->orderBy('no_medic', 'desc')
            ->first();

        // Determine the next no_medic number
        if ($lastCoverage) {
            // Extract the last number, increment it by 1
            $lastNumber = (int) substr($lastCoverage->no_medic, 3); // Get the last 6 digits
            $nextNumber = $lastNumber + 1;
        } else {
            // If there are no records, start from 600000001
            $nextNumber = 1;
        }

        // Format the next number as a 9-digit number starting with '6'
        $newNoMedic = '6' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return $newNoMedic;
    }

}
