<?php

namespace App\Http\Controllers;

use App\Mail\TicketNotification;
use App\Models\BusinessTrip;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Tiket;
use App\Models\TiketApproval;
use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Models\Dependents;
use Mail;
use Illuminate\Support\Str;
// use App\Models\HomeTrip;
// use App\Models\HomeTripPlan;
// use App\Models\HomeTripApproval;

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

        return view('hcis.reimbursements.homeTrip.form.formHt', [
            'link' => $link,
            'parentLink' => $parentLink,
            'families' => $families,
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
}
