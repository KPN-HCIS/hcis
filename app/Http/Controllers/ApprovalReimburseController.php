<?php

namespace App\Http\Controllers;

use App\Models\ca_approval;
use App\Models\ca_extend;
use Illuminate\Support\Facades\Auth;
use App\Models\CATransaction;
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
    public function approval()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced Approval';
        $employeeId = auth()->user()->employee_id;

        // Ambil ca_approval berdasarkan employee_id
        $ca_approval = ca_approval::with('employee')->where('employee_id', $employeeId)->where('approval_status', 'Pending')->get();

        $ca_approvals_with_transactions = $ca_approval->map(function ($approval) {
            $approval->transactions = CATransaction::where('id', $approval->ca_id)->get();
            return $approval;
        });
        $pendingCACount = ca_approval::where('employee_id', $employeeId)->where('approval_status', 'Pending')->count();
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

        foreach ($ca_approval as $ca_approvals) {
            foreach ($ca_approvals->transactions as $transaction) {
                $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
                $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
            }
        }

        return view('hcis.reimbursements.approval.approval', [
            'pendingCACount' => $pendingCACount,
            'pendingHTLCount' => $pendingHTLCount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_approval' => $ca_approvals_with_transactions,
            // 'company' => $company,
        ]);
    }
    public function cashadvancedApproval()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced Approval';
        $employeeId = auth()->user()->employee_id;

        $ca_transactions = CATransaction::with('employee')->where('status_id', $employeeId)->where('approval_status', 'Pending')->get();

        $fullnames = Employee::whereIn('employee_id', $ca_transactions->pluck('status_id'))
            ->pluck('fullname', 'employee_id');

        // Hitung Pending Request, Deklarasi, Extend dan Hotel
        $pendingCACount = CATransaction::where('status_id', $employeeId)->where('approval_status', 'Pending')->count();
        $pendingDECCount = CATransaction::where('sett_id', $employeeId)->where('approval_sett', 'Pending')->count();
        $pendingEXCount = CATransaction::where('extend_id', $employeeId)->where('approval_extend', 'Pending')->count();
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        return view('hcis.reimbursements.approval.approvalCashadv', [
            'pendingCACount' => $pendingCACount,
            'pendingDECCount' => $pendingDECCount,
            'pendingEXCount' => $pendingEXCount,
            'fullnames' => $fullnames,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'pendingHTLCount' => $pendingHTLCount,
        ]);
    }
    public function cashadvancedFormApproval($key)
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
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        // Cek jika tombol reject ditekan
        if ($req->input('action_ca_reject')) {
            $caApprovals = ca_approval::where('ca_id', $ca_id)->get();
            if ($caApprovals->isNotEmpty()) {
                foreach ($caApprovals as $caApproval) {
                    $caApproval->approval_status = 'Rejected';
                    $caApproval->approved_at = Carbon::now();
                    $caApproval->reject_reason = $req->reject_reason;
                    $caApproval->save();
                }
            }
            // ->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_status = 'Rejected';
                $caTransaction->save();
            }

            Alert::success('Success', 'All approvals rejected successfully.');
            return redirect()->route('approval.cashadvanced');
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
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_status = 'Approved'; // Set ke ID user layer tertinggi
                    // $caTransaction->approval_sett = 'On Progress';
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $model->approval_status = 'Approved';
                $model->approved_at = Carbon::now();
                $model->save();

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->status_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        Alert::success('Success', 'Approval updated successfully.');
        return redirect()->route('approval.cashadvanced');
    }

    public function cashadvancedDeklarasi()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
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
        $parentLink = 'Reimbursement';
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
            ->orderBy('layer', 'asc') // Mengurutkan berdasarkan layer
            ->get();

        if ($req->input('action_ca_reject')) {
            $caApprovalsSett = ca_sett_approval::where('ca_id', $ca_id)->get();
            if ($caApprovalsSett->isNotEmpty()) {
                foreach ($caApprovalsSett as $caApprovalSett) {
                    $caApprovalSett->approval_status = 'Rejected';
                    $caApprovalSett->approved_at = Carbon::now();
                    $caApprovalSett->reject_reason = $req->reject_reason;
                    $caApprovalSett->save();
                }
            }
            // ->update(['approval_status' => 'Rejected', 'approved_at' => Carbon::now()]);
            $caTransaction = ca_transaction::where('id', $ca_id)->first();
            if ($caTransaction) {
                $caTransaction->approval_sett = 'Rejected';
                $caTransaction->save();
            }

            Alert::success('Success', 'All approvals rejected successfully.');
            return redirect()->route('approval.cashadvanced');
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
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->approval_sett = 'Approved'; // Set ke ID user layer tertinggi
                    $caTransaction->save();
                }
            } else {
                // Jika ada layer yang lebih tinggi, update status layer saat ini dan alihkan ke layer berikutnya
                $model->approval_status = 'Approved';
                $model->approved_at = Carbon::now();
                $model->save();

                // Update status_id pada ca_transaction ke employee_id layer berikutnya
                $caTransaction = ca_transaction::where('id', $ca_id)->first();
                if ($caTransaction) {
                    $caTransaction->sett_id = $nextApproval->employee_id;
                    $caTransaction->save();
                }
            }
        }

        Alert::success('Success', 'Approval updated successfully.');
        return redirect()->route('approval.cashadvancedDeklarasi');
    }

    public function cashadvancedExtend()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
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

            Alert::success('Success', 'All approvals rejected successfully.');
            return redirect()->route('approval.cashadvancedExtend');
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

                return redirect()->route('approval.cashadvancedExtend');
            }
        }
    }
}
