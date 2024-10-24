<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Dependents;
// use App\Models\HomeTrip;
// use App\Models\HomeTripPlan;
// use App\Models\HomeTripApproval;

class HomeTripController extends Controller
{
    public function homeTrip()
    {
        $employee_id = Auth::user()->employee_id;
        $family = Dependents::orderBy('date_of_birth', 'asc')->where('employee_id', $employee_id)->get();
        $parentLink = 'Reimbursement';
        $link = 'Home Trip';


        return view('hcis.reimbursements.homeTrip.homeTrip', compact( 'family', 'parentLink', 'link'));
    }
}
