<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusinessTripController extends Controller
{
    public function businessTrip()
    {
        return view('hcis.reimbursements.businessTrip.businessTrip');
        // $keluarga = BussinessTripController::orderBy('umur', 'desc')->paginate(5);
        // return view('pages.medical.medical', ['keluarga' => $keluarga]);
    }
}
