<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLayer;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class LayerController extends Controller
{
    function layer() {
        $link = 'approval_layers';
        //$approvalLayers = ApprovalLayer::with('view_employee')->get();
        $approvalLayers = DB::table('approval_layers as al')
        ->select('al.employee_id', 'emp.fullname', 'emp.job_level', 'emp.contribution_level_code', 'emp.group_company')
        ->selectRaw("GROUP_CONCAT(al.layer ORDER BY al.layer DESC SEPARATOR '|') AS layers")
        ->selectRaw("GROUP_CONCAT(al.approver_id ORDER BY al.layer DESC SEPARATOR '|') AS approver_ids")
        ->selectRaw("GROUP_CONCAT(emp1.fullname ORDER BY al.layer DESC SEPARATOR '|') AS approver_names")
        ->selectRaw("GROUP_CONCAT(emp1.job_level ORDER BY al.layer DESC SEPARATOR '|') AS approver_job_levels")
        ->leftJoin('employees as emp', 'emp.employee_id', '=', 'al.employee_id')
        ->leftJoin('employees as emp1', 'emp1.employee_id', '=', 'al.approver_id')
        ->groupBy('al.employee_id', 'emp.fullname', 'emp.job_level', 'emp.contribution_level_code', 'emp.group_company')
        ->get();

        $employees = Employee::select('employee_id', 'fullname')
        ->where('job_level', '>', '4A')
        ->orderBy('fullname', 'asc')
        ->get();

    $employeeCount = $approvalLayers->unique('employee_id')->count();
        return view('pages.layers.layer', [
            'link' => $link,
            'approvalLayers' => $approvalLayers,
            'employeeCount' => $employeeCount,
            'employees' => $employees,
        ]);
    }

    function updatelayer(Request $req) {
        $employeeId = $req->input('employee_id');
        $nikApps = $req->input('nik_app');
    
        // Lakukan validasi jika diperlukan
    
        // Hapus data lama untuk employee_id tertentu
        ApprovalLayer::where('employee_id', $employeeId)->delete();
    
        // Simpan data yang baru
        foreach ($nikApps as $nikApp) {
            // Pisahkan `nik_app` menjadi `approver_id` dan `layer`
            list($approverId, $layer) = explode('_', $nikApp);
            
            // Buat entri baru untuk setiap `nik_app`
            ApprovalLayer::create([
                'employee_id' => $employeeId,
                'approver_id' => $approverId,
                'layer' => $layer
            ]);
        }
    }
}