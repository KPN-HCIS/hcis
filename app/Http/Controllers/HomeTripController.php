<?php

namespace App\Http\Controllers;

use App\Mail\TicketNotification;
use App\Models\BusinessTrip;
use App\Models\Company;
use App\Models\Employee;
use App\Models\HomeTrip;
use App\Models\Location;
use App\Models\Tiket;
use App\Models\TiketApproval;
use Exception;
use Illuminate\Http\Request;
use App\Models\Dependents;
use Mail;
use Illuminate\Support\Str;
// use App\Models\HomeTrip;
// use App\Models\HomeTripPlan;
// use App\Models\HomeTripApproval;
use App\Models\MasterBusinessUnit;
use Illuminate\Support\Facades\Auth;

class HomeTripController extends Controller
{
    public function homeTrip(Request $request)
    {
        // Get the filtered tickets
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';

        $employee_id = Auth::user()->employee_id;
        $user_id = Auth::user()->id;
        $family = Dependents::orderBy('date_of_birth', 'asc')->where('employee_id', $employee_id)->get();
        // dd($transactions);
        $query = Tiket::where('user_id', $user_id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        // $filter = $request->input('filter', 'request');

        // // Apply filter to the query
        // if ($filter === 'request') {
        //     $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        // } elseif ($filter === 'rejected') {
        //     $statusFilter = ['Rejected'];
        // }

        // $ticketsFilter = $query->get();

        // // Apply status filter to the query
        // $query->whereIn('approval_status', $statusFilter);

        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->where('user_id', $user_id)
            ->groupBy('no_tkt')
            ->pluck('id');

        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->where('jns_dinas_tkt', '=', 'Cuti')
            // ->where('approval_status', $statusFilter)
            ->orderBy('created_at', 'desc')
            ->select('id', 'no_tkt', 'np_tkt', 'type_tkt', 'jenis_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();

        // Get all tickets for user
        $tickets = Tiket::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $tiketIds = $tickets->pluck('id');

        $ticketApprovals = TiketApproval::whereIn('tkt_id', $tiketIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected');
            })
            ->get();

        $ticketApprovals = $ticketApprovals->keyBy('tkt_id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        $employeeIds = $tickets->pluck('user_id')->unique();
        foreach ($transactions as $transaction) {
            // Fetch the employee for the current transaction
            $employee = Employee::find($transaction->user_id);

            // If the employee exists, fetch their manager names
            if ($employee) {
                $managerL1Id = $employee->manager_l1_id;
                $managerL2Id = $employee->manager_l2_id;

                $managerL1 = Employee::where('employee_id', $managerL1Id)->first();
                $managerL2 = Employee::where('employee_id', $managerL2Id)->first();

                $managerL1Names = $managerL1 ? $managerL1->fullname : 'Unknown';
                $managerL2Names = $managerL2 ? $managerL2->fullname : 'Unknown';
            }
        }
        $ticket = $tickets->groupBy('no_tkt');

        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });


        return view('hcis.reimbursements.homeTrip.homeTrip', compact(
            'parentLink',
            'link',
            'family',
            'transactions',
            'tickets',
            'ticket',
            'ticketApprovals',
            'employeeName',
            'ticketCounts',
            'managerL1Names',
            'managerL2Names',
            // 'ticketsGroups',
        ));
    }
    public function homeTripForm()
    {
        $employee_id = Auth::user()->employee_id;
        $userId = Auth::user()->id;
        $currentYear = now()->year;

        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';

        $employee_data = Employee::where('employee_id', $employee_id)->first();
        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $employees = Employee::orderBy('ktp')->get();

        $familyMembers = HomeTrip::where('employee_id', $employee_id)
            ->where('period', $currentYear)
            ->where('relation_type', '!=', 'employee')
            ->where('quota', '>', 0) // Only include family members with a quota > 0
            ->get();

        $employeeInHomeTrip = HomeTrip::where('employee_id', $employee_id)
            ->where('period', $currentYear)
            ->where('quota', '>', 0)
            ->where('relation_type', '=', 'employee')
            ->first();

        // dd($familyMembers);
        return view('hcis.reimbursements.homeTrip.form.formHt', [
            'link' => $link,
            'parentLink' => $parentLink,
            'families' => $families,
            'familyMembers' => $familyMembers,
            'employeeInHomeTrip' => $employeeInHomeTrip,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'employees' => $employees,
            'employee_name' => $employee_name,
        ]);
    }
    public function homeTripCreate(Request $request)
    {
        $userId = Auth::id();
        $employee_id = Auth::user()->employee_id;
        $employee = Employee::where('employee_id', $employee_id)->first();

        $employeeName = $employee->fullname;

        $npTkt = array_values($request->np_tkt);
        $selectedName = $npTkt[0] ?? null;
        // dd($request->np_tkt, $selectedName);

        // Check if the selected name is the employee
        if ($selectedName === $employeeName) {
            $gender = $employee->gender;
            $noTelp = $employee->personal_mobile_number;
        } else {
            // Handle dependents
            $dependent = Dependents::where('name', $selectedName)->first();
            if ($dependent) {
                $gender = $dependent->gender;
                $noTelp = $dependent->phone;
            } else {
                // Handle the case where the name doesn't match employee or dependents
                $gender = null;
                $noTelp = null;
            }
        }

        // dd($families);
        if ($request->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($request->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }

        // Prepare the ticket data arrays
        $ticketData = [
            'noktp_tkt' => $request->noktp_tkt,
            'tlp_tkt' => $request->tlp_tkt,
            'jk_tkt' => $request->jk_tkt,
            'np_tkt' => $request->np_tkt,
            'dari_tkt' => $request->dari_tkt,
            'ke_tkt' => $request->ke_tkt,
            'tgl_brkt_tkt' => $request->tgl_brkt_tkt,
            'tgl_plg_tkt' => $request->tgl_plg_tkt,
            'jam_brkt_tkt' => $request->jam_brkt_tkt,
            'jam_plg_tkt' => $request->jam_plg_tkt,
            'jenis_tkt' => $request->jenis_tkt,
            'type_tkt' => $request->type_tkt,
            'ket_tkt' => $request->ket_tkt,
            'approval_status' => $statusValue,
            'tkt_only' => 'Y',
        ];
        // dd($ticketData);

        $noKtp = [];
        $npTkt = [];
        $dariTkt = [];
        $keTkt = [];
        $tglBrktTkt = [];
        $jamBrktTkt = [];
        $noTktList = [];
        $tglPlgTkt = [];
        $jamPlgTkt = [];
        $tipeTkt = [];

        $generatedNoTkt = $this->generateTicketNumber();

        foreach ($ticketData['np_tkt'] as $key => $selectedName) {
            // Only process if the required fields are filled
            if (!empty($selectedName)) {
                $tiket = new Tiket();
                $tiket->id = (string) Str::uuid();
                // Use the pre-generated ticket number
                $tiket->no_tkt = $generatedNoTkt;

                $userId = Auth::id();
                $tiket->no_sppd = $request->bisnis_numb;
                $tiket->user_id = $userId;
                $tiket->unit = $request->unit;
                $tiket->jk_tkt = $gender;
                $tiket->np_tkt = $ticketData['np_tkt'][$key];
                $tiket->noktp_tkt = $ticketData['noktp_tkt'][$key];
                $tiket->tlp_tkt = $noTelp;
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
                $tiket->jns_dinas_tkt = 'Cuti';
                $tiket->tkt_only = 'Y';
                // dd($request->all());
                $tiket->save();

                $noKtp[] = $ticketData['noktp_tkt'][$key];
                $npTkt[] = $ticketData['np_tkt'][$key];
                $dariTkt[] = $ticketData['dari_tkt'][$key];
                $keTkt[] = $ticketData['ke_tkt'][$key];
                $tipeTkt[] = $ticketData['type_tkt'][$key];
                $tglBrktTkt[] = $ticketData['tgl_brkt_tkt'][$key];
                $jamBrktTkt[] = $ticketData['jam_brkt_tkt'][$key];
                $noTktList[] = $tiket->no_tkt;
                $tglPlgTkt[] = $ticketData['tgl_plg_tkt'][$key];
                $jamPlgTkt[] = $ticketData['jam_plg_tkt'][$key];
            }
        }

        // if ($statusValue !== 'Draft') {
        //     $managerId = Employee::where('id', $userId)->pluck('manager_l1_id')->first();
        //     $managerEmail = Employee::where('employee_id', $managerId)->pluck('email')->first();
        //     $managerName = Employee::where('employee_id', $managerId)->pluck('fullname')->first();
        //     $approvalLink = route('approve.ticket', [
        //         'id' => urlencode($tiket->id),
        //         'manager_id' => $managerId,
        //         'status' => 'Pending L2'
        //     ]);

        //     $rejectionLink = route('reject.ticket.link', [
        //         'id' => urlencode($tiket->id),
        //         'manager_id' => $managerId,
        //         'status' => 'Rejected'
        //     ]);
        //     // // dd($managerEmail);
        //     if ($managerEmail) {
        //         // Send email to the manager
        //         Mail::to($managerEmail)->send(new TicketNotification([
        //             'noSppd' => $request->bisnis_numb,
        //             'noTkt' => $noTktList,
        //             'namaPenumpang' => $npTkt,
        //             'dariTkt' => $dariTkt,
        //             'keTkt' => $keTkt,
        //             'tglBrktTkt' => $tglBrktTkt,
        //             'jamBrktTkt' => $jamBrktTkt,
        //             'approvalStatus' => $statusValue,
        //             'tipeTkt' => $tipeTkt,
        //             'tglPlgTkt' => $tglPlgTkt,
        //             'jamPlgTkt' => $jamPlgTkt,
        //             'managerName' => $managerName,
        //             'approvalLink' => $approvalLink,
        //             'rejectionLink' => $rejectionLink,
        //         ]));
        //     }
        // }
        return redirect()->route('home-trip')->with('success', 'The ticket request has been input successfully.');
    }

    public function homeTripDelete($id)
    {
        $ticket = Tiket::findByRouteKey($id);
        // dd($ticket);
        Tiket::where('no_tkt', $ticket->no_tkt)->delete();

        // Redirect to the ticket page with a success message
        return redirect()->route('home-trip')->with('success', 'Home Trip has been deleted');
    }

    private function generateTicketNumber()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Get the last transaction for the current year, including deleted ones
        $lastTransaction = Tiket::whereYear('created_at', $currentYear)
            ->where('no_tkt', 'like', '%TKTC-HRD%')  // Keep the filter for 'TKTD-HRD'
            ->orderBy('no_tkt', 'desc')
            ->withTrashed()
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/TKTC-HRD\/([IVX]+)\/\d{4}/', $lastTransaction->no_tkt, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/TKTC-HRD/$romanMonth/$currentYear";

        // dd($newNoSppd);

        return $newNoSppd;
    }

    public function getRomanMonth($month)
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

    public function homeTripAdmin(Request $request)
    {
        // Get the filtered tickets
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';

        $employee_id = Auth::user()->employee_id;
        $user_id = Auth::user()->id;
        $family = Dependents::orderBy('date_of_birth', 'asc')->where('employee_id', $employee_id)->get();
        // dd($transactions);
        $query = Tiket::where('user_id', $user_id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        // $filter = $request->input('filter', 'request');

        // // Apply filter to the query
        // if ($filter === 'request') {
        //     $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        // } elseif ($filter === 'rejected') {
        //     $statusFilter = ['Rejected'];
        // }

        // $ticketsFilter = $query->get();

        // // Apply status filter to the query
        // $query->whereIn('approval_status', $statusFilter);

        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->where('user_id', $user_id)
            ->groupBy('no_tkt')
            ->pluck('id');

        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->where('jns_dinas_tkt', '=', 'Cuti')
            // ->where('approval_status', $statusFilter)
            ->orderBy('created_at', 'desc')
            ->select('id', 'no_tkt', 'np_tkt', 'type_tkt', 'jenis_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();

        // Get all tickets for user
        $tickets = Tiket::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $tiketIds = $tickets->pluck('id');

        $ticketApprovals = TiketApproval::whereIn('tkt_id', $tiketIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected');
            })
            ->get();

        $ticketApprovals = $ticketApprovals->keyBy('tkt_id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        $employeeIds = $tickets->pluck('user_id')->unique();
        foreach ($transactions as $transaction) {
            // Fetch the employee for the current transaction
            $employee = Employee::find($transaction->user_id);

            // If the employee exists, fetch their manager names
            if ($employee) {
                $managerL1Id = $employee->manager_l1_id;
                $managerL2Id = $employee->manager_l2_id;

                $managerL1 = Employee::where('employee_id', $managerL1Id)->first();
                $managerL2 = Employee::where('employee_id', $managerL2Id)->first();

                $managerL1Names = $managerL1 ? $managerL1->fullname : 'Unknown';
                $managerL2Names = $managerL2 ? $managerL2->fullname : 'Unknown';
            }
        }
        $ticket = $tickets->groupBy('no_tkt');

        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });


        return view('hcis.reimbursements.homeTrip.homeTrip', compact(
            'parentLink',
            'link',
            'family',
            'transactions',
            'tickets',
            'ticket',
            'ticketApprovals',
            'employeeName',
            'ticketCounts',
            'managerL1Names',
            'managerL2Names',
            // 'ticketsGroups',
        ));
    }
    public function homeTripForm()
    {
        $employee_id = Auth::user()->employee_id;
        $userId = Auth::user()->id;
        $currentYear = now()->year;

        $families = Dependents::orderBy('date_of_birth', 'desc')->where('employee_id', $employee_id)->get();
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';

        $employee_data = Employee::where('employee_id', $employee_id)->first();
        $employee_name = Employee::select('fullname')
            ->where('employee_id', $employee_id)
            ->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $employees = Employee::orderBy('ktp')->get();

        $familyMembers = HomeTrip::where('employee_id', $employee_id)
            ->where('period', $currentYear)
            ->where('relation_type', '!=', 'employee')
            ->where('quota', '>', 0) // Only include family members with a quota > 0
            ->get();

        $employeeInHomeTrip = HomeTrip::where('employee_id', $employee_id)
            ->where('period', $currentYear)
            ->where('quota', '>', 0)
            ->where('relation_type', '=', 'employee')
            ->first();

        // dd($familyMembers);
        return view('hcis.reimbursements.homeTrip.form.formHt', [
            'link' => $link,
            'parentLink' => $parentLink,
            'families' => $families,
            'familyMembers' => $familyMembers,
            'employeeInHomeTrip' => $employeeInHomeTrip,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'employees' => $employees,
            'employee_name' => $employee_name,
        ]);
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
