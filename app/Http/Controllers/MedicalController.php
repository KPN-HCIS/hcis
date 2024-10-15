<?php

namespace App\Http\Controllers;

use App\Models\DataKeluarga;
use App\Models\MasterDisease;
use Illuminate\Http\Request;

class MedicalController extends Controller
{
    public function medical()
    {
        $family = DataKeluarga::orderBy('umur', 'desc')->paginate(5);

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('family', 'parentLink', 'link'));
    }
    public function medicalForm()
    {
        $diseases = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();
        // $family = MasterDisease::orderBy('disease_name', 'asc')->where('active', 'T')->get();

        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact('diseases', 'parentLink', 'link'));
    }

}
