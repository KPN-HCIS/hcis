<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LayerController extends Controller
{
    function layer() {
        $link = 'approval_layers';
        //$approvalLayers = ApprovalLayer::with('view_employee')->get();
        $approvalLayers = DB::table('approval_layers')
        ->select('approval_layers.employee_id', 'emp.contribution_level_code', 'emp.group_company', 'emp.fullname', 'approval_layers.approver_id', 'emp2.fullname as directname', 'approval_layers.layer')
        ->leftJoin('employees as emp', 'emp.employee_id', '=', 'approval_layers.employee_id')
        ->leftJoin('employees as emp2', 'emp2.employee_id', '=', 'approval_layers.approver_id')
        ->get();

    $employeeCount = $approvalLayers->unique('employee_id')->count();
        return view('pages.layers.layer', [
            'link' => $link,
            'approvalLayers' => $approvalLayers,
            'employeeCount' => $employeeCount,
        ]);
    }
    // public function showData()
    // {
    //     $approvalLayers = ApprovalLayer::with('view_employee')->get();
    //     return view('data', compact('approvalLayers'));
    // }
}
