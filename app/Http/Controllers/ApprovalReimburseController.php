<?php

namespace App\Http\Controllers;

use App\Models\ca_approval;
use Illuminate\Support\Facades\Auth;
use App\Models\CATransaction;
use App\Models\Company;
use App\Models\htl_transaction;
use App\Models\Employee;
use App\Models\Location;
use App\Models\ListPerdiem;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

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

        return view('hcis.reimbursements.approval.approvalCashadv', [
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
        // Mengambil transaksi dengan user_id yang sesuai dan mengikutsertakan relasi employee
        $ca_transactions = CATransaction::with('employee')->where('user_id', $userId)->get();
        $company = Company::with('companies')->where('contribution_level_code', 'company_name')->get();
        $pendingCACount = CATransaction::where('approval_status', 'Pending')->count();
        $pendingHTLCount = htl_transaction::where('approval_status', 'Pending')->count();

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }
        //dd($ca_transactions);
        return view('hcis.reimbursements.approval.approval', [
            'pendingCACount' => $pendingCACount,
            'pendingHTLCount' => $pendingHTLCount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
            'company' => $company,
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
        $model = ca_approval::where('ca_id', $ca_id)->firstOrFail();

        // Update data sesuai dengan input
        $model->approval_status = $req->approval_status;
        $model->approved_at = Carbon::now();
        $model->save();

        Alert::success('Success', 'Approval updated successfully.');
        return redirect()->route('approval');
    }
}
