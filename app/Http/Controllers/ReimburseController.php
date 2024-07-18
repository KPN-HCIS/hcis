<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class ReimburseController extends Controller
{
    function reimbursements() {

        $userId = Auth::id();

        return view('hcis.reimbursements.dash', [
            'userId' => $userId,
        ]);
    }
    function cashadvanced() {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        return view('hcis.reimbursements.cashadv.cashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
        ]);
    }
    function cashadvancedCreate() {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        return view('hcis.reimbursements.cashadv.formCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
        ]);
    }
}
