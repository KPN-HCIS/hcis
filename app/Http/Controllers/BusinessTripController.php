<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BTApproval;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Taksi;
use App\Models\Tiket;
use Carbon\Carbon;
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

        $sppd = BusinessTrip::where('user_id', $user->id)->orderBy('mulai', 'asc')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // No sppd
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        // dd($tickets);
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
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
    public function deklarasiAdmin($id)
    {
        $companies = Company::orderBy('contribution_level')->get();

        $n = BusinessTrip::find($id);
        return view('hcis.reimbursements.businessTrip.deklarasiAdmin', ['n' => $n, 'companies' => $companies]);
    }

    public function filterDate(Request $request)
    {
        $user = Auth::user();
        $sppd = BusinessTrip::where('user_id', $user->id);
        $sppdNos = $sppd->pluck('no_sppd');

        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        if ($startDate && $endDate) {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->whereBetween('mulai', [$startDate, $endDate])
                ->orderBy('mulai', 'asc')
                ->get(); // Adjust the pagination as needed
        } else {
            $sppd = BusinessTrip::where('user_id', $user->id) // Filter by the user's ID
                ->orderBy('mulai', 'asc')
                ->get();
        }
        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }


    public function pdfDownload($id)
    {
        $sppd = BusinessTrip::findOrFail($id);
        $response = ['sppd' => $sppd];

        $types = [
            'ca' => ca_transaction::class,
            'tiket' => Tiket::class,
            'hotel' => Hotel::class,
            'taksi' => Taksi::class
        ];

        foreach ($types as $type => $model) {
            if (in_array($type, ['tiket', 'hotel'])) {
                $data = $model::where('no_sppd', $sppd->no_sppd)->get();
            } else {
                $data = $model::where('no_sppd', $sppd->no_sppd)->first();
            }

            if ($data) {
                $response[$type] = $data;
            }
        }

        return response()->json($response);
    }

    public function export($id, $types = null)
    {
        try {
            $user = Auth::user();
            $sppd = BusinessTrip::where('user_id', $user->id)->where('id', $id)->firstOrFail();

            if (!$types) {
                $types = ['sppd', 'ca', 'tiket', 'hotel', 'taksi'];
            } else {
                $types = explode(',', $types);
            }

            $zip = new ZipArchive();
            $zipFileName = 'Business Trip.zip';
            $zipFilePath = storage_path('app/public/' . $zipFileName);

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($types as $type) {
                    $pdfContent = null;
                    $pdfName = '';

                    switch ($type) {
                        case 'sppd':
                            $pdfName = 'SPPD.pdf';
                            $viewPath = 'hcis.reimbursements.businessTrip.sppd_pdf';
                            $data = ['sppd' => $sppd];
                            break;
                        case 'ca':
                            $ca = ca_transaction::where('no_sppd', $sppd->no_sppd)->first();
                            if (!$ca)
                                continue 2;
                            $pdfName = 'CA.pdf';
                            $viewPath = 'hcis.reimbursements.businessTrip.ca_pdf';
                            $data = ['ca' => $ca];
                            break;
                        case 'tiket':
                            $tickets = Tiket::where('no_sppd', $sppd->no_sppd)->get();
                            if ($tickets->isEmpty()) {
                                continue 2;
                            }
                            $pdfName = 'Ticket.pdf';
                            $viewPath = 'hcis.reimbursements.businessTrip.tiket_pdf';
                            $data = [
                                'ticket' => $tickets->first(),
                                'passengers' => $tickets->map(function ($ticket) {
                                    return (object) [
                                        'np_tkt' => $ticket->np_tkt,
                                        'tlp_tkt' => $ticket->tlp_tkt,
                                        'jk_tkt' => $ticket->jk_tkt,
                                        'dari_tkt' => $ticket->dari_tkt,
                                        'ke_tkt' => $ticket->ke_tkt,
                                        'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                                        'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                                        'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                                        'jam_plg_tkt' => $ticket->jam_plg_tkt,
                                        'type_tkt' => $ticket->type_tkt,
                                        'jenis_tkt' => $ticket->jenis_tkt,
                                        'company_name' => $ticket->employee->company_name,
                                        'cost_center' => $ticket->cost_center
                                    ];
                                })
                            ];
                            break;
                        case 'hotel':
                            $hotels = Hotel::where('no_sppd', $sppd->no_sppd)->get(); // Fetch all hotels with the given sppd
                            if ($hotels->isEmpty()) {
                                continue 2; // Skip if no hotels found
                            }
                            $pdfName = 'Hotel.pdf';
                            $viewPath = 'hcis.reimbursements.businessTrip.hotel_pdf';
                            $data = [
                                'hotel' => $hotels->first(), // Use the first hotel for general details
                                'hotels' => $hotels // Pass all hotels for detailed view
                            ];
                            break;


                        case 'taksi':
                            $taksi = Taksi::where('no_sppd', $sppd->no_sppd)->first();
                            if (!$taksi)
                                continue 2;
                            $pdfName = 'Taxi.pdf';
                            $viewPath = 'hcis.reimbursements.businessTrip.taksi_pdf';
                            $data = ['taksi' => $taksi];
                            break;
                        // case 'deklarasi':
                        //     $deklarasi = deklarasi::where('no_sppd', $sppd->no_sppd)->first();
                        //     if (!$deklarasi)
                        //         continue 2;
                        //     $pdfName = 'Deklarasi.pdf';
                        //     $viewPath = 'hcis.reimbursements.businessTrip.deklarasi_pdf';
                        //     $data = ['deklarasi' => $deklarasi];
                        //     break;
                        default:
                            continue 2;
                    }

                    $pdfContent = PDF::loadView($viewPath, $data)->output();
                    $zip->addFromString($pdfName, $pdfContent);
                }
                $zip->close();
            }

            return response()->download($zipFilePath)->deleteFileAfterSend(true);

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
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();
        return view(
            'hcis.reimbursements.businessTrip.formBusinessTrip',
            [
                'employee_data' => $employee_data,
                'companies' => $companies,
                'locations' => $locations,
            ]
        );
    }

    public function businessTripCreate(Request $request)
    {
        $bt = new BusinessTrip();

        $bt->id = (string) Str::uuid();

        // Fetch employee data using NIK
        $employee_data = null;
        if ($request->has('noktp_tkt') && !empty($request->noktp_tkt[0])) {
            $employee_data = Employee::where('ktp', $request->noktp_tkt[0])->first();
            if (!$employee_data) {
                return redirect()->back()->with('error', 'NIK not found');
            }
        }

        // Check if "Others" is selected in the "tujuan" dropdown
        if ($request->tujuan === 'Others' && !empty($request->others_location)) {
            $tujuan = $request->others_location;  // Use the value from the text box
        } else {
            $tujuan = $request->tujuan;  // Use the selected dropdown value
        }

        $noSppd = $this->generateNoSppd();
        $noSppdCa = $this->generateNoSppdCa();
        $userId = Auth::id();
        $employee = Employee::where('id', $userId)->first();

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
            'tujuan' => $tujuan,
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
            'manager_l1_id' => $employee->manager_l1_id,
            'manager_l2_id' => $employee->manager_l2_id,
        ]);
        if ($request->taksi === 'Ya') {
            $taksi = new Taksi();
            $taksi->id = (string) Str::uuid();
            $taksi->no_vt = $request->no_vt;
            $taksi->no_sppd = $noSppd;
            $taksi->user_id = $userId;
            $taksi->unit = $request->divisi;
            $taksi->nominal_vt = $request->nominal_vt;
            $taksi->keeper_vt = $request->keeper_vt;

            $taksi->save();
        }

        if ($request->hotel === 'Ya') {
            $hotelData = [
                'nama_htl' => $request->nama_htl,
                'lokasi_htl' => $request->lokasi_htl,
                'jmlkmr_htl' => $request->jmlkmr_htl,
                'bed_htl' => $request->bed_htl,
                'tgl_masuk_htl' => $request->tgl_masuk_htl,
                'tgl_keluar_htl' => $request->tgl_keluar_htl,
                'total_hari' => $request->total_hari,
            ];

            foreach ($hotelData['nama_htl'] as $key => $value) {
                if (!empty($value)) {
                    $hotel = new Hotel();
                    $hotel->id = (string) Str::uuid();
                    $hotel->no_htl = $key + 1; // This will give us a sequential number starting from 1
                    $hotel->no_sppd = $noSppd;
                    $hotel->user_id = $userId;
                    $hotel->unit = $request->divisi;
                    $hotel->nama_htl = $value;
                    $hotel->lokasi_htl = $hotelData['lokasi_htl'][$key];
                    $hotel->jmlkmr_htl = $hotelData['jmlkmr_htl'][$key];
                    $hotel->bed_htl = $hotelData['bed_htl'][$key];
                    $hotel->tgl_masuk_htl = $hotelData['tgl_masuk_htl'][$key];
                    $hotel->tgl_keluar_htl = $hotelData['tgl_keluar_htl'][$key];
                    $hotel->total_hari = $hotelData['total_hari'][$key];

                    $hotel->save();
                }
            }
        }

        if ($request->tiket === 'Ya') {
            $ticketData = [
                'noktp_tkt' => $request->noktp_tkt,
                'dari_tkt' => $request->dari_tkt,
                'ke_tkt' => $request->ke_tkt,
                'tgl_brkt_tkt' => $request->tgl_brkt_tkt,
                'tgl_plg_tkt' => $request->tgl_plg_tkt,
                'jam_brkt_tkt' => $request->jam_brkt_tkt,
                'jam_plg_tkt' => $request->jam_plg_tkt,
                'jenis_tkt' => $request->jenis_tkt,
                'type_tkt' => $request->type_tkt,
            ];

            foreach ($ticketData['noktp_tkt'] as $key => $value) {
                if (!empty($value)) {
                    // Fetch employee data inside the loop
                    $employee_data = Employee::where('ktp', $value)->first();

                    if (!$employee_data) {
                        return redirect()->back()->with('error', "NIK $value not found");
                    }

                    $tiket = new Tiket();
                    $tiket->id = (string) Str::uuid();
                    $tiket->no_sppd = $noSppd;
                    $tiket->user_id = $userId;
                    $tiket->unit = $request->divisi;
                    $tiket->jk_tkt = $employee_data ? $employee_data->gender : null;
                    $tiket->np_tkt = $employee_data ? $employee_data->fullname : null;
                    $tiket->noktp_tkt = $value;
                    $tiket->tlp_tkt = $employee_data ? $employee_data->personal_mobile_number : null;
                    $tiket->dari_tkt = $ticketData['dari_tkt'][$key] ?? null;
                    $tiket->ke_tkt = $ticketData['ke_tkt'][$key] ?? null;
                    $tiket->tgl_brkt_tkt = $ticketData['tgl_brkt_tkt'][$key] ?? null;
                    $tiket->tgl_plg_tkt = $ticketData['tgl_plg_tkt'][$key] ?? null;
                    $tiket->jam_brkt_tkt = $ticketData['jam_brkt_tkt'][$key] ?? null;
                    $tiket->jam_plg_tkt = $ticketData['jam_plg_tkt'][$key] ?? null;
                    $tiket->jenis_tkt = $ticketData['jenis_tkt'][$key] ?? null;
                    $tiket->type_tkt = $ticketData['type_tkt'][$key] ?? null;

                    $tiket->save();
                }
            }
        }


        if ($request->ca === 'Ya') {
            $ca = new ca_transaction();
            $ca->id = (string) Str::uuid();
            $ca->type_ca = 'dns';
            $ca->no_ca = $noSppdCa;
            $ca->no_sppd = $noSppd;
            $ca->user_id = $userId;
            $ca->unit = $request->divisi;

            $company = Company::find($request->bb_perusahaan);

            $ca->contribution_level_code = $company->contribution_level_code;
            $ca->destination = $request->tujuan;
            $ca->others_location = $request->others_location;

            $ca->ca_needs = $request->keperluan;
            $ca->start_date = $request->mulai;
            $ca->end_date = $request->kembali;

            $ca->date_required = $request->date_required;
            $ca->total_days = $request->total_days;
            $ca->detail_ca = $request->detail_ca;
            $ca->total_ca = $request->total_ca;
            $ca->total_real = $request->total_real;
            $ca->total_cost = $request->total_cost;

            $ca->approval_status = $request->status;
            $ca->approval_sett = $request->approval_sett;
            $ca->approval_extend = $request->approval_extend;


            $ca->save();
        }
        return redirect('/businessTrip');
    }

    public function saveDraft(Request $request)
    {
        // Create a new BusinessTrip instance
        $bt = new BusinessTrip();
        $bt->id = (string) Str::uuid();
        $userId = Auth::id();
        $noSppd = $this->generateNoSppd();

        // Extract all input data
        $draftData = $request->all();
        $draftData['id'] = $bt->id;
        $draftData['user_id'] = $userId;
        $draftData['no_sppd'] = $noSppd;
        $draftData['status'] = 'Draft'; // Ensure status is set to 'Draft'

        // Create the BusinessTrip record
        BusinessTrip::create($draftData);

        // Handle related models if needed (Taksi, Hotel, Tiket, ca_transaction)

        // Respond with JSON
        return response()->json(['success' => true]);
    }


    public function admin()
    {
        $user = Auth::user();

        $sppd = BusinessTrip::where('user_id', $user->id)->orderBy('mulai', 'asc')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // No sppd
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        // dd($tickets);
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.btAdmin', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }
    public function filterDateAdmin(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        $sppd = BusinessTrip::query();

        if ($startDate && $endDate) {
            $sppd = $sppd->whereBetween('mulai', [$startDate, $endDate]);
        }

        $sppd = $sppd->orderBy('mulai', 'asc')->get();

        $sppdNos = $sppd->pluck('no_sppd');
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';
        $showData = true;

        return view('hcis.reimbursements.businessTrip.btAdmin', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'showData'));
    }
    public function approval()
    {
        $user = Auth::user();
        $sppd = BusinessTrip::where(function ($query) use ($user) {
            $query->where('manager_l1_id', $user->employee_id)
                ->where('status', 'Pending L1')
                ->orWhere(function ($query) use ($user) {
                    $query->where('manager_l2_id', $user->employee_id)
                        ->where('status', 'Pending L2');
                });
        })
            ->orderBy('mulai', 'asc')
            ->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // Retrieve related data based on the collected SPPD numbers
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Approval';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }
    public function updateStatus($id, Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;
        $approval = new BTApproval();
        $approval->id = (string) Str::uuid();

        // Find the business trip by ID
        $businessTrip = BusinessTrip::findOrFail($id);

        // Determine the new status and layer based on the action and manager's role
        $action = $request->input('status_approval');
        if ($action == 'Rejected') {
            $statusValue = 'Rejected';
            if ($employeeId == $businessTrip->manager_l1_id) {
                $layer = 1;
            } elseif ($employeeId == $businessTrip->manager_l2_id) {
                $layer = 2;
            } else {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }
        } elseif ($employeeId == $businessTrip->manager_l1_id) {
            $statusValue = 'Pending L2';
            $layer = 1;
        } elseif ($employeeId == $businessTrip->manager_l2_id) {
            $statusValue = 'Approved';
            $layer = 2;
        } else {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Update the status in the BusinessTrip table
        $businessTrip->update(['status' => $statusValue]);

        // Record the approval or rejection in the BTApproval table
        $approval->bt_id = $businessTrip->id;
        $approval->layer = $layer;
        $approval->approval_status = $statusValue;
        $approval->approved_at = now();
        $approval->employee_id = $employeeId;

        // Save the approval record
        $approval->save();
        $message = $statusValue === 'Rejected'
            ? 'The request has been successfully' . ' Rejected.'
            : 'The request has been successfully' . ' Accepted.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', $message);
    }


    public function filterDateApproval(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        $sppd = BusinessTrip::query();

        // Apply date filtering if both startDate and endDate are provided
        if ($startDate && $endDate) {
            $sppd = $sppd->whereBetween('mulai', [$startDate, $endDate]);
        }

        // Filter based on manager's role and status
        $sppd = $sppd->where(function ($query) use ($user) {
            $query->where('manager_l1_id', $user->employee_id)
                ->where('status', 'Pending L1')
                ->orWhere(function ($query) use ($user) {
                    $query->where('manager_l2_id', $user->employee_id)
                        ->where('status', 'Pending L2');
                });
        });

        // Order and retrieve the filtered results
        $sppd = $sppd->orderBy('mulai', 'asc')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // Retrieve related data based on the collected SPPD numbers
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
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

        if ($lastTransaction && preg_match('/(\d{3})\/SPPD-HRD\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_sppd, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/SPPD-HRD/$romanMonth/$currentYear";

        return $newNoSppd;
    }
    private function generateNoSppdCa()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Assuming you want to generate no_sppd similarly to no_ca
        $lastTransaction = BusinessTrip::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_sppd', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/SPPD-CA\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_sppd, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/SPPD-CA/$romanMonth/$currentYear";

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
