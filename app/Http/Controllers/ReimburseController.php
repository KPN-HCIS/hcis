<?php

namespace App\Http\Controllers;

use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\CAApproval;
use App\Models\Hotel;
use Exception;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Designation;
use App\Models\ca_sett_approval;
use App\Models\ca_extend;
use App\Models\Location;
use App\Models\Employee;
use App\Models\MatrixApproval;
use App\Models\ListPerdiem;
use App\Models\HealthCoverage;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\CATransaction;
// use App\Http\Controllers\Log;
use App\Models\ca_approval;
use App\Models\htl_transaction;
use App\Models\Tiket;
use App\Models\TiketApproval;
use App\Models\master_holiday;
use App\Models\HotelApproval;
use App\Models\tkt_transaction;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashAdvancedExport;
use App\Exports\TicketExport;
use App\Exports\HotelExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

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

        if (request()->get('start_date') == '') {
        } else {
            if ($request->has(['start_date', 'end_date'])) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');

                $query->whereBetween('start_date', [$startDate, $endDate]);
            }
        }

        if (request()->get('from_date') == '') {
        } else {
            if ($request->has(['from_date', 'until_date'])) {
                $fromDate = $request->input('from_date');
                $untilDate = Carbon::parse($request->input('until_date'))->addDay();

                $query->whereBetween('created_at', [$fromDate, $untilDate]);
            }
        }

        if (request()->get('stat') == '-') {
        } else {
            if ($request->has('stat') && $request->input('stat') !== '') {
                $status = $request->input('stat');
                $query->where('ca_status', $status);
            }
        }

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

        $holiday = master_holiday::pluck('tanggal_libur');

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
            'holiday' => $holiday,
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
                // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key];
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);
                    // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);

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

                if ($employee_id != null) {
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
                if ($employee_id != null) {
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
                if ($employee_id != null) {
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
                if ($employee_id != null) {
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

    public function hotel(Request $request)
    {
        $userId = Auth::user();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        $query = Hotel::where('user_id', $userId->id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        $filter = $request->input('filter', 'request');

        // Apply filter to the query
        if ($filter === 'request') {
            $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        } elseif ($filter === 'rejected') {
            $statusFilter = ['Rejected'];
        }

        // Apply status filter to the query
        $query->whereIn('approval_status', $statusFilter);

        // Log::info('Filtered Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get the filtered tickets
        $hotelFilter = $query->get();

        // Fetch latest hotel entries grouped by 'no_htl'
        $latestHotelIds = Hotel::selectRaw('MAX(id) as id')
            ->where('user_id', $userId->id)
            ->groupBy('no_htl')
            ->pluck('id');

        // Fetch the hotel transactions using the latest ids
        $transactions = Hotel::whereIn('id', $latestHotelIds)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->whereIn('approval_status', $statusFilter)
            ->select('id', 'no_htl', 'nama_htl', 'lokasi_htl', 'approval_status', 'user_id', 'no_sppd')
            ->get();

        // Fetch all hotel transactions of the user
        $hotels = Hotel::where('user_id', $userId->id)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get();

        $hotel = Hotel::where('user_id', $userId->id)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_htl');

        $hotelIds = $hotels->pluck('id');

        // Fetch hotel approval details using the hotel IDs
        $hotelApprovals = HotelApproval::whereIn('htl_id', $hotelIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get();
        Log::info('Hotel Approvals:', $hotelApprovals->toArray());

        $hotelApprovals = $hotelApprovals->keyBy('htl_id');
        // dd($hotelApprovals);

        // Group transactions by hotel number
        $hotelGroups = $hotels->groupBy('no_htl');

        // Fetch employee data
        $employeeIds = $hotels->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Fetch manager IDs from the employees data
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();

        // Fetch manager names
        $managerL1Names = Employee::whereIn('employee_id', $managerL1Ids)->pluck('fullname');
        $managerL2Names = Employee::whereIn('employee_id', $managerL2Ids)->pluck('fullname');

        // Count grouped hotel entries
        $hotelCounts = $hotels->groupBy('no_htl')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.hotel.hotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
            'hotelCounts' => $hotelCounts,
            'hotels' => $hotels,
            'hotel' => $hotel,
            'hotelGroups' => $hotelGroups,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
            'hotelApprovals' => $hotelApprovals,
            'employeeName' => $employeeName,
            'filter' => $filter,
        ]);
    }

    function hotelCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Hotel';
        $link = 'Add Hotel Data';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
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

    private function generateNoSppdHtl()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Get the last transaction for the current year, including deleted ones
        $lastTransaction = Hotel::whereYear('created_at', $currentYear)
            ->orderBy('no_htl', 'desc')
            ->withTrashed()
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/HTLD-HRD\/([IVX]+)\/\d{4}/', $lastTransaction->no_htl, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/HTLD-HRD/$romanMonth/$currentYear";
        // dd($newNoSppd);

        return $newNoSppd;
    }

    public function hotelSubmit(Request $req)
    {
        $userId = Auth::id();
        $noSppdHtl = $this->generateNoSppdHtl();

        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }
        // Prepare the hotel data arrays
        $hotelData = [
            'nama_htl' => $req->nama_htl,
            'lokasi_htl' => $req->lokasi_htl,
            'jmlkmr_htl' => $req->jmlkmr_htl,
            'bed_htl' => $req->bed_htl,
            'tgl_masuk_htl' => $req->tgl_masuk_htl,
            'tgl_keluar_htl' => $req->tgl_keluar_htl,
            'total_hari' => $req->total_hari,
            'approval_status' => $statusValue,
        ];

        foreach ($hotelData['nama_htl'] as $key => $value) {
            // Only process if required fields are filled
            if (!empty($hotelData['nama_htl'][$key]) && !empty($hotelData['lokasi_htl'][$key]) && !empty($hotelData['tgl_masuk_htl'][$key])) {
                $model = new Hotel();
                $model->id = (string) Str::uuid();
                $model->no_htl = $noSppdHtl; // Use the pre-generated hotel number
                $model->no_sppd = $req->bisnis_numb;
                $model->user_id = $userId;
                $model->unit = $req->unit;
                $model->nama_htl = $hotelData['nama_htl'][$key];
                $model->lokasi_htl = $hotelData['lokasi_htl'][$key];
                $model->jmlkmr_htl = $hotelData['jmlkmr_htl'][$key] ?? null;
                $model->bed_htl = $hotelData['bed_htl'][$key] ?? null;
                $model->tgl_masuk_htl = $hotelData['tgl_masuk_htl'][$key] ?? null;
                $model->tgl_keluar_htl = $hotelData['tgl_keluar_htl'][$key] ?? null;
                $model->total_hari = $hotelData['total_hari'][$key] ?? null;
                $model->created_by = $userId;
                $model->approval_status = $statusValue;
                $model->hotel_only = 'Y';
                $model->created_by = $userId;
                // dd($statusValue);
                $model->save();
            }
        }

        // Update BusinessTrip record if applicable
        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $model->approval_status == 'Pending L1') {
            // Update the 'hotel' field to 'Ya'
            $bt->hotel = 'Ya';
            $bt->save();
        }
        return redirect('/hotel')->with('success', 'Hotel request input successfully');
    }

    public function hotelEdit($key)
    {
        $userId = Auth::id();

        // Define links for navigation
        $parentLink = 'Hotel';
        $link = 'Hotel Edit';

        // Fetch the specific hotel transaction by key
        $hotel = Hotel::findByRouteKey($key);

        // Check if the hotel transaction exists, if not redirect with an error message
        if (!$hotel) {
            return redirect()->route('hotel')->with('error', 'Hotel transaction not found');
        }

        // Fetch all hotel transactions associated with the same no_sppd for reference
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();

        // Prepare data for multiple forms
        $hotelData = [];
        $hotelCount = $hotels->count();
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'id' => $hotel->id, // Include ID for updating
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < $hotelCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.hotel.editHotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $hotels,
            'hotel' => $hotel,
            'hotelData' => $hotelData,
        ]);
    }


    public function hotelUpdate(Request $req, $key)
    {
        $hotelIds = $req->input('hotel_ids', []); // Get the array of existing hotel IDs
        $existingHotels = Hotel::whereIn('id', $hotelIds)->get()->keyBy('id'); // Load existing hotels into a collection

        $processedHotelIds = [];
        $updateBusinessTrip = false;

        // Determine approval status based on the action
        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }

        // Get the no_htl from the first existing hotel record
        $existingNoHtl = $existingHotels->first()->no_htl ?? null;
        // dd($existingNoHtl);

        // Loop through hotel data
        foreach ($req->nama_htl as $index => $value) {
            if (!empty($value)) {
                // Get hotel ID for this index
                $hotelId = $req->hotel_ids[$index] ?? null;

                // Prepare hotel data array
                $hotelData = [
                    'unit' => $req->unit,
                    'no_sppd' => $req->bisnis_numb,
                    'nama_htl' => $req->nama_htl[$index],
                    'lokasi_htl' => $req->lokasi_htl[$index],
                    'jmlkmr_htl' => $req->jmlkmr_htl[$index],
                    'bed_htl' => $req->bed_htl[$index],
                    'tgl_masuk_htl' => $req->tgl_masuk_htl[$index],
                    'tgl_keluar_htl' => $req->tgl_keluar_htl[$index],
                    'total_hari' => $req->total_hari[$index],
                    'approval_status' => $statusValue,
                    'jns_dinas_htl' => $req->jns_dinas_htl,
                    'hotel_only' => 'Y',
                ];

                // Check if hotel ID exists to decide if it's an update or a new entry
                if ($hotelId && isset($existingHotels[$hotelId])) {
                    $existingHotel = $existingHotels[$hotelId];
                    $hotelData['user_id'] = $existingHotel->user_id;

                    // Check if status is changing to "Pending L1"
                    if ($existingHotel->approval_status != 'Pending L1' && $hotelData['approval_status'] == 'Pending L1') {
                        $updateBusinessTrip = true;
                    }

                    // Update existing hotel record
                    $existingHotel->update($hotelData);
                    $processedHotelIds[] = $hotelId; // Keep track of processed hotel IDs
                } else {
                    // If hotel ID doesn't exist, create a new hotel record
                    // Use the existing no_htl from the first hotel
                    $newHotel = Hotel::create(array_merge($hotelData, [
                        'id' => (string) Str::uuid(), // Auto-generated UUID
                        'no_htl' => $existingNoHtl, // Use the existing no_htl
                        'user_id' => Auth::id(),
                        'created_by' => Auth::id(),
                        'hotel_only' => 'Y',
                    ]));

                    // dd($newHotel);
                    $processedHotelIds[] = $newHotel->id; // Keep track of processed hotel IDs

                    // Check if the new hotel is created with "Pending L1" status
                    if ($hotelData['approval_status'] == 'Pending L1') {
                        $updateBusinessTrip = true;
                    }
                }
            }
        }

        // Update BusinessTrip if status changed to "Pending L1" for any hotel
        if ($updateBusinessTrip) {
            $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
            if ($bt) {
                $bt->hotel = 'Ya';
                $bt->save();
            }
        }
        // dd([$hotelIds, $processedHotelIds]);
        // Delete hotels with the same no_htl but not in the processedHotelIds
        Hotel::where('no_htl', $existingNoHtl)
            ->whereNotIn('id', $processedHotelIds)
            ->delete();

        // Show success or warning message
        // if (count($processedHotelIds) > 0) {
        //     Alert::success('Success', "Hotels updated successfully");
        // } else {
        //     Alert::warning('Warning', "No hotels were updated.");
        // }

        return redirect('/hotel')->with('success', 'Hotel request updated successfully');
    }



    public function hotelDelete($key)
    {
        // Find the hotel record by its primary key
        $model = Hotel::findByRouteKey($key);

        if ($model) {
            // Retrieve the no_htl value of the hotel to delete all hotels with the same no_htl
            $noHtl = $model->no_htl;

            // Delete all hotels with the same no_htl
            Hotel::where('no_htl', $noHtl)->delete();
        }

        // Redirect after deletion
        return redirect()->intended(route('hotel', absolute: false))->with('success', 'All related hotels deleted successfully');
    }


    public function hotelExport($id)
    {
        // Find the hotel by ID
        $hotel = Hotel::findOrFail($id);

        // Retrieve all hotels with the same `no_htl`
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();

        // Prepare the data to be passed to the PDF view
        $data = [
            'hotel' => $hotel,
            'hotels' => $hotels
        ];

        // Load the view and pass the data
        $pdf = PDF::loadView('hcis.reimbursements.hotel.hotel_pdf', $data);

        // Stream the generated PDF to the browser, opening in a new tab
        return $pdf->stream('Hotel.pdf');
    }



    public function hotelApproval()
    {
        $user = Auth::user();
        $userId = $user->id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record
        $employeeId = auth()->user()->employee_id;

        $parentLink = 'Approval';
        $link = 'Hotel Approval';

        // Get unique ticket numbers with conditions
        $hotelNumbers = Hotel::where('hotel_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_htl')->unique();
        // dd($ticketNumbers);

        // Fetch all tickets using the latestTicketIds
        $transactions = Hotel::whereIn('no_htl', $hotelNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter tickets based on manager and approval status
        $hotels = $transactions->filter(function ($hotel) use ($employee) {
            // Get the employee who owns the ticket
            $ticketOwnerEmployee = Employee::where('id', $hotel->user_id)->first();

            if ($hotel->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) {
                return true;
            } elseif ($hotel->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id) {
                return true;
            }
            return false;
        });

        $totalHTLCount = $hotels->count();

        $ticketNumbers = Tiket::where('tkt_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_tkt')->unique();
        $transactions_tkt = Tiket::whereIn('no_tkt', $ticketNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();
        $totalTKTCount = $transactions_tkt->filter(function ($ticket) use ($employee) {
            $ticketOwnerEmployee = Employee::where('id', $ticket->user_id)->first();
            return ($ticket->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) ||
                ($ticket->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id);
        })->count();

        $totalPendingCount = CATransaction::where(function ($query) use ($employeeId) {
            $query->where('status_id', $employeeId)->where('approval_status', 'Pending')
                ->orWhere('sett_id', $employeeId)->where('approval_sett', 'Pending')
                ->orWhere('extend_id', $employeeId)->where('approval_extend', 'Pending');
        })->count();

        $totalBTCount = BusinessTrip::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_l1_id', $user->employee_id)
                    ->whereIn('status', ['Pending L1', 'Declaration L1']);
            })->orWhere(function ($q) use ($user) {
                $q->where('manager_l2_id', $user->employee_id)
                    ->whereIn('status', ['Pending L2', 'Declaration L2']);
            });
        })->count();

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
                DB::raw('SUM(CASE WHEN medical_type = "Maternity" THEN balance_verif ELSE 0 END) as maternity_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance_verif ELSE 0 END) as inpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance_verif ELSE 0 END) as outpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance_verif ELSE 0 END) as glasses_balance_verif'),
                'status'
            )
                ->whereNotNull('verif_by')   // Only include records where verif_by is not null
                ->whereNotNull('balance_verif')
                ->where('status', 'Pending')
                ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            // Add usage_id for each medical record without filtering by employee_id
            $medical = $medicalGroup->map(function ($item) {
                // Fetch the usage_id based on no_medic (for any employee)
                $usageId = HealthCoverage::where('no_medic', $item->no_medic)->value('usage_id');
                $item->usage_id = $usageId;

                // Calculate total per no_medic
                $item->total_per_no_medic = $item->maternity_balance_verif + $item->inpatient_balance_verif + $item->outpatient_balance_verif + $item->glasses_balance_verif;

                return $item;
            });
        } else {
            $medical = collect(); // Empty collection if user doesn't have approval rights
        }

        $totalMDCCount = $medical->count();

        // Group tickets by `no_tkt` and get the first entry for each group
        $hotelGroups = $hotels->groupBy('no_htl')->map(function ($group) {
            return $group->first();
        });

        // dd($tickets);

        // Group tickets by `no_tkt`
        $hotel = $hotels->groupBy('no_htl');

        // Fetch employee data for ticket owners
        $employeeIds = $hotels->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

        // Fetch manager names for display (optional, depending on your view)
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();
        $managerL1Names = Employee::whereIn('id', $managerL1Ids)->pluck('fullname', 'id');
        $managerL2Names = Employee::whereIn('id', $managerL2Ids)->pluck('fullname', 'id');

        // Count tickets per `no_tkt`
        $hotelCounts = $hotels->groupBy('no_htl')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.hotel.hotelApproval', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $hotelGroups,
            'hotelCounts' => $hotelCounts,
            'hotels' => $hotels,
            'hotel' => $hotel,
            'totalPendingCount' => $totalPendingCount,
            'totalBTCount' => $totalBTCount,
            'totalTKTCount' => $totalTKTCount,
            'totalHTLCount' => $totalHTLCount,
            'totalMDCCount' => $totalMDCCount,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
        ]);
    }

    public function hotelApprovalDetail($key)
    {
        // Define links for navigation
        $parentLink = 'Hotel Approval';
        $link = 'Request Detail';

        $hotel = Hotel::findByRouteKey($key);

        // Check if the ticket exists, if not redirect with an error message
        if (!$hotel) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();
        $userId = Hotel::where('no_htl', $hotel->no_htl)->pluck('user_id')->first();
        // dd($userId);

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        // dd($employee_data);
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();

        $hotelOwnerEmployee = Employee::where('id', $hotel->user_id)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $hotels;

        $hotelData = [];
        $hotelCount = $hotels->count();
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'id' => $hotel->id, // Include ID for updating
                'no_htl' => $hotel->no_htl,
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < $hotelCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.hotel.hotelApprovalDetail', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'hotel' => $hotel,
            'hotelData' => $hotelData,
            'hotelOwnerEmployee' => $hotelOwnerEmployee,
            'hotelCount' => $hotelCount,
        ]);
    }

    public function updateStatusHotel($id, Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Find the hotel by ID
        $hotel = Hotel::findOrFail($id);
        $noHtl = $hotel->no_htl;

        // Check the provided status_approval input
        $statusApproval = $request->input('status_approval');

        // Handle rejection scenario
        if ($statusApproval == 'Rejected') {
            $rejectInfo = $request->input('reject_info');

            // Get the current approval status before updating it
            $currentApprovalStatus = $hotel->approval_status;

            // Update all hotels with the same no_htl to 'Rejected'
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Rejected']);

            // Log the rejection into the hotel_approvals table for all hotels with the same no_htl
            $hotels = Hotel::where('no_htl', $noHtl)->get();
            foreach ($hotels as $hotel) {
                $rejection = new HotelApproval();
                $rejection->id = (string) Str::uuid();
                $rejection->htl_id = $hotel->id;
                $rejection->employee_id = $employeeId;

                // Determine the correct layer based on the hotel's approval status BEFORE rejection
                $rejection->layer = $currentApprovalStatus == 'Pending L2' ? 2 : 1;

                $rejection->approval_status = 'Rejected';
                $rejection->approved_at = now();
                $rejection->reject_info = $rejectInfo;
                $rejection->save();
            }

            return redirect('/hotel/approval')->with('success', 'Request approved successfully');
        }

        // Handle approval scenarios
        if ($hotel->approval_status == 'Pending L1') {
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Pending L2']);
        } elseif ($hotel->approval_status == 'Pending L2') {
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Approved']);
        } else {
            return redirect()->back()->with('error', 'Invalid status update.');
        }

        // Log the approval into the hotel_approvals table for all hotels with the same no_htl
        $hotels = Hotel::where('no_htl', $noHtl)->get();
        foreach ($hotels as $hotel) {
            $approval = new HotelApproval();
            $approval->id = (string) Str::uuid();
            $approval->htl_id = $hotel->id;
            $approval->employee_id = $employeeId;
            $approval->layer = $hotel->approval_status == 'Pending L2' ? 1 : 2;
            $approval->approval_status = $hotel->approval_status;
            $approval->approved_at = now();
            $approval->save();
        }

        // Redirect to the hotel approval page
        return redirect('/hotel/approval')->with('success', 'Request approved successfully');
    }

    public function hotelAdmin(Request $request)
    {
        $parentLink = 'Reimbursement';
        $link = 'Hotel (Admin)';

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $permissionLocations = $this->permissionLocations;
        $permissionCompanies = $this->permissionCompanies;
        $permissionGroupCompanies = $this->permissionGroupCompanies;

        // Fetch latest hotel entries grouped by 'no_htl'
        $latestHotelIds = Hotel::selectRaw('MAX(id) as id')
            ->groupBy('no_htl')
            ->pluck('id');

        // Fetch the hotel transactions using the latest ids
        $transactions = Hotel::whereIn('id', $latestHotelIds)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->where('approval_status', '!=', 'Draft')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereRaw("DATE(tgl_masuk_htl) BETWEEN ? AND ?", [$startDate, $endDate]);
            });

        // Apply permission filters
        if (!empty($permissionLocations)) {
            $transactions->whereHas('employee', function ($query) use ($permissionLocations) {
                $query->whereIn('work_area_code', $permissionLocations);
            });
        }

        if (!empty($permissionCompanies)) {
            $transactions->whereIn('contribution_level_code', $permissionCompanies);
        }

        if (!empty($permissionGroupCompanies)) {
            $transactions->whereHas('employee', function ($query) use ($permissionGroupCompanies) {
                $query->whereIn('group_company', $permissionGroupCompanies);
            });
        }

        $transactions = $transactions->select('id', 'no_htl', 'nama_htl', 'lokasi_htl', 'approval_status', 'user_id', 'no_sppd')->get();

        // Fetch all hotel transactions, removing the user ID filter
        $hotels = Hotel::with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get();

        $hotel = Hotel::with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_htl');

        $hotelIds = $hotels->pluck('id');

        // Fetch hotel approval details using the hotel IDs
        $hotelApprovals = HotelApproval::whereIn('htl_id', $hotelIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get()
            ->keyBy('htl_id');

        // Group transactions by hotel number
        $hotelGroups = $hotels->groupBy('no_htl');

        foreach ($transactions as $transaction) {
            // Fetch the employee for the current transaction
            $employee = Employee::find($transaction->user_id);

            // If the employee exists, fetch their manager names
            if ($employee) {
                $managerL1Id = $employee->manager_l1_id;
                $managerL2Id = $employee->manager_l2_id;

                $managerL1 = Employee::where('employee_id', $managerL1Id)->first();
                $managerL2 = Employee::where('employee_id', $managerL2Id)->first();

                $managerL1Name = $managerL1 ? $managerL1->fullname : 'Unknown';
                $managerL2Name = $managerL2 ? $managerL2->fullname : 'Unknown';
            }
        }

        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Count grouped hotel entries
        $hotelCounts = $hotels->groupBy('no_htl')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.hotel.admin.hotelAdmin', [
            'link' => $link,
            'parentLink' => $parentLink,
            'transactions' => $transactions,
            'hotelCounts' => $hotelCounts,
            'hotels' => $hotels,
            'hotel' => $hotel,
            'hotelGroups' => $hotelGroups,
            'managerL1Name' => $managerL1Name,
            'managerL2Name' => $managerL2Name,
            'hotelApprovals' => $hotelApprovals,
            'employeeName' => $employeeName,
            // 'filter' => $filter,
        ]);
    }


    public function hotelBookingAdmin(Request $req, $id)
    {
        // dd($id);
        $userId = Auth::id();
        $employeeId = auth()->user()->employee_id;
        // Ambil tiket berdasarkan ID yang diterima
        $model = Hotel::where('id', $id)->firstOrFail();
        $no_htl = $model->no_htl;
        $hotels = Hotel::where('no_htl', $no_htl)->with('businessTrip')->get();

        // Jika tombol booking hotel ditekan
        if ($req->has('action_htl_book')) {
            // Validasi input dari form
            $req->validate([
                'booking_code' => 'required|string|max:255',
                'booking_price' => 'required|numeric|min:0',
            ]);

            foreach ($hotels as $hotel) {
                $hotel->booking_code = $req->input('booking_code');
                $hotel->booking_price = $req->input('booking_price');
                $hotel->save();
            }

            return redirect()->route('hotel.admin')->with('success', 'Hotel booking updated successfully.');
        }
    }
    public function exportHotelAdminExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new HotelExport($startDate, $endDate), 'hotel_report.xlsx');
    }

    public function hotelDeleteAdmin($key)
    {
        // Find the hotel record by its primary key
        $model = Hotel::findByRouteKey($key);

        if ($model) {
            // Retrieve the no_htl value of the hotel to delete all hotels with the same no_htl
            $noHtl = $model->no_htl;

            // Delete all hotels with the same no_htl
            Hotel::where('no_htl', $noHtl)->delete();
        }

        // Redirect after deletion
        return redirect()->intended(route('hotel.admin', absolute: false))->with('success', 'All related hotels deleted successfully');
    }


    public function ticket(Request $request)
    {
        $userId = Auth::user();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        // Base query for filtering
        $query = Tiket::where('user_id', $userId->id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        $filter = $request->input('filter', 'request');

        // Apply filter to the query
        if ($filter === 'request') {
            $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        } elseif ($filter === 'rejected') {
            $statusFilter = ['Rejected'];
        }

        // Apply status filter to the query
        $query->whereIn('approval_status', $statusFilter);

        // Log::info('Filtered Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get the filtered tickets
        $ticketsFilter = $query->get();

        // Fetch latest ticket IDs
        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->where('user_id', $userId->id)
            ->groupBy('no_tkt')
            ->pluck('id');

        // Get transactions with the latest ticket IDs
        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->with('businessTrip')
            ->whereIn('approval_status', $statusFilter) // Apply the same filter to transactions
            ->orderBy('created_at', 'desc')
            ->select('id', 'no_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();
        // Get all tickets for user
        $tickets = Tiket::where('user_id', $userId->id)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group tickets by 'no_tkt'
        $ticket = $tickets->groupBy('no_tkt');

        // Get ticket IDs
        $tiketIds = $tickets->pluck('id');

        // Get ticket approvals
        $ticketApprovals = TiketApproval::whereIn('tkt_id', $tiketIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get();

        // Log ticket approvals
        Log::info('Ticket Approvals:', $ticketApprovals->toArray());

        // Key ticket approvals by ticket ID
        $ticketApprovals = $ticketApprovals->keyBy('tkt_id');

        // Group transactions by 'no_tkt'
        $ticketsGroups = $tickets->groupBy('no_tkt');

        // Fetch employee data
        $employeeIds = $tickets->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Fetch manager IDs from employee data
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();

        // Fetch manager names
        $managerL1Names = Employee::whereIn('employee_id', $managerL1Ids)->pluck('fullname');
        $managerL2Names = Employee::whereIn('employee_id', $managerL2Ids)->pluck('fullname');

        // Count tickets grouped by 'no_tkt'
        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        // Return the view with all the data
        return view('hcis.reimbursements.ticket.ticket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'ticketsGroups' => $ticketsGroups,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
            'ticketApprovals' => $ticketApprovals,
            'employeeName' => $employeeName,
            'filter' => $filter,
            'ticketsFilter' => $ticketsFilter,
        ]);
    }


    public function ticketCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Ticket';
        $link = 'Add Ticket Data';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $employees = Employee::orderBy('ktp')->get();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
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
            'employees' => $employees,
        ]);
    }
    public function ticketSubmit(Request $req)
    {
        $userId = Auth::id();
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

        function generateTicketNumber($type)
        {

            $currentYear = date('Y');
            $currentMonth = date('n');
            $romanMonth = getRomanMonth_tkt($currentMonth);

            // Determine the prefix based on the type
            $prefix = '';
            if ($type === 'Dinas') {
                $prefix = 'TKTD-HRD';
            } elseif ($type === 'Cuti') {
                $prefix = 'TKTC-HRD';
            } else {
                throw new Exception('Invalid ticket type');
            }

            // Get the last transaction of the current year and month for the specific type
            $lastTransaction = Tiket::whereYear('created_at', $currentYear)
                ->where('no_tkt', 'like', "%/$prefix/%/$currentYear")
                ->orderBy('no_tkt', 'desc')
                ->withTrashed()
                ->first();
            // dd($lastTransaction);
            // Determine the new ticket number
            if ($lastTransaction && preg_match('/(\d{3})\/' . preg_quote($prefix, '/') . '\/[^\/]+\/' . $currentYear . '/', $lastTransaction->no_tkt, $matches)) {
                $lastNumber = intval($matches[1]);
            } else {
                $lastNumber = 0;
            }
            // dd($lastNumber);

            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $newNoTkt = "$newNumber/$prefix/$romanMonth/$currentYear";
            // dd($newNoTkt);

            return $newNoTkt;
        }

        // Determine the ticket type from the request
        $ticketType = $req->jns_dinas_tkt == 'Dinas' ? 'Dinas' : 'Cuti';
        // Generate the ticket number for the entire submission
        $newNoTkt = generateTicketNumber($ticketType);

        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }

        // Prepare the ticket data arrays
        $ticketData = [
            'noktp_tkt' => $req->noktp_tkt,
            'tlp_tkt' => $req->tlp_tkt,
            'jk_tkt' => $req->jk_tkt,
            'np_tkt' => $req->np_tkt,
            'dari_tkt' => $req->dari_tkt,
            'ke_tkt' => $req->ke_tkt,
            'tgl_brkt_tkt' => $req->tgl_brkt_tkt,
            'tgl_plg_tkt' => $req->tgl_plg_tkt,
            'jam_brkt_tkt' => $req->jam_brkt_tkt,
            'jam_plg_tkt' => $req->jam_plg_tkt,
            'jenis_tkt' => $req->jenis_tkt,
            'type_tkt' => $req->type_tkt,
            'ket_tkt' => $req->ket_tkt,
            'approval_status' => $statusValue,
            'tkt_only' => 'Y',
        ];

        foreach ($ticketData['noktp_tkt'] as $key => $value) {
            // Only process if the required fields are filled
            if (!empty($ticketData['noktp_tkt'][$key]) && !empty($ticketData['jk_tkt'][$key]) && !empty($ticketData['np_tkt'][$key])) {
                $tiket = new Tiket();
                $tiket->id = (string) Str::uuid();

                // Use the pre-generated ticket number
                $tiket->no_tkt = $newNoTkt;

                $userId = Auth::id();
                $tiket->no_sppd = $req->bisnis_numb;
                $tiket->user_id = $userId;
                $tiket->unit = $req->unit;
                $tiket->jk_tkt = $ticketData['jk_tkt'][$key];
                $tiket->np_tkt = $ticketData['np_tkt'][$key];
                $tiket->noktp_tkt = $ticketData['noktp_tkt'][$key];
                $tiket->tlp_tkt = $ticketData['tlp_tkt'][$key];
                $tiket->dari_tkt = $ticketData['dari_tkt'][$key] ?? null;
                $tiket->ke_tkt = $ticketData['ke_tkt'][$key] ?? null;
                $tiket->tgl_brkt_tkt = $ticketData['tgl_brkt_tkt'][$key] ?? null;
                $tiket->tgl_plg_tkt = $ticketData['tgl_plg_tkt'][$key] ?? null;
                $tiket->jam_brkt_tkt = $ticketData['jam_brkt_tkt'][$key] ?? null;
                $tiket->jam_plg_tkt = $ticketData['jam_plg_tkt'][$key] ?? null;
                $tiket->jenis_tkt = $ticketData['jenis_tkt'][$key] ?? null;
                $tiket->type_tkt = $ticketData['type_tkt'][$key] ?? null;
                $tiket->ket_tkt = $ticketData['ket_tkt'][$key] ?? null;
                $tiket->approval_status = $statusValue;
                $tiket->jns_dinas_tkt = $req->jns_dinas_tkt;
                $tiket->tkt_only = 'Y';
                // dd($req->all());
                $tiket->save();
            }
        }

        // Update BusinessTrip record
        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $tiket->approval_status == 'Pending L1') {
            $bt->tiket = 'Ya';
            $bt->save();
        }

        return redirect()->route('ticket')->with('success', 'The ticket request has been input successfully.');
    }


    public function ticketEdit($key)
    {
        $userId = Auth::id();

        // Define links for navigation
        $parentLink = 'Ticket';
        $link = 'Edit Ticket';

        $ticket = Tiket::findByRouteKey($key);
        // dd($ticket);

        // Check if the ticket exists, if not redirect with an error message
        if (!$ticket) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $employees = Employee::orderBy('ktp')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $tickets;

        $ticketData = [];
        $ticketCount = $tickets->count();
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'id' => $ticket->id,
                'noktp_tkt' => $ticket->noktp_tkt,
                'tlp_tkt' => $ticket->tlp_tkt,
                'jk_tkt' => $ticket->jk_tkt,
                'np_tkt' => $ticket->np_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < $ticketCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
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
            'ticket' => $ticket,
            'ticketData' => $ticketData,
            'employees' => $employees,
        ]);
    }

    public function ticketUpdate(Request $req)
    {
        // Get all existing tickets for this business trip
        $ticketIds = $req->input('ticket_ids', []);
        $existingTickets = Tiket::where('id', $ticketIds)->get()->keyBy('id');
        // dd($req->all());
        // dd($existingTickets);

        $processedTicketIds = [];
        $firstNoTkt = null;

        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }

        $existingNoTkt = $existingTickets->first()->no_tkt ?? null;
        // dd($existingNoTkt);
        // dd($req->noktp_tkt);
        foreach ($req->noktp_tkt as $key => $value) {
            if (!empty($value)) {
                $ticketData = [
                    'no_sppd' => $req->bisnis_numb,
                    'user_id' => Auth::id(),
                    'unit' => $req->unit,
                    'dari_tkt' => $req->dari_tkt[$key] ?? null,
                    'ke_tkt' => $req->ke_tkt[$key] ?? null,
                    'tgl_brkt_tkt' => $req->tgl_brkt_tkt[$key] ?? null,
                    'jam_brkt_tkt' => $req->jam_brkt_tkt[$key] ?? null,
                    'jenis_tkt' => $req->jenis_tkt[$key] ?? null,
                    'type_tkt' => $req->type_tkt[$key] ?? null,
                    'tgl_plg_tkt' => $req->tgl_plg_tkt[$key] ?? null,
                    'jam_plg_tkt' => $req->jam_plg_tkt[$key] ?? null,
                    'ket_tkt' => $req->ket_tkt[$key] ?? null,
                    'jk_tkt' => $req->jk_tkt[$key] ?? null,
                    'np_tkt' => $req->np_tkt[$key] ?? null,
                    'tlp_tkt' => $req->tlp_tkt[$key] ?? null,
                    'approval_status' => $statusValue,
                    'jns_dinas_tkt' => $req->jns_dinas_tkt,
                    'tkt_only' => 'Y',
                ];


                if (isset($existingTickets[$value])) {
                    $existingTicket = $existingTickets[$value];
                    // dd($ticketData);
                    $existingTicket->update($ticketData);
                } else {
                    if (is_null($firstNoTkt) && $existingTickets->isNotEmpty()) {
                        $firstNoTkt = $existingTickets->first()->no_tkt;
                    }

                    // If there's still no firstNoTkt, generate a new one
                    if (is_null($firstNoTkt)) {
                        $firstNoTkt = generateTicketNumber($req->jns_dinas_tkt);
                    }

                    // Create a new ticket entry
                    $newTiket = Tiket::create(array_merge($ticketData, [
                        'id' => (string) Str::uuid(),
                        'no_tkt' => $firstNoTkt,
                        'noktp_tkt' => $value,
                        'tkt_only' => 'Y',
                    ]));
                }

                // Track the processed ticket IDs
                $processedTicketIds[] = $newTiket->id;
                // dd($processedTicketIds);
            }
        }
        // dd([$existingNoTkt, $processedTicketIds]);

        // Soft delete tickets that are no longer in the request
        Tiket::where('no_tkt', $existingNoTkt)
            ->whereNotIn('id', $processedTicketIds)
            ->delete();

        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $req->status == 'Pending L1') {
            $bt->tiket = 'Ya';
            $bt->save();
        }


        return redirect()->route('ticket')->with('success', 'The ticket request has been updated successfully.');
    }
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $fromDate = $request->input('from_date');
        $untilDate = Carbon::parse($request->input('until_date'))->addDay();
        $stat = $request->input('stat');
        // dd($startDate, $endDate, $fromDate, $untilDate, $stat);

        return Excel::download(new CashAdvancedExport($startDate, $endDate, $fromDate, $untilDate, $stat), 'cash_advanced.xlsx');
    }

    public function ticketDelete($key)
    {
        $model = Tiket::findByRouteKey($key);
        Tiket::where('no_tkt', $model->no_tkt)->delete();

        // Redirect to the ticket page with a success message
        return redirect()->route('ticket')->with('success', 'Tickets has been deleted');
    }
    public function ticketExport($id)
    {
        // Fetch all tickets related to the provided ID (assuming 'no_sppd' is the common field)
        $ticket = Tiket::findOrFail($id);
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();

        // Prepare the data to be passed to the PDF view
        $data = [
            'ticket' => $ticket,
            'passengers' => $tickets->map(function ($ticket) {
                return (object) [
                    'np_tkt' => $ticket->np_tkt,
                    'tlp_tkt' => $ticket->tlp_tkt,
                    'jk_tkt' => $ticket->jk_tkt,
                    'dari_tkt' => $ticket->dari_tkt,
                    'ke_tkt' => $ticket->ke_tkt,
                    'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                    'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                    'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                    'jam_plg_tkt' => $ticket->jam_plg_tkt,
                    'type_tkt' => $ticket->type_tkt,
                    'jenis_tkt' => $ticket->jenis_tkt,
                    'company_name' => $ticket->employee->company_name ?? 'N/A',
                    'cost_center' => $ticket->cost_center ?? 'N/A'
                ];
            })
        ];

        // Load the view and pass the data
        $pdf = PDF::loadView('hcis.reimbursements.ticket.tiket_pdf', $data);

        // Stream the generated PDF to the browser, opening in a new tab
        return $pdf->stream('Ticket.pdf');
    }

    public function ticketApproval()
    {
        $user = Auth::user();
        $userId = $user->id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record
        $employeeId = auth()->user()->employee_id;

        $parentLink = 'Approval';
        $link = 'Ticket Approval';

        // Get unique ticket numbers with conditions
        $ticketNumbers = Tiket::where('tkt_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_tkt')->unique();
        // dd($ticketNumbers);

        // Fetch all tickets using the latestTicketIds
        $transactions = Tiket::whereIn('no_tkt', $ticketNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();


        // Filter tickets based on manager and approval status
        $tickets = $transactions->filter(function ($ticket) use ($employee) {
            // Get the employee who owns the ticket
            $ticketOwnerEmployee = Employee::where('id', $ticket->user_id)->first();

            if ($ticket->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) {
                return true;
            } elseif ($ticket->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id) {
                return true;
            }
            return false;
        });

        // Group tickets by `no_tkt` and get the first entry for each group
        $ticketGroups = $tickets->groupBy('no_tkt')->map(function ($group) {
            return $group->first();
        });

        // dd($tickets);

        // Group tickets by `no_tkt`
        $ticket = $tickets->groupBy('no_tkt');

        // Fetch employee data for ticket owners
        $employeeIds = $tickets->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

        // Fetch manager names for display (optional, depending on your view)
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();
        $managerL1Names = Employee::whereIn('id', $managerL1Ids)->pluck('fullname', 'id');
        $managerL2Names = Employee::whereIn('id', $managerL2Ids)->pluck('fullname', 'id');

        // Count tickets per `no_tkt`
        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        $totalTKTCount = $tickets->count();

        $totalPendingCount = CATransaction::where(function ($query) use ($employeeId) {
            $query->where('status_id', $employeeId)->where('approval_status', 'Pending')
                ->orWhere('sett_id', $employeeId)->where('approval_sett', 'Pending')
                ->orWhere('extend_id', $employeeId)->where('approval_extend', 'Pending');
        })->count();

        $totalBTCount = BusinessTrip::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_l1_id', $user->employee_id)
                    ->whereIn('status', ['Pending L1', 'Declaration L1']);
            })->orWhere(function ($q) use ($user) {
                $q->where('manager_l2_id', $user->employee_id)
                    ->whereIn('status', ['Pending L2', 'Declaration L2']);
            });
        })->count();

        $hotelNumbers = Hotel::where('hotel_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_htl')->unique();

        // Fetch all tickets using the latestTicketIds
        $transactions_htl = Hotel::whereIn('no_htl', $hotelNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter tickets based on manager and approval status
        $hotels = $transactions_htl->filter(function ($hotel) use ($employee) {
            // Get the employee who owns the ticket
            $ticketOwnerEmployee = Employee::where('id', $hotel->user_id)->first();

            if ($hotel->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) {
                return true;
            } elseif ($hotel->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id) {
                return true;
            }
            return false;
        });

        $totalHTLCount = $hotels->count();

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
                DB::raw('SUM(CASE WHEN medical_type = "Maternity" THEN balance_verif ELSE 0 END) as maternity_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Inpatient" THEN balance_verif ELSE 0 END) as inpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Outpatient" THEN balance_verif ELSE 0 END) as outpatient_balance_verif'),
                DB::raw('SUM(CASE WHEN medical_type = "Glasses" THEN balance_verif ELSE 0 END) as glasses_balance_verif'),
                'status'
            )
                ->whereNotNull('verif_by')   // Only include records where verif_by is not null
                ->whereNotNull('balance_verif')
                ->where('status', 'Pending')
                ->groupBy('no_medic', 'date', 'period', 'hospital_name', 'patient_name', 'disease', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            // Add usage_id for each medical record without filtering by employee_id
            $medical = $medicalGroup->map(function ($item) {
                // Fetch the usage_id based on no_medic (for any employee)
                $usageId = HealthCoverage::where('no_medic', $item->no_medic)->value('usage_id');
                $item->usage_id = $usageId;

                // Calculate total per no_medic
                $item->total_per_no_medic = $item->maternity_balance_verif + $item->inpatient_balance_verif + $item->outpatient_balance_verif + $item->glasses_balance_verif;

                return $item;
            });
        } else {
            $medical = collect(); // Empty collection if user doesn't have approval rights
        }

        $totalMDCCount = $medical->count();

        return view('hcis.reimbursements.ticket.ticketApproval', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $ticketGroups,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'totalPendingCount' => $totalPendingCount,
            'totalBTCount' => $totalBTCount,
            'totalTKTCount' => $totalTKTCount,
            'totalMDCCount' => $totalMDCCount,
            'totalHTLCount' => $totalHTLCount,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
        ]);
    }

    public function ticketApprovalDetail($key)
    {
        // Define links for navigation
        $parentLink = 'Ticket Approval';
        $link = 'Approval Detail';

        $ticket = Tiket::findByRouteKey($key);

        // Check if the ticket exists, if not redirect with an error message
        if (!$ticket) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();
        $userId = Tiket::where('no_tkt', $ticket->no_tkt)->pluck('user_id')->first();
        // dd($userId);

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        // dd($employee_data);
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();

        $ticketOwnerEmployee = Employee::where('id', $ticket->user_id)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $tickets;

        $ticketData = [];
        $ticketCount = $tickets->count();
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'id' => $ticket->id,
                'no_tkt' => $ticket->no_tkt,
                'noktp_tkt' => $ticket->noktp_tkt,
                'tlp_tkt' => $ticket->tlp_tkt,
                'jk_tkt' => $ticket->jk_tkt,
                'np_tkt' => $ticket->np_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < $ticketCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.ticket.ticketApprovalDetail', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'ticket' => $ticket,
            'ticketData' => $ticketData,
            'ticketOwnerEmployee' => $ticketOwnerEmployee,
            'ticketCount' => $ticketCount,
        ]);
    }

    public function updateStatusTicket($id, Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Find the ticket by ID
        $ticket = Tiket::findOrFail($id);
        $noTkt = $ticket->no_tkt;

        // Check the provided status_approval input
        $statusApproval = $request->input('status_approval');

        // Handle rejection scenario
        if ($statusApproval == 'Rejected') {

            $rejectInfo = $request->input('reject_info');

            // Get the current approval status before updating it
            $currentApprovalStatus = $ticket->approval_status;

            // Update all tickets with the same no_tkt to 'Rejected'
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Rejected']);

            // Log the rejection into the tkt_approvals table for all tickets with the same no_tkt
            $tickets = Tiket::where('no_tkt', $noTkt)->get();
            foreach ($tickets as $ticket) {
                $rejection = new TiketApproval();
                $rejection->id = (string) Str::uuid();
                $rejection->tkt_id = $ticket->id;
                $rejection->employee_id = $employeeId;

                // Determine the correct layer based on the ticket's approval status BEFORE rejection
                if ($currentApprovalStatus == 'Pending L2') {
                    $rejection->layer = 2; // Layer 2 if ticket was at L2
                } else {
                    $rejection->layer = 1; // Otherwise, it's Layer 1
                }

                $rejection->approval_status = 'Rejected';
                $rejection->approved_at = now();
                $rejection->reject_info = $rejectInfo;
                $rejection->save();
            }

            // Redirect to the ticket approval page instead of back to the same page
            return redirect('/ticket/approval')->with('success', 'Request rejected successfully');
        }

        // dd($ticket->approval_status);
        // If not rejected, proceed with normal approval process
        if ($ticket->approval_status == 'Pending L1') {
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Pending L2']);
        } elseif ($ticket->approval_status == 'Pending L2') {
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Approved']);
        } else {
            return redirect()->back()->with('error', 'Invalid status update.');
        }

        // Log the approval into the tkt_approvals table for all tickets with the same no_tkt
        $tickets = Tiket::where('no_tkt', $noTkt)->get();
        foreach ($tickets as $ticket) {
            $approval = new TiketApproval();
            $approval->id = (string) Str::uuid();
            $approval->tkt_id = $ticket->id;
            $approval->employee_id = $employeeId;
            $approval->layer = $ticket->approval_status == 'Pending L2' ? 1 : 2;
            $approval->approval_status = $ticket->approval_status;
            $approval->approved_at = now();
            $approval->save();
        }

        return redirect('/ticket/approval')->with('success', 'Request approved successfully');
    }

    public function ticketAdmin(Request $request)
    {
        $parentLink = 'Reimbursement';
        $link = 'Ticket (Admin)';

        // Base query for filtering without user-specific filtering
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $permissionLocations = $this->permissionLocations;
        $permissionCompanies = $this->permissionCompanies;
        $permissionGroupCompanies = $this->permissionGroupCompanies;

        // Fetch latest ticket IDs
        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->groupBy('no_tkt')
            ->pluck('id');

        // Get transactions with the latest ticket IDs
        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->with('businessTrip')
            ->where('approval_status', '!=', 'Draft') // Apply the same filter to transactions
            ->orderBy('created_at', 'desc')

            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereRaw("DATE(tgl_brkt_tkt) BETWEEN ? AND ?", [$startDate, $endDate]);
            });


        // Apply permission filters
        if (!empty($permissionLocations)) {
            $transactions->whereHas('employee', function ($query) use ($permissionLocations) {
                $query->whereIn('work_area_code', $permissionLocations);
            });
        }

        if (!empty($permissionCompanies)) {
            $transactions->whereIn('contribution_level_code', $permissionCompanies);
        }

        if (!empty($permissionGroupCompanies)) {
            $transactions->whereHas('employee', function ($query) use ($permissionGroupCompanies) {
                $query->whereIn('group_company', $permissionGroupCompanies);
            });
        }

        $transactions = $transactions->select('id', 'no_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();

        // Get all tickets
        $tickets = Tiket::with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group tickets by 'no_tkt'
        $ticket = $tickets->groupBy('no_tkt');

        // Get ticket IDs
        $tiketIds = $tickets->pluck('id');

        // Get ticket approvals
        $ticketApprovals = TiketApproval::whereIn('tkt_id', $tiketIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get();

        // Key ticket approvals by ticket ID
        $ticketApprovals = $ticketApprovals->keyBy('tkt_id');
        $ticketsGroups = $tickets->groupBy('no_tkt');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Fetch employee data and manager names for transactions
        foreach ($transactions as $transaction) {
            // Fetch the employee for the current transaction
            $employee = Employee::find($transaction->user_id);

            // If the employee exists, fetch their manager names
            if ($employee) {
                $managerL1Id = $employee->manager_l1_id;
                $managerL2Id = $employee->manager_l2_id;

                $managerL1 = Employee::where('employee_id', $managerL1Id)->first();
                $managerL2 = Employee::where('employee_id', $managerL2Id)->first();

                $managerL1Name = $managerL1 ? $managerL1->fullname : 'Unknown';
                $managerL2Name = $managerL2 ? $managerL2->fullname : 'Unknown';
            }
        }

        // Count tickets grouped by 'no_tkt'
        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        // Return the view with all the data
        return view('hcis.reimbursements.ticket.admin.ticketAdmin', [
            'link' => $link,
            'parentLink' => $parentLink,
            'transactions' => $transactions,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'ticketsGroups' => $ticketsGroups,
            'managerL1Name' => $managerL1Name ?? 'Unknown',
            'managerL2Name' => $managerL2Name ?? 'Unknown',
            'ticketApprovals' => $ticketApprovals,
            'employeeName' => $employeeName,
        ]);
    }

    public function ticketBookingAdmin(Request $req, $id)
    {
        $userId = Auth::id();
        $employeeId = auth()->user()->employee_id;

        // Ambil tiket berdasarkan ID yang diterima
        $model = Tiket::where('id', $id)->firstOrFail();
        $no_tkt = $model->no_tkt;
        $tickets = Tiket::where('no_tkt', $no_tkt)->with('businessTrip')->get();

        // dd($tickets);
        // Jika tombol booking ticket ditekan
        if ($req->has('action_tkt_book')) {
            // Validasi input dari form
            $req->validate([
                'booking_code' => 'required|string|max:255',
                'booking_price' => 'required|numeric|min:0',
            ]);

            foreach ($tickets as $ticket) {
                $ticket->booking_code = $req->input('booking_code');
                $ticket->tkt_price = $req->input('booking_price');
                $ticket->save();
            }

            return redirect()->route('ticket.admin')->with('success', 'Ticket booking updated successfully.');
        }
    }


    public function exportTicketAdminExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new TicketExport($startDate, $endDate), 'ticket_report.xlsx');
    }

    public function ticketDeleteAdmin($key)
    {
        $model = Tiket::findByRouteKey($key);
        Tiket::where('no_tkt', $model->no_tkt)->delete();

        // Redirect to the ticket page with a success message
        return redirect()->route('ticket.admin')->with('success', 'Tickets has been deleted');
    }


    private function getRomanMonth($month)
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
}
