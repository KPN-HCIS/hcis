<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalLayer;
use App\Models\User;

class LayerController extends Controller
{
    function layer() {
        $link = 'approval_layers';
        //$approvalLayers = ApprovalLayer::with('view_employee')->get();
        $approvalLayers = ApprovalLayer::select('employee_id')->distinct()->with('view_employee')->get();
        $employeeCount = $approvalLayers->count();
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
