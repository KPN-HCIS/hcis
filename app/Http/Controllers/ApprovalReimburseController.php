<?php

namespace App\Http\Controllers;

use App\Models\ca_approval;
use App\Models\ca_extend;
use Illuminate\Support\Facades\Auth;
use App\Models\CATransaction;
use App\Models\BusinessTrip;
use App\Models\Tiket;
use App\Models\Hotel;
use App\Models\HealthCoverage;
use App\Models\Company;
use App\Models\Designation;
use App\Models\htl_transaction;
use App\Models\Employee;
use App\Models\Location;
use App\Models\ca_transaction;
use App\Models\ListPerdiem;
use App\Models\ca_sett_approval;
use App\Models\MatrixApproval;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ApprovalReimburseController extends Controller
{
    public function reimbursementsApproval()
    {
        $userId = Auth::id();

        return view('hcis.reimbursements.approval.approval', [
            'userId' => $userId,
        ]);
    }
    public function cashadvancedApproval()
    {
        $userId = Auth::id();
        $user = Auth::user();
        $parentLink = 'Approval';
        $link = 'Cash Advanced Approval';
        $employeeId = auth()->user()->employee_id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record

        $ca_transactions = CATransaction::with('employee')->where('status_id', $employeeId)->where('approval_status', 'Pending')->get();
        $ca_transactions_dec = CATransaction::with('employee')->where('sett_id', $employeeId)->where('approval_sett', 'Pending')->get();
        $ca_transactions_ext = CATransaction::with('employee')->where('extend_id', $employeeId)->where('approval_extend', 'Pending')->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions_ext->pluck('status_id'))
            ->pluck('fullname', 'employee_id');

        $extendData = ca_extend::whereIn('ca_id', $ca_transactions_ext->pluck('id'))
            ->get(['ca_id', 'ext_end_date', 'ext_total_days', 'reason_extend']);

        $extendTime = $extendData->keyBy('ca_id')->map(function ($item) {
            return [
                'ext_end_date' => $item->ext_end_date,
                'ext_total_days' => $item->ext_total_days,
                'reason_extend' => $item->reason_extend,
            ];
        });

        // Hitung Pending Request, Deklarasi, Extend dan Hotel
        $pendingCACount = CATransaction::where('status_id', $employeeId)->where('approval_status', 'Pending')->count();
        $pendingDECCount = CATransaction::where('sett_id', $employeeId)->where('approval_sett', 'Pending')->count();
        $pendingEXCount = CATransaction::where('extend_id', $employeeId)->where('approval_extend', 'Pending')->count();
        $totalPendingCount = $pendingCACount + $pendingDECCount + $pendingEXCount;
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

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

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        return view('hcis.reimbursements.approval.approvalCashadv', [
            'pendingCACount' => $pendingCACount,
            'pendingDECCount' => $pendingDECCount,
            'pendingEXCount' => $pendingEXCount,
            'totalPendingCount' => $totalPendingCount,
            'totalBTCount' => $totalBTCount,
            'totalTKTCount' => $totalTKTCount,
            'totalHTLCount' => $totalHTLCount,
            'totalMDCCount' => $totalMDCCount,
            'fullnames' => $fullnames,
            'extendTime' => $extendTime,
            'extendData' => $extendData,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'ca_transactions_dec' => $ca_transactions_dec,
            'ca_transactions_ext' => $ca_transactions_ext,
            'pendingHTLCount' => $pendingHTLCount,
        ]);
    }
    public function cashadvancedFormApproval($key)
    {
        $userId = Auth::id();
        $parentLink = 'Approval';
        $link = 'Cash Advanced Approval';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::findByRouteKey($key);

        return view('hcis.reimbursements.approval.listApproveCashadv', [
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
    function cashadvancedActionApproval(Request $req, $ca_id)
    {
        $userId = Auth::id();
        $employeeId = auth()->user()->employee_id;
        $model = ca_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->firstOrFail();

        // Cek apakah ini sudah di-approve atau tidak
        if ($model->approval_status == 'Approved') {
            Alert::warning('Warning', 'This approval has already been approved.');
            return redirect()->route('approval.cashadvanced');
        }

        // Ambil semua approval yang terkait dengan ca_id
        $approvals = ca_approval::where('ca_id', $ca_id)
            ->where('approval_status', 'Pending')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        // Cek jika tombol reject ditekan
        if ($req->input('action_ca_reject')) {
            $caApprovals = ca_approval::where('ca_id', $ca_id)->get();
            if ($caApprovals->isNotEmpty()) {
                foreach ($caApprovals as $caApproval) {
                    $caApproval->approval_status = 'Rejected';
                    $caApproval->approved_at = Carbon::now();
                    $caApproval->reject_info = $req->reject_info;
                    $caApproval->save();
                }
            }
            // ->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_status = 'Rejected';
                $caTransaction->save();
            }

            return redirect()->route('approval.cashadvanced')->with('success', 'Transaction Rejected, Rejection will be send to the employee.');
        }

        // Cek jika tombol approve ditekan
        if ($req->input('action_ca_approve')) {
            $nextApproval = null;


            // Mencari layer berikutnya yang lebih tinggi
            foreach ($approvals as $approval) {
                if ($approval->layer > $model->layer && $approval->employee_id <> $model->employee_id) {
                    $nextApproval = $approval;
                    break;
                }
            }

            // Jika tidak ada layer yang lebih tinggi (berarti ini adalah layer tertinggi)
            if (!$nextApproval) {
                // Set status ke Approved untuk layer tertinggi
                $models = ca_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update status_id pada ca_transaction
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_status = 'Approved'; // Set ke ID user layer tertinggi
                    // $caTransaction->approval_sett = 'On Progress';
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $models = ca_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now();
                    $model->save();
                }

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->status_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        return redirect()->route('approval.cashadvanced')->with('success', 'Transaction Approved, Thanks for Approving.');
    }

    public function cashadvancedActionApprovalAdmin(Request $req, $ca_id)
    {
        // Ambil dataNoId dari request
        $dataNoId = $req->input('data_no_id');
        $model = ca_approval::where('ca_id', $ca_id)
            ->where('id', $dataNoId)
            ->first();

        if (!$model) {
            return redirect()->route('cashadvanced.admin')->with('error', 'Approval not found for this transaction.');
        }

        // Ambil semua approval yang terkait dengan ca_id
        $approvals = ca_approval::where('ca_id', $ca_id)
            ->where('approval_status', 'Pending')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        if ($req->input('action_ca_reject')) {
            $caApprovals = ca_approval::where('ca_id', $ca_id)->get();
            if ($caApprovals->isNotEmpty()) {
                foreach ($caApprovals as $caApproval) {
                    $caApproval->approval_status = 'Rejected';
                    $caApproval->approved_at = Carbon::now();
                    $caApproval->reject_info = $req->reject_info;
                    $caApproval->save();
                }
            }
            // ->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_status = 'Rejected';
                $caTransaction->save();
            }

            return redirect()->route('cashadvanced.admin')->with('success', 'Transaction Rejected, Rejection will be send to the employee.')
                ->with('refresh', true);
        }

        if ($req->input('action_ca_approve')) {
            $nextApproval = null;

            // Mencari layer berikutnya yang lebih tinggi
            foreach ($approvals as $approval) {
                if ($approval->layer > $model->layer && $approval->employee_id <> $model->employee_id) {
                    $nextApproval = $approval;
                    break;
                }
            }

            // Jika tidak ada layer yang lebih tinggi (berarti ini adalah layer tertinggi)
            if (!$nextApproval) {
                // Set status ke Approved untuk layer tertinggi
                $models = ca_approval::where('ca_id', $ca_id)->where('employee_id', $model->employee_id)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update approval_status pada ca_transaction
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_status = 'Approved'; // Set ke Approved untuk transaksi
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $models = ca_approval::where('ca_id', $ca_id)->where('employee_id', $model->employee_id)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->status_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        return redirect()->route('cashadvanced.admin')->with('success', 'Transaction approved successfully.')
            ->with('refresh', true);
    }

    public function cashadvancedDeklarasi()
    {
        $userId = Auth::id();
        $parentLink = 'Approval';
        $link = 'Cash Advanced';
        $employeeId = auth()->user()->employee_id;

        $ca_transactions = CATransaction::with('employee')->where('sett_id', $employeeId)->where('approval_sett', 'Pending')->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('sett_id'))
            ->pluck('fullname', 'employee_id');

        $pendingCACount = CATransaction::where('status_id', $employeeId)->where('approval_status', 'Pending')->count();
        $pendingDECCount = CATransaction::where('sett_id', $employeeId)->where('approval_sett', 'Pending')->count();
        $pendingEXCount = CATransaction::where('extend_id', $employeeId)->where('approval_extend', 'Pending')->count();
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        return view('hcis.reimbursements.approval.approvalDeklarasiCashadv', [
            'pendingCACount' => $pendingCACount,
            'pendingDECCount' => $pendingDECCount,
            'pendingEXCount' => $pendingEXCount,
            'link' => $link,
            'fullnames' => $fullnames,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'pendingHTLCount' => $pendingHTLCount,
        ]);
    }
    public function cashadvancedFormDeklarasi($key)
    {
        $userId = Auth::id();
        $parentLink = 'Approval';
        $link = 'Cash Advanced Approval';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::findByRouteKey($key);

        return view('hcis.reimbursements.approval.listApproveDeklarasiCashadv', [
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
    function cashadvancedActionDeklarasi(Request $req, $ca_id)
    {
        $userId = Auth::id();
        $employeeId = auth()->user()->employee_id;
        $model = ca_sett_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->firstOrFail();

        // Cek apakah ini sudah di-approve atau tidak
        if ($model->approval_status == 'Approved') {
            Alert::warning('Warning', 'This approval has already been approved.');
            return redirect()->route('approval.cashadvancedDeklarasi');
        }

        // Ambil semua approval yang terkait dengan ca_id
        $approvals = ca_sett_approval::where('ca_id', $ca_id)
            ->where('approval_status', 'Pending')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        if ($req->input('action_ca_reject')) {
            $caApprovalsSett = ca_sett_approval::where('ca_id', $ca_id)->get();
            if ($caApprovalsSett->isNotEmpty()) {
                foreach ($caApprovalsSett as $caApprovalSett) {
                    $caApprovalSett->approval_status = 'Rejected';
                    $caApprovalSett->approved_at = Carbon::now();
                    $caApprovalSett->reject_info = $req->reject_info;
                    $caApprovalSett->save();
                }
            }
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_sett = 'Rejected';
                $caTransaction->save();
            }

            return redirect()->route('approval.cashadvancedDeklarasi')->with('success', 'Transaction Rejected, Rejection will be send to the employee.');
        }

        // Cek jika tombol approve ditekan
        if ($req->input('action_ca_approve')) {
            $nextApproval = null;

            // Mencari layer berikutnya yang lebih tinggi
            foreach ($approvals as $approval) {
                if ($approval->layer > $model->layer && $approval->employee_id <> $model->employee_id) {
                    $nextApproval = $approval;
                    break;
                }
            }

            // Jika tidak ada layer yang lebih tinggi (berarti ini adalah layer tertinggi)
            if (!$nextApproval) {
                // Set status ke Approved untuk layer tertinggi
                $models = ca_sett_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update status_id pada ca_transaction
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_sett = 'Approved'; // Set ke ID user layer tertinggi
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $models = ca_sett_approval::where('ca_id', $ca_id)->where('employee_id', $employeeId)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->sett_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        return redirect()->route('approval.cashadvancedDeklarasi')->with('success', 'Transaction Approved, Thanks for Approving.');
    }

    public function cashadvancedActionDeklarasiAdmin(Request $req, $ca_id)
    {
        // Ambil dataNoId dari request
        $dataNoId = $req->input('data_no_id');
        $model = ca_sett_approval::where('ca_id', $ca_id)
            ->where('id', $dataNoId)
            ->first();

        if (!$model) {
            return redirect()->route('cashadvanced.admin')->with('error', 'Approval not found for this transaction.');
        }

        // Ambil semua approval yang terkait dengan ca_id
        $approvals = ca_sett_approval::where('ca_id', $ca_id)
            ->where('approval_status', 'Pending')
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        if ($req->input('action_ca_reject')) {
            $caApprovalsSett = ca_sett_approval::where('ca_id', $ca_id)->get();
            if ($caApprovalsSett->isNotEmpty()) {
                foreach ($caApprovalsSett as $caApprovalSett) {
                    $caApprovalSett->approval_status = 'Rejected';
                    $caApprovalSett->approved_at = Carbon::now();
                    $caApprovalSett->reject_info = $req->reject_info;
                    $caApprovalSett->save();
                }
            }
            // ->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_sett = 'Rejected';
                $caTransaction->save();
            }

            return redirect()->route('cashadvanced.admin')->with('success', 'Transaction Rejected, Rejection will be send to the employee.')
                ->with('refresh', true);
        }

        if ($req->input('action_ca_approve')) {
            $nextApproval = null;

            // Mencari layer berikutnya yang lebih tinggi
            foreach ($approvals as $approval) {
                if ($approval->layer > $model->layer && $approval->employee_id <> $model->employee_id) {
                    $nextApproval = $approval;
                    break;
                }
            }

            // Jika tidak ada layer yang lebih tinggi (berarti ini adalah layer tertinggi)
            if (!$nextApproval) {
                // Set status ke Approved untuk layer tertinggi
                $models = ca_sett_approval::where('ca_id', $ca_id)->where('employee_id', $model->employee_id)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update approval_sett pada ca_transaction
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_sett = 'Approved'; // Set ke Approved untuk transaksi
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $models = ca_sett_approval::where('ca_id', $ca_id)->where('employee_id', $model->employee_id)->get();
                foreach ($models as $model) {
                    $model->approval_status = 'Approved';
                    $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                    $model->save();
                }

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->status_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        return redirect()->route('cashadvanced.admin')->with('success', 'Transaction approved successfully.')
            ->with('refresh', true);
    }

    public function cashadvancedExtend()
    {
        $userId = Auth::id();
        $parentLink = 'Approval';
        $link = 'Cash Advanced';
        $employeeId = auth()->user()->employee_id;

        $ca_transactions = CATransaction::with('employee')->where('extend_id', $employeeId)->where('approval_extend', 'Pending')->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('extend_id'))
            ->pluck('fullname', 'employee_id');

        $extendData = ca_extend::whereIn('ca_id', $ca_transactions->pluck('id'))
            ->get(['ca_id', 'ext_end_date', 'ext_total_days', 'reason_extend']);

        // Indeks koleksi berdasarkan ca_id
        $extendTime = $extendData->keyBy('ca_id')->map(function ($item) {
            return [
                'ext_end_date' => $item->ext_end_date,
                'ext_total_days' => $item->ext_total_days,
                'reason_extend' => $item->reason_extend,
            ];
        });

        $pendingCACount = CATransaction::where('status_id', $employeeId)->where('approval_status', 'Pending')->count();
        $pendingDECCount = CATransaction::where('sett_id', $employeeId)->where('approval_sett', 'Pending')->count();
        $pendingEXCount = CATransaction::where('extend_id', $employeeId)->where('approval_extend', 'Pending')->count();
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

        return view('hcis.reimbursements.approval.approvalExtendCashadv', [
            'pendingCACount' => $pendingCACount,
            'pendingDECCount' => $pendingDECCount,
            'pendingEXCount' => $pendingEXCount,
            'link' => $link,
            'fullnames' => $fullnames,
            'extendTime' => $extendTime,
            'extendData' => $extendData,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'pendingHTLCount' => $pendingHTLCount,
        ]);
    }
    public function cashadvancedActionExtended(Request $req)
    {
        $id = $req->input('no_id'); // Get the ID from the no_id input
        $userId = Auth::id();
        $employeeId = auth()->user()->employee_id;
        $id = $req->input('no_id');
        $employee_data = Employee::where('id', $userId)->first();

        $model = ca_extend::where('ca_id', $id)->where('employee_id', $employeeId)->firstOrFail();

        // Ambil semua approval yang terkait dengan ca_id
        $approvals = ca_extend::where('ca_id', $id)
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        // Cek jika tombol reject ditekan
        if ($req->input('action_ca_reject')) {
            ca_extend::where('ca_id', $id)->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $id)->first();
            if ($caTransaction) {
                $caTransaction->approval_extend = 'Rejected';
                $caTransaction->save();
            }

            return redirect()->route('approval.cashadvancedExtend')->with('success', 'Transaction Rejected, Rejection will be send to the employee.');
        }

        // Cek jika tombol approve ditekan
        if ($req->input('action_ca_approve')) {
            $nextApproval = null;

            // Mencari layer berikutnya yang lebih tinggi
            foreach ($approvals as $approval) {
                if ($approval->layer > $model->layer) {
                    $nextApproval = $approval;
                    break;
                }
            }

            // Jika tidak ada layer yang lebih tinggi (berarti ini adalah layer tertinggi)
            if (!$nextApproval) {
                // Set status ke Approved untuk layer tertinggi
                $model->approval_status = 'Approved';
                $model->approved_at = Carbon::now(); // Simpan waktu approval sekarang
                $model->save();

                // Update status_id pada ca_transaction
                $caTransaction = ca_transaction::where('id', $id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_extend = 'Approved'; // Set ke ID user layer tertinggi
                    $caTransaction->start_date = $req->input('ext_start_date');
                    $caTransaction->end_date = $req->input('ext_end_date');
                    $caTransaction->total_days = $req->input('ext_totaldays');
                    $caTransaction->reason_extend = $req->input('ext_reason');
                    $caTransaction->save();
                }

                return redirect()->route('approval.cashadvancedExtend');
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $model->approval_status = 'Approved';
                $model->approved_at = Carbon::now();
                $model->save();

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $id)->first();
                if ($caTransaction) {
                    $caTransaction->extend_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }

                return redirect()->route('approval.cashadvancedExtend')->with('success', 'Extend Approved, Thanks for Approving.');
            }
        }
    }
}
