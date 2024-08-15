<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\Company;
use App\Models\Employee;
use App\Models\htl_transaction;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BusinessTripController extends Controller
{
    public function businessTrip()
    {
        $user = Auth::user();
        $perPage = request()->query('per_page', 10);

        $sppd = BusinessTrip::where('user_id', $user->id)->orderBy('mulai', 'asc')->paginate($perPage);

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // No sppd
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        // $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = htl_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        // $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'hotel'));
    }

    public function delete($id)
    {
        $n = BusinessTrip::find($id);
        if ($n) {
            $n->delete();
        }
        return redirect('businessTrip');
    }
    public function formUpdate($id)
    {
        $companies = Company::orderBy('contribution_level')->get();

        $n = BusinessTrip::find($id);
        return view('hcis.reimbursements.businessTrip.editFormBt', ['n' => $n, 'companies' => $companies]);
    }
    public function approval()
    {
        $user = Auth::user();
        $perPage = request()->query('per_page', 10);

        $sppd = BusinessTrip::where('user_id', $user->id)->orderBy('mulai', 'asc')->paginate($perPage);
        $ca = ca_transaction::where('user_id', $user->id)->first();

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'ca'));
    }

    public function deklarasi($id)
    {
        $companies = Company::orderBy('contribution_level')->get();

        $n = BusinessTrip::find($id);
        return view('hcis.reimbursements.businessTrip.deklarasi', ['n' => $n, 'companies' => $companies]);
    }

    public function update($id, Request $request)
    {
        $n = BusinessTrip::find($id);
        if ($n) {
            $oldNoSppd = $n->no_sppd;
            // $n->no_sppd = $oldNoSppd;
            $n->nama = $request->nama;
            $n->divisi = $request->divisi;
            $n->unit_1 = $request->unit_1;
            $n->atasan_1 = $request->atasan_1;
            $n->email_1 = $request->email_1;
            $n->unit_2 = $request->unit_2;
            $n->email_2 = $request->email_2;
            $n->no_sppd = $request->no_sppd;
            $n->mulai = $request->mulai;
            $n->kembali = $request->kembali;
            $n->tujuan = $request->tujuan;
            $n->keperluan = $request->keperluan;
            $n->bb_perusahaan = $request->bb_perusahaan;
            $n->norek_krywn = $request->norek_krywn;
            $n->nama_pemilik_rek = $request->nama_pemilik_rek;
            $n->nama_bank = $request->nama_bank;
            $n->ca = $request->ca;
            $n->tiket = $request->tiket;
            $n->hotel = $request->hotel;
            $n->taksi = $request->taksi;
            $n->no_sppd = $oldNoSppd;
            $n->save();
        }
        return redirect("/businessTrip");
    }
    public function search(Request $request)
    {
        $user = Auth::user();
        $cari = $request->q;
        $ca = ca_transaction::where('user_id', $user->id)->first();
        $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
            ->where(function ($query) use ($cari) {
                $query->where('nama', 'like', '%' . $cari . '%')
                    ->orWhere('divisi', 'like', '%' . $cari . '%')
                    ->orWhere('no_sppd', 'like', '%' . $cari . '%')
                    ->orWhere('mulai', 'like', '%' . $cari . '%')
                    ->orWhere('ca', 'like', '%' . $cari . '%')
                    ->orWhere('tiket', 'like', '%' . $cari . '%')
                    ->orWhere('hotel', 'like', '%' . $cari . '%')
                    ->orWhere('taksi', 'like', '%' . $cari . '%')
                    ->orWhere('status', 'like', '%' . $cari . '%');
            })
            ->paginate(10);

        $sppd->appends($request->all());
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        // return redirect('businessTrip');
        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'ca'));
    }
    public function filterDate(Request $request)
    {
        $user = Auth::user();
        $ca = ca_transaction::where('user_id', $user->id)->first();
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        if ($startDate && $endDate) {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->whereBetween('mulai', [$startDate, $endDate])
                ->orderBy('mulai', 'desc')
                ->paginate(10); // Adjust the pagination as needed
        } else {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->orderBy('mulai', 'desc')
                ->paginate(10);
        }
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'ca'));
    }

    public function updatestatus($id, Request $request)
    {
        $statusValue = $request->input('status');

        // Update your model or database table with the new status value
        BusinessTrip::where('id', $id)->update(['status' => $statusValue]);

        return redirect('/businessTrip');
    }

    // public function export($id)
    // {
    //     return Excel::download(new BusinessTripExport($id), 'Business-Trip-' . $id . '.xlsx');
    // }
    public function pdfDownload($id)
    {
        $data = BusinessTrip::find($id);
        return view('hcis.reimbursements.businessTrip.export', ['data' => $data]);
    }


    public function export($id)
    {
        $data = BusinessTrip::find($id);
        $ca = ca_transaction::where('user_id', $id)->first();

        // Check if data exists
        if (!$data) {
            return abort(404, 'Data not found');
        }

        // Generate the PDF
        $pdf = PDF::loadView('hcis.reimbursements.businessTrip.bt_pdf', ['data' => $data, 'ca' => $ca]);

        return $pdf->download('Business Trip' . $id . '.pdf');
    }

    public function businessTripformAdd()
    {
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        return view(
            'hcis.reimbursements.businessTrip.formBusinessTrip',
            [
                'employee_data' => $employee_data,
                'companies' => $companies,
            ]
        );
    }

    public function businessTripCreate(Request $request)
    {
        $noSppd = $this->generateNoSppd();
        $userId = Auth::id();
        BusinessTrip::create([
            'nama' => $request->nama,
            'user_id' => $userId,
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

        if ($lastTransaction && preg_match('/(\d{3})\/BT-ACC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_sppd, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/BT-ACC/$romanMonth/$currentYear";

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
