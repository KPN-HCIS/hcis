<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    function index() {
        $link = 'reports';
        return view('reports.app', [
            'link' => $link
        ]);
    }
    public function getReportContent($reportType)
    {
        $user = Auth::user()->employee_id;

        $data = ApprovalRequest::with(['employee', 'manager', 'goal', 'initiated'])->get();

        // dd($data);
        // Logic to fetch and return report content based on $reportType
        if ($reportType === 'Goal') {
            return view('reports.goal', compact('data'));
        } else {
            return ''; // Handle other report types accordingly
        }
    }
}
