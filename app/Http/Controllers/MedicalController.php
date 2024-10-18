<?php

namespace App\Http\Controllers;

use App\Models\Dependents;
use App\Models\Employee;
use App\Models\MasterDisease;
use App\Models\HealthPlan;
use App\Models\HealthCoverage;
use App\Models\MasterMedical;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MedicalController extends Controller
{
    public function medical()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
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
            ->where('employee_id', $employee_id)
            ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        $medical = $medicalGroup->map(function ($item) use ($employee_id) {
            // Fetch the usage_id based on no_medic
            $usageId = HealthCoverage::where('no_medic', $item->no_medic)
                ->where('employee_id', $employee_id)
                ->value('usage_id'); // Assuming there's one usage_id per no_medic

            // Add usage_id to the current item
            $item->usage_id = $usageId;

            return $item;
        });

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
        $medical_type = MasterMedical::orderBy('id', 'desc')->get();

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
