<?php

namespace App\Http\Controllers;

use App\Models\DataKeluarga;
use Illuminate\Http\Request;

class MedicalController extends Controller
{
    public function medical()
    {
        $keluarga = DataKeluarga::orderBy('umur', 'desc')->paginate(5);

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.medical.medical', compact('keluarga', 'parentLink', 'link'));
    }

}
