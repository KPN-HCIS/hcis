<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\Company;
use App\Models\Employee;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class BusinessTripController extends Controller
{
    public function businessTrip()
    {
        $perPage = request()->query('per_page', 10);
        $sppd = BusinessTrip::orderBy('mulai', 'asc')->paginate($perPage);

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link'));
    }

    public function search(Request $request)
    {
        //search where
        $cari = $request->q;
        $sppd = BusinessTrip::where('nama', 'like', '%' . $cari . '%')
            ->orWhere('divisi', 'like', '%' . $cari . '%')
            ->orWhere('no_sppd', 'like', '%' . $cari . '%')
            ->orWhere('mulai', 'like', '%' . $cari . '%')
            ->orWhere('ca', 'like', '%' . $cari . '%')
            ->orWhere('tiket', 'like', '%' . $cari . '%')
            ->orWhere('hotel', 'like', '%' . $cari . '%')
            ->orWhere('taksi', 'like', '%' . $cari . '%')
            ->orWhere('status', 'like', '%' . $cari . '%')
            ->paginate(10);

        $sppd->appends($request->all());
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link'));
    }
    public function filterDate(Request $request)
    {
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        if ($startDate && $endDate) {
            $sppd = BusinessTrip::whereBetween('mulai', [$startDate, $endDate])
                ->orderBy('mulai', 'desc')
                ->paginate(10); // Adjust the pagination as needed
        } else {
            $sppd = BusinessTrip::orderBy('mulai', 'desc')->paginate(10);
        }

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link'));
    }

    public function updatestatus($id, Request $request)
    {
        $statusValue = $request->input('status');

        // Update your model or database table with the new status value
        BusinessTrip::where('id', $id)->update(['status' => $statusValue]);

        return redirect('/businessTrip');
    }

    public function export($id)
    {
        return Excel::download(new BusinessTripExport($id), 'Business-Trip-' . $id . '.xlsx');
    }
    public function businessTripformAdd()
    {
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        return view('hcis.reimbursements.businessTrip.formBusinessTrip',
        [
        'employee_data' => $employee_data,
        'companies' => $companies,
    ]);
    }

    public function businessTripCreate(Request $request)
    {
        $noSppd = $this->generateNoSppd();
        BusinessTrip::create([
            'nama' => $request->nama,
            'divisi' => $request->divisi,
            'unit_1' => $request->unit_1,
            'atasan_1' => $request->atasan_1,
            'email_1' => $request->email_1,
            'unit_2' => $request->unit_2,
            'atasan_2' => $request->atasan_2,
            'email_2' => $request->email_2,
            'no_sppd' => $noSppd,
            'mulai' => $request->mulai,
            'kembali' => $request->kembali,
            'tujuan' => $request->tujuan,
            'keperluan' => $request->keperluan,
            'bb_perusahaan' => $request->bb_perusahaan,
            'norek_krywn' => $request->norek_krywn,
            'nama_pemilik_rek' => $request->nama_pemilik_rek,
            'nama_bank' => $request->nama_bank,
            'ca' => $request->ca,
            'tiket' => $request->tiket,
            'hotel' => $request->hotel,
            'taksi' => $request->taksi,
            'status' => $request->status,

        ]);
        return redirect('/businessTrip');
    }
    private function generateNoSppd()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Assuming you want to generate no_sppd similarly to no_ca
        $lastTransaction = BusinessTrip::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_sppd', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/BT_ACC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_sppd, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/BT_ACC/$romanMonth/$currentYear";

        return $newNoSppd;
    }

    private function getRomanMonth($month)
    {
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romanMonths[$month];
    }

}
