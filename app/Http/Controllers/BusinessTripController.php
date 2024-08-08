<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Hotel;
use App\Models\Taksi;
use App\Models\Tiket;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use ZipArchive;
use Illuminate\Support\Facades\Log;


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
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
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


    public function update($id, Request $request)
    {
        $n = BusinessTrip::find($id);
        if ($n) {
            $oldNoSppd = $n->no_sppd;
            $n->nama = $request->nama;
            $n->jns_dinas = $request->jns_dinas;
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

    public function deklarasi($id)
    {
        $companies = Company::orderBy('contribution_level')->get();

        $n = BusinessTrip::find($id);
        return view('hcis.reimbursements.businessTrip.deklarasi', ['n' => $n, 'companies' => $companies]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $cari = $request->q;

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

        $sppdNos = $sppd->pluck('no_sppd');
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $sppd->appends($request->all());
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }
    public function filterDate(Request $request)
    {
        $user = Auth::user();
        $sppd = BusinessTrip::where('user_id', $user->id);
        $sppdNos = $sppd->pluck('no_sppd');

        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        if ($startDate && $endDate) {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->whereBetween('mulai', [$startDate, $endDate])
                ->orderBy('mulai', 'asc')
                ->paginate(10); // Adjust the pagination as needed
        } else {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->orderBy('mulai', 'asc')
                ->paginate(10);
        }
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }

    public function updatestatus($id, Request $request)
    {
        $statusValue = $request->input('status');

        // Update your model or database table with the new status value
        BusinessTrip::where('id', $id)->update(['status' => $statusValue]);
        $currentUrl = url()->previous();

        return redirect($currentUrl);
    }

    public function pdfDownload($id)
    {
        $sppd = BusinessTrip::findOrFail($id);

        $response = ['sppd' => $sppd];

        $caTransactions = ca_transaction::where('no_sppd', $sppd->no_sppd)->first();
        if ($caTransactions) {
            $response['caTransactions'] = $caTransactions;
        }
        $tickets = Tiket::where('no_sppd', $sppd->no_sppd)->first();
        if ($tickets) {
            $response['tickets'] = $tickets;
        }
        $hotel = Hotel::where('no_sppd', $sppd->no_sppd)->first();
        if ($hotel) {
            $response['hotel'] = $hotel;
        }
        $taksi = Taksi::where('no_sppd', $sppd->no_sppd)->first();
        if ($taksi) {
            $response['taksi'] = $taksi;
        }
        return response()->json($response);
    }
    public function export($id, $type)
    {
        try {
            Log::info("Export request received for ID: {$id}, Type: {$type}");

            $user = Auth::user();
            $pdfContent = null;
            $pdfName = '';

            switch ($type) {
                case 'sppd':
                    $sppd = BusinessTrip::where('user_id', $user->id)->where('id', $id)->firstOrFail();
                    $pdfName = 'SPPD.pdf';
                    $viewPath = 'hcis.reimbursements.businessTrip.sppd_pdf';
                    $data = ['sppd' => $sppd];
                    break;
                case 'ca':
                    $ca = ca_transaction::findOrFail($id);
                    $pdfName = 'CA.pdf';
                    $viewPath = 'hcis.reimbursements.businessTrip.ca_pdf';
                    $data = ['ca' => $ca];
                    break;
                case 'tiket':
                    $ticket = Tiket::findOrFail($id);
                    $pdfName = 'Ticket.pdf';
                    $viewPath = 'hcis.reimbursements.businessTrip.tiket_pdf';
                    $data = ['ticket' => $ticket];
                    break;
                case 'hotel':
                    $hotel = Hotel::findOrFail($id);
                    $pdfName = 'Hotel.pdf';
                    $viewPath = 'hcis.reimbursements.businessTrip.hotel_pdf';
                    $data = ['hotel' => $hotel];
                    break;
                case 'taksi':
                    $taksi = Taksi::findOrFail($id);
                    $pdfName = 'Taxi.pdf';
                    $viewPath = 'hcis.reimbursements.businessTrip.taksi_pdf';
                    $data = ['taksi' => $taksi];
                    break;
                default:
                    throw new \Exception('Invalid document type.');
            }

            Log::info("Attempting to load view: {$viewPath}");

            $pdfContent = PDF::loadView($viewPath, $data);

            Log::info("PDF content generated successfully");

            return $pdfContent->stream($pdfName);

        } catch (\Exception $e) {
            Log::error("Error in export function: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            } else {
                return back()->with('error', $e->getMessage());
            }
        }
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
        $bt = new BusinessTrip();
        $bt->id = (string) Str::uuid();

        $noSppd = $this->generateNoSppd();
        $userId = Auth::id();
        BusinessTrip::create([
            'id' => $bt->id,
            'user_id' => $userId,
            'jns_dinas' => $request->jns_dinas,
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

    public function approval()
    {
        $user = Auth::user();
        $perPage = request()->query('per_page', 10);
        $startDate = request()->query('start-date');
        $endDate = request()->query('end-date');
        $searchQuery = request()->query('q');

        $showData = $startDate || $endDate || $searchQuery;

        if ($showData) {
            $query = BusinessTrip::where('user_id', $user->id);

            if ($startDate && $endDate) {
                $query->whereBetween('mulai', [$startDate, $endDate]);
            }

            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('nama', 'like', "%{$searchQuery}%")
                        ->orWhere('no_sppd', 'like', "%{$searchQuery}%")
                        ->orWhere('divisi', 'like', "%{$searchQuery}%");
                });
            }

            $sppd = $query->orderBy('mulai', 'asc')->paginate($perPage);

            $sppdNos = $sppd->pluck('no_sppd');

            $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
            $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
            $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
            $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        } else {
            $sppd = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $caTransactions = collect([]);
            $tickets = collect([]);
            $hotel = collect([]);
            $taksi = collect([]);
        }

        $parentLink = 'Reimbursement';
        $link = 'BT Approval';

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'showData'));
    }

    public function searchApproval(Request $request)
    {
        $user = Auth::user();
        $cari = $request->q;

        $sppd = BusinessTrip::query()
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

        $sppdNos = $sppd->pluck('no_sppd');
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $sppd->appends($request->all());
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';
        $showData = true;

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'showData'));
    }
    public function filterDateApproval(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        $sppd = BusinessTrip::query();

        if ($startDate && $endDate) {
            $sppd = $sppd->whereBetween('mulai', [$startDate, $endDate]);
        }

        $sppd = $sppd->orderBy('mulai', 'asc')->paginate(10);

        $sppdNos = $sppd->pluck('no_sppd');
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';
        $showData = true;

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'showData'));
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
    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = BusinessTrip::query();

        if ($startDate && $endDate) {
            $query->whereBetween('mulai', [$startDate, $endDate]);
        }

        $businessTrips = $query->get();

        return Excel::download(new BusinessTripExport($businessTrips), 'Data Perjalanan Dinas.xlsx');
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
