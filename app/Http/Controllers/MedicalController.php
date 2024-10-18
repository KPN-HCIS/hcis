<?php

namespace App\Http\Controllers;

use App\Models\DataKeluarga;
use App\Models\HealthPlan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\ImportHealthCoverage;

class MedicalController extends Controller
{
    public function medical()
    {
        $keluarga = DataKeluarga::orderBy('umur', 'desc')->paginate(5);

        $parentLink = 'Reimbursement';
        $link = 'Medical';

        return view('hcis.reimbursements.medical.medical', compact(
            'keluarga',
            'parentLink',
            'link'
        ));
    }
    public function medicalForm()
    {
        $keluarga = DataKeluarga::orderBy('umur', 'desc')->paginate(5);

        $parentLink = 'Medical';
        $link = 'Add Medical Coverage Usage';

        return view('hcis.reimbursements.medical.form.medicalForm', compact(
            'keluarga',
            'parentLink',
            'link'
        ));
    }

    public function importExcel(Request $request)
    {
        $userId = Auth::id();
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Mengimpor data menggunakan Maatwebsite Excel
        Excel::import(
            new ImportHealthCoverage,
            $request->file('file')
        );

        return redirect()->route('medical')->with('success', 'Transaction successfully added From Excell.');
    }
}
