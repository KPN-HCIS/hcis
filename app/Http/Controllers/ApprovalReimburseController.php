<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CATransaction;
use App\Models\Company;
use App\Models\htl_transaction;
use App\Models\Employee;
use App\Models\Location;
use App\Models\ListPerdiem;
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
        $employee_data = Employee::where('id', $userId)->first();
        // Mengambil transaksi dengan user_id yang sesuai dan mengikutsertakan relasi employee
        $ca_transactions = CATransaction::where('status_id', $employee_data->employee_id)->get();
        // ->leftJoin('ca_approvals', 'ca_transactions.id', '=', 'ca_approvals.id')
        // ->select('ca_transactions.*', 
        //     DB::raw('GROUP_CONCAT(ca_approvals.employee_id SEPARATOR ", ") as app_employee_ids'), 
        //     DB::raw('GROUP_CONCAT(ca_approvals.layer SEPARATOR ", ") as app_layers'), 
        //     DB::raw('GROUP_CONCAT(ca_approvals.approval_status SEPARATOR ", ") as app_approval_statuses')
        // )
        // ->where('ca_transactions.user_id', $userId)
        
        // ->get();
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
        $link = 'Cash Advanced';

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
    function cashadvancedActionApproval(Request $req, $key)
    {
        $userId = Auth::id();
        $model = CATransaction::findByRouteKey($key);
        $model->approval_status         = $req->approval_status;
        $model->created_by      = $userId;
        $model->save();

        Alert::success('Success Approve');
        return redirect()->intended(route('approval', absolute: false));
    }
}
