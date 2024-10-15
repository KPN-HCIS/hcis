<?php

namespace App\Http\Controllers;

use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\CAApproval;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Designation;
use App\Models\ca_sett_approval;
use App\Models\ca_extend;
use App\Models\Location;
use App\Models\Employee;
use App\Models\MatrixApproval;
use App\Models\ListPerdiem;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\CATransaction;
use App\Http\Controllers\Log;
use App\Models\ca_approval;
use App\Models\htl_transaction;
use App\Models\tkt_transaction;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashAdvancedExport;

class ReimburseController extends Controller
{
    protected $groupCompanies;
    protected $companies;
    protected $locations;
    protected $permissionGroupCompanies;
    protected $permissionCompanies;
    protected $permissionLocations;
    protected $roles;

    public function __construct()
    {
        // $this->category = 'Goals';
        $this->roles = Auth()->user()->roles;

        $restrictionData = [];
        if (!is_null($this->roles) && $this->roles->isNotEmpty()) {
            $restrictionData = json_decode($this->roles->first()->restriction, true);
        }

        $this->permissionGroupCompanies = $restrictionData['group_company'] ?? [];
        $this->permissionCompanies = $restrictionData['contribution_level_code'] ?? [];
        $this->permissionLocations = $restrictionData['work_area_code'] ?? [];

        $groupCompanyCodes = $restrictionData['group_company'] ?? [];

        $this->groupCompanies = Location::select('company_name')
            ->when(!empty($groupCompanyCodes), function ($query) use ($groupCompanyCodes) {
                return $query->whereIn('company_name', $groupCompanyCodes);
            })
            ->orderBy('company_name')->distinct()->pluck('company_name');

        $workAreaCodes = $restrictionData['work_area_code'] ?? [];

        $this->locations = Location::select('company_name', 'area', 'work_area')
            ->when(!empty($workAreaCodes) || !empty($groupCompanyCodes), function ($query) use ($workAreaCodes, $groupCompanyCodes) {
                return $query->where(function ($query) use ($workAreaCodes, $groupCompanyCodes) {
                    if (!empty($workAreaCodes)) {
                        $query->whereIn('work_area', $workAreaCodes);
                    }
                    if (!empty($groupCompanyCodes)) {
                        $query->orWhereIn('company_name', $groupCompanyCodes);
                    }
                });
            })
            ->orderBy('area')
            ->get();

        $companyCodes = $restrictionData['contribution_level_code'] ?? [];

        $this->companies = Company::select('contribution_level', 'contribution_level_code')
            ->when(!empty($companyCodes), function ($query) use ($companyCodes) {
                return $query->whereIn('contribution_level_code', $companyCodes);
            })
            ->orderBy('contribution_level_code')->get();
    }

    function reimbursements()
    {

        $userId = Auth::id();

        return view('hcis.reimbursements.dash', [
            'userId' => $userId,
        ]);
    }
    public function cashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $today = Carbon::today();
        $ca_transactions = CATransaction::with(['employee', 'statusReqEmployee'])
            ->where('user_id', $userId)
            ->where('ca_status', '!=', 'Done')
            ->where('approval_status', '!=', 'Rejected')
            ->where('approval_sett', '=', '')
            ->get();
        // dd($ca_transactions);
        //tambah where status<>done
        // $pendingCACount = CATransaction::where('user_id', $userId)->where('approval_status', 'Pending')->count();
        $pendingCACount = CATransaction::where('user_id', $userId)
            ->where('approval_status', '!=', 'Rejected')
            ->where('ca_status', '!=', 'Done')
            ->count();
        // dd($pendingCACount);
        $today = Carbon::today();

        // Mengambil data karyawan yang sedang login
        $employee_data = Employee::where('id', $userId)->first();

        // Mendapatkan transaksi CA yang terkait dengan user yang sedang login
        // $ca_transactions = CATransaction::with('employee')->where('user_id', $userId)->get();

        // Mengambil fullname dari employee berdasarkan status_id
        // $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('status_id'))->pluck('fullname', 'employee_id');

        $disableCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_status', 'Pending')
                    ->orWhere('ca_status', 'Refund');
            })
            ->count();

        $deklarasiCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_sett', '')
                    ->orWhere('approval_sett', 'Declaration')
                    ->orWhere('approval_sett', 'Rejected')
                    ->orWhere('approval_sett', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->where('approval_status', 'Approved')
            // ->where('approval_extend', '!=', 'Pending')
            ->count();

        foreach ($ca_transactions as $transaction) {
            $transaction->settName = $transaction->statusReqEmployee ? $transaction->statusReqEmployee->fullname : '';
        }

        return view('hcis.reimbursements.cashadv.cashadv', [
            'deklarasiCACount' => $deklarasiCACount,
            'disableCACount' => $disableCACount,
            'employee_data' => $employee_data,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            // 'fullnames' => $fullnames, // Pass fullnames ke view
        ]);
    }
    public function requestCashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $today = Carbon::today();

        $employee_data = Employee::where('id', $userId)->first();

        $ca_transactions = CATransaction::with('employee')
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_status', 'Pending')
                    ->orWhere('approval_status', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('status_id'))
            ->pluck('fullname', 'employee_id');

        $pendingCACount = CATransaction::where('user_id', $userId)
            ->where('approval_status', 'Pending')
            ->count();

        $deklarasiCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_sett', '')
                    ->orWhere('approval_sett', 'Declaration')
                    ->orWhere('approval_sett', 'Rejected')
                    ->orWhere('approval_sett', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->where('ca_status', '!=', 'Done')
            ->where('approval_status', '=', 'Approved')
            ->where('approval_sett', '=', '')
            ->count();

        foreach ($ca_transactions as $transaction) {
            if ($transaction->end_date <= $today && $transaction->approval_status == 'Approved' && $transaction->approval_sett == 'On Progress') {
                $transaction->approval_sett = 'Waiting for Declaration';
            }
            $transaction->save();
        }

        return view('hcis.reimbursements.cashadv.cashadvRequest', [
            'deklarasiCACount' => $deklarasiCACount,
            'pendingCACount' => $pendingCACount,
            'link' => $link,
            'fullnames' => $fullnames,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
        ]);
    }
    public function cashadvancedAdmin(Request $request)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Report CA';
        $query = CATransaction::with(['employee', 'statusReqEmployee', 'statusSettEmployee'])->orderBy('created_at', 'desc');
        $ca_approvals = ca_approval::with(['employee', 'statusReqEmployee'])
            ->where('approval_status', '<>', 'Rejected')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        foreach ($ca_approvals as $approval) {
            $approval->ReqName = $approval->statusReqEmployee ? $approval->statusReqEmployee->fullname : '';
        }

        $ca_sett = ca_sett_approval::where('approval_status', '<>', 'Rejected')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        foreach ($ca_sett as $approval_sett) {
            $approval_sett->ReqName = $approval_sett->statusReqEmployee ? $approval_sett->statusReqEmployee->fullname : '';
        }

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');
        // dd($ca_approvals);

        $permissionLocations = $this->permissionLocations;
        $permissionCompanies = $this->permissionCompanies;
        $permissionGroupCompanies = $this->permissionGroupCompanies;

        if (!empty($permissionLocations)) {
            $query->whereHas('employee', function ($query) use ($permissionLocations) {
                $query->whereIn('work_area_code', $permissionLocations);
            });
        }

        if (!empty($permissionCompanies)) {
            $query->whereIn('contribution_level_code', $permissionCompanies);
        }

        if (!empty($permissionGroupCompanies)) {
            $query->whereHas('employee', function ($query) use ($permissionGroupCompanies) {
                $query->whereIn('group_company', $permissionGroupCompanies);
            });
        }
        //dd($permissionGroupCompanies);

        // Cek apakah ada nilai start_date dan end_date dalam request
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Tambahkan kondisi whereBetween pada query
            $query->whereBetween('start_date', [$startDate, $endDate]);
        }

        if (request()->get('stat') == '-') {
            // Jika status adalah '-', tidak perlu melakukan filter.
        } else {
            // Periksa apakah ada parameter status yang diberikan
            if ($request->has('stat') && $request->input('stat') !== '') {
                $status = $request->input('stat');
                // Tambahkan kondisi where untuk status jika ada status yang valid
                $query->where('ca_status', $status);
            }
        }

        // Eksekusi query untuk mendapatkan data yang difilter
        $ca_transactions = $query->get();

        foreach ($ca_transactions as $transaction) {
            $transaction->ReqName = $transaction->statusReqEmployee ? $transaction->statusReqEmployee->fullname : '';
            $transaction->settName = $transaction->statusSettEmployee ? $transaction->statusSettEmployee->fullname : '';
        }

        // $pendingCACount = CATransaction::where('user_id', $userId)->where('approval_status', 'Pending')->count();

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        return view('hcis.reimbursements.cashadv.adminCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'ca_approvals' => $ca_approvals,
            'ca_sett' => $ca_sett,
        ]);
    }
    public function cashadvancedAdminUpdate(Request $request, $id)
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
    public function deklarasiCashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $today = Carbon::today()->format('Y-m-d');
        // dd($today);
        $ca_transactions = CATransaction::with(['employee', 'statusSettEmployee', 'statusExtendEmployee'])
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_status', 'Approved');
            })
            ->where('end_date', '<=', $today)
            ->where('ca_status', '!=', 'Done')
            ->get();

        $reason = ca_sett_approval::whereIn('ca_id', $ca_transactions->pluck('id'))
            ->pluck('reject_info', 'ca_id');

        // dd($ca_transactions);
        foreach ($ca_transactions as $transaction) {
            $transaction->settName = $transaction->statusSettEmployee ? $transaction->statusSettEmployee->fullname : '';
            $transaction->extName = $transaction->statusExtendEmployee ? $transaction->statusExtendEmployee->fullname : '';
        }

        $deklarasiCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_sett', '')
                    ->orWhere('approval_sett', 'Declaration')
                    ->orWhere('approval_sett', 'Rejected')
                    ->orWhere('approval_sett', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->where('approval_status', 'Approved')
            // ->where('approval_extend', '<>', 'Pending')
            ->count();

        return view('hcis.reimbursements.cashadv.cashadvDeklarasi', [
            'deklarasiCACount' => $deklarasiCACount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'reason' => $reason,
            // 'settName' => $settName,

        ]);
    }
    public function filterCaTransactions(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Lakukan query untuk memfilter transaksi berdasarkan tanggal
        $ca_transactions = CATransaction::with('employee')->whereBetween('start_date', [$startDate, $endDate])
            ->get();
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        // dd($ca_transactions);
        // Kembalikan tampilan tabel yang telah difilter
        return view('hcis.reimbursements.cashadv.CaTransactionsTable', compact('ca_transactions'))->render();
    }
    public function doneCashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $today = Carbon::today();

        $ca_transactions = CATransaction::with('employee')
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('ca_status', 'Done');
            })
            ->where('end_date', '<=', $today)
            ->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('status_id'))
            ->pluck('fullname', 'employee_id');

        $deklarasiCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_sett', '')
                    ->orWhere('approval_sett', 'Declaration')
                    ->orWhere('approval_sett', 'Rejected')
                    ->orWhere('approval_sett', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->where('ca_status', '!=', 'Done')
            ->where('approval_status', '=', 'Approved')
            ->where('approval_sett', '=', '')
            ->count();

        foreach ($ca_transactions as $transaction) {
            if (
                $transaction->end_date <= $today &&
                $transaction->approval_status == 'Approved' &&
                $transaction->approval_sett == 'On Progress'
            ) {

                $transaction->approval_sett = 'Waiting for Declaration';
            }
            $transaction->save();
        }

        return view('hcis.reimbursements.cashadv.cashadvDone', [
            'deklarasiCACount' => $deklarasiCACount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'fullnames' => $fullnames,
        ]);
    }
    public function rejectCashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $today = Carbon::today();

        $employee_data = Employee::where('id', $userId)->first();

        $ca_transactions = CATransaction::where('user_id', $userId)
            ->where('approval_status', 'Rejected')
            ->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('status_id'))
            ->pluck('fullname', 'employee_id');

        $reason = ca_approval::whereIn('ca_id', $ca_transactions->pluck('id'))
            ->pluck('reject_info', 'ca_id');

        $deklarasiCACount = CATransaction::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('approval_sett', '')
                    ->orWhere('approval_sett', 'Declaration')
                    ->orWhere('approval_sett', 'Rejected')
                    ->orWhere('approval_sett', 'Draft');
            })
            ->where('end_date', '<=', $today)
            ->where('ca_status', '!=', 'Done')
            ->where('approval_status', '=', 'Approved')
            ->where('approval_sett', '=', '')
            ->count();

        // foreach ($ca_transactions as $transaction) {
        //     if (
        //         $transaction->end_date <= $today &&
        //         $transaction->approval_status == 'Approved' &&
        //         $transaction->approval_sett == 'On Progress'
        //     ) {

        //         $transaction->approval_sett = 'Waiting for Declaration';
        //     }
        //     $transaction->save();
        // }

        return view('hcis.reimbursements.cashadv.cashadvReject', [
            'deklarasiCACount' => $deklarasiCACount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'employee_data' => $employee_data,
            'fullnames' => $fullnames,
            'reason' => $reason,
        ]);
    }
    function cashadvancedCreate(Request $request)
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $formCount = $request->input('form_count', 1);  // Default to 1 if no form_count

        // If "Add More" was clicked, increase the form count
        if ($request->has('add_more')) {
            $formCount++;
        }

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $noSppdListDNS = CATransaction::whereNotNull('no_sppd')
            ->where('user_id', $userId)
            ->where('no_sppd', '!=', '')
            ->where('type_ca', 'dns')
            ->pluck('no_sppd');
        $noSppdListENT = CATransaction::whereNotNull('no_sppd')
            ->where('user_id', $userId)
            ->where('no_sppd', '!=', '')
            ->where('type_ca', 'dns')
            ->pluck('no_sppd');
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where('status', '!=', 'Verified')
            ->get();

        function findDepartmentHead($employee)
        {
            $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

            if (!$manager) {
                return null;
            }

            $designation = Designation::where('job_code', $manager->designation_code)->first();

            if ($designation->dept_head_flag == 'T') {
                return $manager;
            } else {
                return findDepartmentHead($manager);
            }
            return null;
        }
        $deptHeadManager = findDepartmentHead($employee_data);

        $managerL1 = $deptHeadManager->employee_id;
        $managerL2 = $deptHeadManager->manager_l1_id;

        $cek_director_id = Employee::select([
            'dsg.department_level2',
            'dsg2.director_flag',
            DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
            'dsg2.designation_name',
            'dsg2.job_code',
            'emp.fullname',
            'emp.employee_id',
        ])
            ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
            ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
            ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
            ->where('employees.designation_code', '=', $employee_data->designation_code)
            ->where('dsg2.director_flag', '=', 'T')
            ->get();

        $director_id = "";

        if ($cek_director_id->isNotEmpty()) {
            $director_id = $cek_director_id->first()->employee_id;
        }

        return view('hcis.reimbursements.cashadv.formCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'noSppdListDNS' => $noSppdListDNS,
            'noSppdListENT' => $noSppdListENT,
            'managerL1' => $managerL1,
            'managerL2' => $managerL2,
            'director_id' => $director_id,
            'formCount' => $formCount,
        ]);
    }
    public function cashadvancedSubmit(Request $req)
    {
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();
        $currentYear = date('Y');
        $currentYearShort = date('y'); // Mengambil 2 digit terakhir dari tahun
        $prefix = 'CA';

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = CATransaction::withTrashed()
            ->whereYear('created_at', $currentYear)
            ->orderBy('no_ca', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $newNoCa = "$prefix$currentYearShort$newNumber";

        $uuid = Str::uuid();
        $model = new CATransaction;
        $model->id = $uuid;
        $model->type_ca = $req->ca_type;
        $model->no_ca = $newNoCa;
        $model->user_id = $userId;
        $model->unit = $req->unit;
        $model->contribution_level_code = $req->companyFilter;
        $model->destination = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs = $req->ca_needs;
        $model->start_date = $req->start_date;
        $model->end_date = $req->end_date;
        $model->date_required = $req->ca_required;
        $model->declare_estimate = $req->ca_decla;
        $model->total_days = $req->totaldays;
        if ($req->ca_type == 'dns') {
            // Menyiapkan array untuk menyimpan detail dari setiap bagian
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Loop untuk Perdiem
            if ($req->has('start_bt_perdiem')) {
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key];
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);

                    // Check for valid data before adding to detail array
                    if (!empty($startDate) && !empty($endDate) && !empty($companyCode) && !empty($nominal)) {
                        $detail_perdiem[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'location' => $location,
                            'other_location' => $other_location,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Transport
            if ($req->has('tanggal_bt_transport')) {
                foreach ($req->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_transport[$key];
                    $companyCode = $req->company_bt_transport[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_transport[$key]);

                    if (!empty($tanggal) && !empty($companyCode) && !empty($nominal)) {
                        $detail_transport[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Penginapan
            if ($req->has('start_bt_penginapan')) {
                foreach ($req->start_bt_penginapan as $key => $startDate) {
                    $endDate = $req->end_bt_penginapan[$key];
                    $totalDays = $req->total_days_bt_penginapan[$key];
                    $hotelName = $req->hotel_name_bt_penginapan[$key];
                    $companyCode = $req->company_bt_penginapan[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_penginapan[$key]);

                    if (!empty($startDate) && !empty($endDate) && !empty($totalDays) && !empty($hotelName) && !empty($companyCode) && !empty($nominal)) {
                        $detail_penginapan[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'hotel_name' => $hotelName,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Lainnya
            if ($req->has('tanggal_bt_lainnya')) {
                foreach ($req->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_lainnya[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_lainnya[$key]);

                    if (!empty($tanggal) && !empty($nominal)) {
                        $detail_lainnya[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Konversi array menjadi JSON untuk disimpan di database
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $model->detail_ca = json_encode($detail_ca);
            $model->declare_ca = json_encode($detail_ca);
            $model->no_sppd = $req->bisnis_numb_dns;
        } else if ($req->ca_type == 'ndns') {
            $detail_ndns = [];
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    if (!empty($tanggal) && !empty($nominal_nbt)) {
                        $detail_ndns[] = [
                            'tanggal_nbt' => $tanggal,
                            'keterangan_nbt' => $keterangan_nbt,
                            'nominal_nbt' => $nominal_nbt,
                        ];
                    }
                }
            }
            $detail_ndns_json = json_encode($detail_ndns);
            $model->detail_ca = $detail_ndns_json;
            $model->declare_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_e = [];
            $relation_e = [];

            // Mengumpulkan detail entertain
            if ($req->has('enter_type_e_detail')) {
                foreach ($req->enter_type_e_detail as $key => $type) {
                    $fee_detail = $req->enter_fee_e_detail[$key];
                    $nominal = str_replace('.', '', $req->nominal_e_detail[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    if (!empty($type) && !empty($nominal)) {
                        $detail_e[] = [
                            'type' => $type,
                            'fee_detail' => $fee_detail,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Mengumpulkan detail relation
            if ($req->has('rname_e_relation')) {
                foreach ($req->rname_e_relation as $key => $name) {
                    $position = $req->rposition_e_relation[$key];
                    $company = $req->rcompany_e_relation[$key];
                    $purpose = $req->rpurpose_e_relation[$key];

                    // Memastikan semua data yang diperlukan untuk relation terisi
                    if (!empty($name) && !empty($position) && !empty($company) && !empty($purpose)) {
                        $relation_e[] = [
                            'name' => $name,
                            'position' => $position,
                            'company' => $company,
                            'purpose' => $purpose,
                            'relation_type' => array_filter([
                                'Food' => in_array('food', $req->food_e_relation ?? [$key]),
                                'Transport' => in_array('transport', $req->transport_e_relation ?? [$key]),
                                'Accommodation' => in_array('accommodation', $req->accommodation_e_relation ?? [$key]),
                                'Gift' => in_array('gift', $req->gift_e_relation ?? [$key]),
                                'Fund' => in_array('fund', $req->fund_e_relation ?? [$key]),
                            ], fn($checked) => $checked),
                        ];
                    }
                }
            }

            // Gabungkan detail entertain dan relation, lalu masukkan ke detail_ca
            $detail_ca = [
                'detail_e' => $detail_e,
                'relation_e' => $relation_e,
            ];
            $model->detail_ca = json_encode($detail_ca);
            $model->declare_ca = json_encode($detail_ca);
            $model->no_sppd = $req->bisnis_numb_ent;
        }

        $model->total_ca = str_replace('.', '', $req->totalca);
        $model->total_real = "0";
        $model->total_cost = str_replace('.', '', $req->totalca);

        if ($req->input('action_ca_draft')) {
            $model->approval_status = $req->input('action_ca_draft');
            $model->created_by = $userId;
            $model->save();
            return redirect()->route('cashadvanced')->with('success', 'Transaction successfully Added in Draft.');
        }
        if ($req->input('action_ca_submit')) {
            $model->approval_status = $req->input('action_ca_submit');
        }
        if ($req->input('action_ca_submit')) {
            function findDepartmentHead($employee)
            {
                $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

                if (!$manager) {
                    return null;
                }

                $designation = Designation::where('job_code', $manager->designation_code)->first();

                if ($designation->dept_head_flag == 'T') {
                    return $manager;
                } else {
                    return findDepartmentHead($manager);
                }
                return null;
            }
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

            $model->status_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval
            $total_ca = str_replace('.', '', $req->totalca);
            $data_matrix_approvals = MatrixApproval::where(function ($query) use ($req) {
                if ($req->ca_type === 'dns') {
                    $query->where('modul', 'dns');
                } else {
                    $query->where('modul', 'like', '%' . $req->ca_type . '%');
                }
            })
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->companyFilter . '%')
                ->whereRaw(
                    '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                    [$total_ca]
                )
                ->get();
            // dd($data_matrix_approvals);
            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }

                if ($employee_id !=  null) {
                    $model_approval = new ca_approval;
                    $model_approval->ca_id = $uuid;
                    $model_approval->role_name = $data_matrix_approval->desc;
                    $model_approval->employee_id = $employee_id;
                    $model_approval->layer = $data_matrix_approval->layer;
                    $model_approval->approval_status = 'Pending';

                    // Simpan data ke database
                    $model_approval->save();
                }

                // Simpan data ke database
                $model_approval->save();
            }
        }

        $model->created_by = $userId;
        $model->save();

        return redirect()->route('cashadvanced')->with('success', 'Transaction successfully added waiting for Approval.');
    }
    function cashadvancedEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $noSppdListDNS = CATransaction::whereNotNull('no_sppd')
            ->where('user_id', $userId)
            ->where('no_sppd', '!=', '')
            ->where('type_ca', 'dns')
            ->pluck('no_sppd');
        $noSppdListENT = CATransaction::whereNotNull('no_sppd')
            ->where('user_id', $userId)
            ->where('no_sppd', '!=', '')
            ->where('type_ca', 'dns')
            ->pluck('no_sppd');
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where('status', '!=', 'Verified')
            ->get();
        // $transactions = CATransaction::find($key);
        $transactions = CATransaction::findByRouteKey($key);
        // dd($key);
        return view('hcis.reimbursements.cashadv.editCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'noSppdListENT' => $noSppdListENT,
            'noSppdListDNS' => $noSppdListDNS,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedUpdate(Request $req, $key)
    {
        $userId = Auth::id();
        $uuid = Str::uuid();
        $model = CATransaction::findByRouteKey($key);
        $employee_data = Employee::where('id', $userId)->first();
        // $model->type_ca = $req->ca_type;
        $model->no_ca = $req->no_ca;
        $model->no_sppd = $req->bisnis_numb;
        // $model->user_id         = $req->id;
        $model->unit = $req->unit;
        $model->contribution_level_code = $req->companyFilter;
        $model->destination = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs = $req->ca_needs;
        $model->start_date = $req->start_date;
        $model->end_date = $req->end_date;
        $model->date_required = $req->ca_required;
        $model->declare_estimate = $req->ca_decla;
        $model->total_days = $req->totaldays;
        if ($req->ca_type == 'dns') {
            // Menyiapkan array untuk menyimpan detail dari setiap bagian
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Loop untuk Perdiem
            if ($req->has('start_bt_perdiem')) {
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key] ?? '';
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);

                    if (!empty($startDate) && !empty($endDate) && !empty($companyCode) && !empty($nominal)) {
                        $detail_perdiem[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'location' => $location,
                            'other_location' => $other_location,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Transport
            if ($req->has('tanggal_bt_transport')) {
                foreach ($req->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_transport[$key];
                    $companyCode = $req->company_bt_transport[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_transport[$key]);

                    if (!empty($tanggal) && !empty($companyCode) && !empty($nominal)) {
                        $detail_transport[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Penginapan
            if ($req->has('start_bt_penginapan')) {
                foreach ($req->start_bt_penginapan as $key => $startDate) {
                    $endDate = $req->end_bt_penginapan[$key];
                    $totalDays = $req->total_days_bt_penginapan[$key];
                    $hotelName = $req->hotel_name_bt_penginapan[$key];
                    $companyCode = $req->company_bt_penginapan[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_penginapan[$key]);

                    if (!empty($startDate) && !empty($endDate) && !empty($totalDays) && !empty($hotelName) && !empty($companyCode) && !empty($nominal)) {
                        $detail_penginapan[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'hotel_name' => $hotelName,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Loop untuk Lainnya
            if ($req->has('tanggal_bt_lainnya')) {
                foreach ($req->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_lainnya[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_lainnya[$key]);

                    if (!empty($tanggal) && !empty($nominal)) {
                        $detail_lainnya[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Konversi array menjadi JSON untuk disimpan di database
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $model->declare_ca = json_encode($detail_ca);
            $model->detail_ca = json_encode($detail_ca);
        } else if ($req->ca_type == 'ndns') {
            $detail_ndns = [];
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    if (!empty($tanggal) && !empty($nominal_nbt)) {
                        $detail_ndns[] = [
                            'tanggal_nbt' => $tanggal,
                            'keterangan_nbt' => $keterangan_nbt,
                            'nominal_nbt' => $nominal_nbt,
                        ];
                    }
                }
            }
            $detail_ndns_json = json_encode($detail_ndns);
            $model->declare_ca = $detail_ndns_json;
            $model->detail_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_e = [];
            $relation_e = [];

            // Mengumpulkan detail entertain
            if ($req->has('enter_type_e_detail')) {
                foreach ($req->enter_type_e_detail as $key => $type) {
                    $fee_detail = $req->enter_fee_e_detail[$key];
                    $nominal = str_replace('.', '', $req->nominal_e_detail[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    if (!empty($type) && !empty($nominal)) {
                        $detail_e[] = [
                            'type' => $type,
                            'fee_detail' => $fee_detail,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            // Mengumpulkan detail relation
            if ($req->has('rname_e_relation')) {
                foreach ($req->rname_e_relation as $key => $name) {
                    $relation_e[] = [
                        'name' => $name,
                        'position' => $req->rposition_e_relation[$key],
                        'company' => $req->rcompany_e_relation[$key],
                        'purpose' => $req->rpurpose_e_relation[$key],
                        'relation_type' => array_filter([
                            'Food' => in_array('food', $req->food_e_relation ?? [$key]),
                            'Transport' => in_array('transport', $req->transport_e_relation ?? [$key]),
                            'Accommodation' => in_array('accommodation', $req->accommodation_e_relation ?? [$key]),
                            'Gift' => in_array('gift', $req->gift_e_relation ?? [$key]),
                            'Fund' => in_array('fund', $req->fund_e_relation ?? [$key]),
                        ], fn($checked) => $checked),
                    ];
                }
            }

            // Gabungkan detail entertain dan relation, lalu masukkan ke detail_ca
            $detail_ca = [
                'detail_e' => $detail_e,
                'relation_e' => $relation_e,
            ];
            $model->detail_ca = json_encode($detail_ca);
            $model->declare_ca = json_encode($detail_ca);
        }
        $model->total_ca = str_replace('.', '', $req->totalca);
        $model->total_real = "0";
        $model->total_cost = str_replace('.', '', $req->totalca);
        if ($req->input('action_ca_draft')) {
            $model->approval_status = $req->input('action_ca_draft');
            $model->save();
            return redirect()->route('cashadvanced')->with('success', 'Transaction successfully Added in Draft.');
        }
        if ($req->input('action_ca_submit')) {
            $model->approval_status = $req->input('action_ca_submit');
        }

        $model->created_by = $userId;

        if ($req->input('action_ca_submit')) {
            function findDepartmentHead($employee)
            {
                $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

                if (!$manager) {
                    return null;
                }

                $designation = Designation::where('job_code', $manager->designation_code)->first();

                if ($designation->dept_head_flag == 'T') {
                    return $manager;
                } else {
                    return findDepartmentHead($manager);
                }
                return null;
            }
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

            $model->status_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval
            $total_ca = str_replace('.', '', $req->totalca);
            $data_matrix_approvals = MatrixApproval::where(function ($query) use ($req) {
                if ($req->ca_type === 'dns') {
                    $query->where('modul', 'dns');
                } else {
                    $query->where('modul', 'like', '%' . $req->ca_type . '%');
                }
            })
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->companyFilter . '%')
                ->whereRaw(
                    '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                    [$total_ca]
                )
                ->get();

            // dd($data_matrix_approvals);
            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }
                if ($employee_id !=  null) {
                    $model_approval = new ca_approval;
                    $model_approval->ca_id = $req->no_id;
                    $model_approval->role_name = $data_matrix_approval->desc;
                    $model_approval->employee_id = $employee_id;
                    $model_approval->layer = $data_matrix_approval->layer;
                    $model_approval->approval_status = 'Pending';

                    // Simpan data ke database
                    $model_approval->save();
                }
            }
        }
        $model->save();
        return redirect()->route('cashadvanced')->with('success', 'Transaction successfully added waiting for Approval.');
    }
    public function cashadvancedExtend(Request $req)
    {
        $id = $req->input('no_id'); // Get the ID from the no_id input
        $userId = Auth::id();
        $model = CATransaction::find($id);
        $employee_data = Employee::where('id', $userId)->first();

        if ($req->input('action_ca_submit')) {
            $model->approval_extend = $req->input('action_ca_submit');
        }
        if ($req->input('action_ca_submit')) {
            function findDepartmentHead($employee)
            {
                $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

                if (!$manager) {
                    return null;
                }

                $designation = Designation::where('job_code', $manager->designation_code)->first();

                if ($designation->dept_head_flag == 'T') {
                    return $manager;
                } else {
                    return findDepartmentHead($manager);
                }
                return null;
            }
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

            $model->extend_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            $data_matrix_approvals = MatrixApproval::where('modul', 'extendca')
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->companyFilter . '%')
                ->get();
            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }
                if ($employee_id !=  null) {
                    $model_approval = new ca_extend;
                    $model_approval->ca_id = $req->no_id;
                    $model_approval->role_name = $data_matrix_approval->desc;
                    $model_approval->employee_id = $employee_id;
                    $model_approval->layer = $data_matrix_approval->layer;
                    $model_approval->approval_status = 'Pending';
                    $model_approval->start_date = $req->input('start_date');
                    $model_approval->end_date = $req->input('end_date');
                    $model_approval->ext_end_date = $req->input('ext_end_date');
                    $model_approval->total_days = $req->input('totaldays');
                    $model_approval->ext_total_days = $req->input('ext_totaldays');
                    $model_approval->reason_extend = $req->input('ext_reason');

                    // Simpan data ke database
                    $model_approval->save();
                }
            }

            $model->save();

            return redirect()->route('cashadvancedDeklarasi')->with('success', 'Transaction asking for Extend, Please wait for Approval.');
        }
    }
    function cashadvancedDelete($id)
    {
        $model = ca_transaction::find($id);

        if ($model) {
            $model->delete();
            return redirect()->back()->with('success', 'Transaction successfully deleted.');
        } else {
            return redirect()->back()->with('error', 'Transaction not found.');
        }
    }
    function cashadvancedDownload($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        // $kantor = Company::where('contribution_level', $companies->contribution_level_code)->first();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::find($key);
        $approval = ca_approval::with('employee')
            ->where('ca_id', $key)
            ->where('approval_status', '!=', 'Rejected')
            ->orderBy('layer', 'asc')
            ->get();

        $pdf = PDF::loadView('hcis.reimbursements.cashadv.printCashadv', [
            'link' => $link,
            // 'pdf' => $pdf,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'transactions' => $transactions,
            'approval' => $approval,
        ])->setPaper('a4', 'potrait')->set_option("enable_php", true);

        return $pdf->stream('Cash Advanced ' . $key . '.pdf');
    }
    function cashadvancedDownloadDeklarasi($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)
            ->first();
        $companies = Company::orderBy('contribution_level')
            ->get();
        $locations = Location::orderBy('area')
            ->get();
        // $transactions = CATransaction::find($key);
        $transactions = CATransaction::with('companies')->find($key);
        $approval = ca_sett_approval::with('employee')
            ->where('ca_id', $key)
            ->where('approval_status', '<>', 'Rejected')
            ->orderBy('layer', 'asc')
            ->get();

        $pdf = PDF::loadView('hcis.reimbursements.cashadv.printDeklarasiCashadv', [
            'link' => $link,
            // 'pdf' => $pdf,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'transactions' => $transactions,
            'approval' => $approval,
        ])->setPaper('a4', 'potrait')->set_option("enable_php", true);

        return $pdf->stream('Cash Advanced ' . $key . '.pdf');
    }

    public function cashadvancedDeklarasi($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced Approval';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::findByRouteKey($key);
        // dd($transactions);

        return view('hcis.reimbursements.cashadv.deklarasiCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedDeclare(Request $req, $key)
    {
        $userId = Auth::id();
        $uuid = Str::uuid();
        $model = CATransaction::findByRouteKey($key);
        $employee_data = Employee::where('id', $userId)->first();

        $req->validate([
            'prove_declare' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:2048', // Aturan validasi file gambar
        ]);

        if ($req->hasFile('prove_declare')) {
            $file = $req->file('prove_declare');
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/proofs'), $filename);
            // $file->move('/home/hcis8257/public_html/apps/uploads/proofs', $filename);

            $model->prove_declare = $filename;
        } else {
            $model->prove_declare = $req->existing_prove_declare;
        }
        $model->no_ca = $req->no_ca;
        $model->no_sppd = $req->bisnis_numb;

        if ($req->ca_type == 'dns') {
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            if ($req->has('start_bt_perdiem')) {
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key];
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);

                    if (!empty($startDate) && !empty($endDate) && !empty($companyCode) && !empty($nominal)) {
                        $detail_perdiem[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'location' => $location,
                            'other_location' => $other_location,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            if ($req->has('tanggal_bt_transport')) {
                foreach ($req->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_transport[$key];
                    $companyCode = $req->company_bt_transport[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_transport[$key]);

                    if (!empty($tanggal) && !empty($companyCode) && !empty($nominal)) {
                        $detail_transport[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            if ($req->has('start_bt_penginapan')) {
                foreach ($req->start_bt_penginapan as $key => $startDate) {
                    $endDate = $req->end_bt_penginapan[$key];
                    $totalDays = $req->total_days_bt_penginapan[$key];
                    $hotelName = $req->hotel_name_bt_penginapan[$key];
                    $companyCode = $req->company_bt_penginapan[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_penginapan[$key]);

                    if (!empty($startDate) && !empty($endDate) && !empty($totalDays) && !empty($hotelName) && !empty($companyCode) && !empty($nominal)) {
                        $detail_penginapan[] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'total_days' => $totalDays,
                            'hotel_name' => $hotelName,
                            'company_code' => $companyCode,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            if ($req->has('tanggal_bt_lainnya')) {
                foreach ($req->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_lainnya[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_lainnya[$key]);

                    if (!empty($tanggal) && !empty($nominal)) {
                        $detail_lainnya[] = [
                            'tanggal' => $tanggal,
                            'keterangan' => $keterangan,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            $declare_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $model->declare_ca = json_encode($declare_ca);
        } else if ($req->ca_type == 'ndns') {
            $detail_ndns = [];
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]);

                    if (!empty($tanggal) && !empty($nominal_nbt)) {
                        $detail_ndns[] = [
                            'tanggal_nbt' => $tanggal,
                            'keterangan_nbt' => $keterangan_nbt,
                            'nominal_nbt' => $nominal_nbt,
                        ];
                    }
                }
            }
            $detail_ndns_json = json_encode($detail_ndns);
            $model->declare_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_e = [];
            $relation_e = [];

            if ($req->has('enter_type_e_detail')) {
                foreach ($req->enter_type_e_detail as $key => $type) {
                    $fee_detail = $req->enter_fee_e_detail[$key];
                    $nominal = str_replace('.', '', $req->nominal_e_detail[$key]);

                    if (!empty($type) && !empty($nominal)) {
                        $detail_e[] = [
                            'type' => $type,
                            'fee_detail' => $fee_detail,
                            'nominal' => $nominal,
                        ];
                    }
                }
            }

            if ($req->has('rname_e_relation')) {
                foreach ($req->rname_e_relation as $key => $name) {
                    $relation_e[] = [
                        'name' => $name,
                        'position' => $req->rposition_e_relation[$key],
                        'company' => $req->rcompany_e_relation[$key],
                        'purpose' => $req->rpurpose_e_relation[$key],
                        'relation_type' => array_filter([
                            'Food' => in_array('food', $req->food_e_relation ?? [$key]),
                            'Transport' => in_array('transport', $req->transport_e_relation ?? [$key]),
                            'Accommodation' => in_array('accommodation', $req->accommodation_e_relation ?? [$key]),
                            'Gift' => in_array('gift', $req->gift_e_relation ?? [$key]),
                            'Fund' => in_array('fund', $req->fund_e_relation ?? [$key]),
                        ], fn($checked) => $checked),
                    ];
                }
            }

            $declare_ca = [
                'detail_e' => $detail_e,
                'relation_e' => $relation_e,
            ];
            $model->declare_ca = json_encode($declare_ca);
        }
        $model->total_ca = str_replace('.', '', $req->totalca_deklarasi);
        $model->total_real = str_replace('.', '', $req->totalca);
        $model->total_cost = $model->total_ca - $model->total_real;
        //tambah 1 status disini


        if ($req->input('action_ca_draft')) {
            $model->approval_sett = $req->input('action_ca_draft');
            $model->save();
            return redirect()->route('cashadvancedDeklarasi')->with('success', 'Transaction successfully Added in Draft.');
        }
        if ($req->input('action_ca_submit')) {
            $model->approval_sett = $req->input('action_ca_submit');
            if ($model->total_cost > 0) {
                $model->ca_status = 'Refund';
            } else {
                $model->ca_status = 'On Progress';
            }
        }
        if ($req->input('action_ca_submit')) {
            function findDepartmentHead($employee)
            {
                $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

                if (!$manager) {
                    return null;
                }

                $designation = Designation::where('job_code', $manager->designation_code)->first();

                if ($designation->dept_head_flag == 'T') {
                    return $manager;
                } else {
                    return findDepartmentHead($manager);
                }
                return null;
            }
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

            $model->sett_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";
            // dd($req->companyFilter);

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval
            $total_ca = str_replace('.', '', $req->totalca);
            $data_matrix_approvals = MatrixApproval::where(function ($query) use ($req) {
                if ($req->ca_type === 'dns') {
                    $query->where('modul', 'dns');
                } else {
                    $query->where('modul', 'like', '%' . $req->ca_type . '%');
                }
            })
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->contribution_level_code . '%')
                ->whereRaw(
                    '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                    [$total_ca]
                )
                ->get();
            // dd($req->contribution_level_code);
            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }
                if ($employee_id !=  null) {
                    $model_approval = new ca_sett_approval;
                    $model_approval->ca_id = $req->no_id;
                    $model_approval->role_name = $data_matrix_approval->desc;
                    $model_approval->employee_id = $employee_id;
                    $model_approval->layer = $data_matrix_approval->layer;
                    $model_approval->approval_status = 'Pending';

                    // Simpan data ke database
                    $model_approval->save();
                }
            }
        }
        $model->declaration_at = Carbon::now();
        $model->save();

        return redirect()->route('cashadvancedDeklarasi')->with('success', 'Transaction successfully added waiting for Approval.');
    }

    public function hotel()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';
        $transactions = Hotel::with('employee')->get();

        // foreach ($transactions as $transaction) {
        //     dd($transaction); // This will dump the first transaction and stop execution
        // }

        return view('hcis.reimbursements.hotel.hotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
        ]);
    }


    function hotelCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)->where('status', '!=', 'Approved')->get();
        // $no_sppds = ca_transaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();


        return view('hcis.reimbursements.hotel.formHotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
        ]);
    }
    public function hotelSubmit(Request $req)
    {
        function getRomanMonth_htl($month)
        {
            $romanMonths = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];
            return $romanMonths[$month];
        }
        $userId = Auth::id();
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = getRomanMonth_htl($currentMonth);

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = htl_transaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_htl', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/HTL-ACC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_htl, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoHtl = "$newNumber/HTL-ACC/$romanMonth/$currentYear";

        $model = new htl_transaction;
        $model->id = Str::uuid();
        $model->no_htl = $newNoHtl;
        $model->no_sppd = $req->bisnis_numb;
        $model->user_id = $userId;
        $model->unit = $req->unit;
        $model->nama_htl = $req->nama_htl;
        $model->lokasi_htl = $req->lokasi_htl;
        $model->jmlkmr_htl = $req->jmlkmr_htl;
        $model->bed_htl = $req->bed_htl;
        $model->tgl_masuk_htl = $req->tgl_masuk_htl;
        $model->tgl_keluar_htl = $req->tgl_keluar_htl;
        $model->total_hari = $req->totaldays;
        $model->created_by = $userId;
        $model->save();

        Alert::success('Success');
        session()->flash('message', 'Berhasil di Tambahkan');
        return redirect()->intended(route('hotel', absolute: false));
    }
    function hotelEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = htl_transaction::findByRouteKey($key);

        return view('hcis.reimbursements.hotel.editHotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    public function hotelUpdate(Request $req, $key)
    {
        $model = htl_transaction::findByRouteKey($key);

        if ($model) {
            $model->unit = $req->unit;
            $model->nama_htl = $req->nama_htl;
            $model->lokasi_htl = $req->lokasi_htl;
            $model->jmlkmr_htl = $req->jmlkmr_htl;
            $model->bed_htl = $req->bed_htl;
            $model->tgl_masuk_htl = $req->tgl_masuk_htl;
            $model->tgl_keluar_htl = $req->tgl_keluar_htl;
            $model->total_hari = $req->totaldays;
            $model->save();

            Alert::success('Success');
            session()->flash('message', 'Edit Berhasil');
            return redirect()->route('hotel');
        } else {
            return redirect()->back()->withErrors(['message' => 'Transaction not found']);
        }
    }
    function hotelDelete($key)
    {
        $model = htl_transaction::findByRouteKey($key);
        $model->delete();
        return redirect()->intended(route('hotel', absolute: false));
    }
    public function ticket()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';
        $transactions = tkt_transaction::with('employee')->get();

        return view('hcis.reimbursements.ticket.ticket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
        ]);
    }
    function ticketCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)->where('status', '!=', 'Approved')->get();
        // $no_sppds = ca_transaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();


        return view('hcis.reimbursements.ticket.formTicket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
        ]);
    }
    public function ticketSubmit(Request $req)
    {
        function getRomanMonth_tkt($month)
        {
            $romanMonths = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];
            return $romanMonths[$month];
        }
        $userId = Auth::id();
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = getRomanMonth_tkt($currentMonth);

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = tkt_transaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_tkt', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/TKT-ACC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_tkt, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoHtl = "$newNumber/TKT-ACC/$romanMonth/$currentYear";

        $model = new tkt_transaction;
        $model->id = Str::uuid();
        $model->no_tkt = $newNoHtl;
        $model->no_sppd = $req->bisnis_numb;
        $model->user_id = $userId;
        $model->unit = $req->unit;
        $model->jk_tkt = $req->jk_tkt;
        $model->np_tkt = $req->np_tkt;
        $model->noktp_tkt = $req->noktp_tkt;
        $model->tlp_tkt = $req->tlp_tkt;
        $model->jenis_tkt = $req->jenis_tkt;
        $model->dari_tkt = $req->dari_tkt;
        $model->ke_tkt = $req->ke_tkt;
        $model->tgl_brkt_tkt = $req->tgl_brkt_tkt;
        $model->jam_brkt_tkt = $req->jam_brkt_tkt;
        $model->type_tkt = $req->type_tkt;
        $model->tgl_plg_tkt = $req->tgl_plg_tkt;
        $model->jam_plg_tkt = $req->jam_plg_tkt;
        $model->created_by = $userId;
        $model->save();

        Alert::success('Success');
        session()->flash('message', 'Berhasil di Tambahkan');
        return redirect()->intended(route('ticket', absolute: false));
    }
    function ticketEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = tkt_transaction::findByRouteKey($key);

        return view('hcis.reimbursements.ticket.editTicket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    public function ticketUpdate(Request $req, $key)
    {
        $userId = Auth::id();
        $model = tkt_transaction::findByRouteKey($key);
        $model->jk_tkt = $req->jk_tkt;
        $model->np_tkt = $req->np_tkt;
        $model->noktp_tkt = $req->noktp_tkt;
        $model->tlp_tkt = $req->tlp_tkt;
        $model->jenis_tkt = $req->jenis_tkt;
        $model->dari_tkt = $req->dari_tkt;
        $model->ke_tkt = $req->ke_tkt;
        $model->tgl_brkt_tkt = $req->tgl_brkt_tkt;
        $model->jam_brkt_tkt = $req->jam_brkt_tkt;
        $model->type_tkt = $req->type_tkt;
        $model->tgl_plg_tkt = $req->tgl_plg_tkt;
        $model->jam_plg_tkt = $req->jam_plg_tkt;
        $model->created_by = $userId;
        $model->save();

        Alert::success('Success');
        session()->flash('message', 'Berhasil di Edit');
        return redirect()->intended(route('ticket', absolute: false));
    }
    function ticketDelete($key)
    {
        $model = tkt_transaction::findByRouteKey($key);
        $model->delete();
        return redirect()->intended(route('ticket', absolute: false));
    }
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new CashAdvancedExport($startDate, $endDate), 'cash_advanced.xlsx');
    }
}
