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
use App\Imports\ImportHealthCoverage;
use App\Models\ca_sett_approval;
use App\Models\CATransaction;
use App\Models\ca_approval;


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
        $master_medical = MasterMedical::all();

        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

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

        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'rejectMedic', 'employeeName'));
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

    public function medicalDetail(Request $request, $employee_id)
    {
        // Gunakan findByRouteKey untuk mendekripsi $key
        // $employee = Employee::findByRouteKey($key);

        // Ambil employee_id yang telah didekripsi
        // $employee_id = $employee->employee_id;

        // Ambil data dependents, medical, dan medical_plan berdasarkan employee_id
        $family = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $medical = HealthCoverage::orderBy('created_at', 'desc')->where('employee_id', $employee_id)->get();
        $medical_plan = HealthPlan::orderBy('period', 'desc')->where('employee_id', $employee_id)->get();
        $master_medical = MasterMedical::all();

        // Format data medical_plan
        $formatted_data = [];
        foreach ($medical_plan as $plan) {
            $formatted_data[$plan->period][$plan->medical_type] = $plan->balance;
        }

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        // Kirim data ke view
        return view('hcis.reimbursements.medical.medical', compact('family', 'medical_plan', 'medical', 'parentLink', 'link', 'master_medical', 'formatted_data'));
    }

    public function medicalAdminUpdate(Request $request, $id)
    {
        $request->validate([
            'ca_status' => 'required|string',
        ]);

        // Temukan transaksi berdasarkan ID
        $ca_transaction = CATransaction::find($id);

        if (!$ca_transaction) {
            return redirect()->back()->with('error', 'Transaction not found.');
        }

        // Update field ca_status berdasarkan value yang dipilih di modal
        $ca_transaction->ca_status = $request->input('ca_status');
        $ca_transaction->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Transaction status updated successfully.')
            ->with('refresh', true);
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
}
