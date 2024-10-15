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
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact('keluarga', 'parentLink', 'link'));
    }
    public function medicalForm()
    {
        $keluarga = DataKeluarga::orderBy('umur', 'desc')->paginate(5);

        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact('keluarga', 'parentLink', 'link'));
    }

}
