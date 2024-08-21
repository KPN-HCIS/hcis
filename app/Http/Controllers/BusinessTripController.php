<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BTApproval;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\Company;
use App\Models\Designation;
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
        $n = BusinessTrip::findOrFail($id);
        if ($n) {
            $n->delete(); // Perform soft delete
        }
        return redirect()->route('businessTrip')->with('success', 'Business Trip marked as deleted.');
    }

    public function formUpdate($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        // Retrieve the taxi data for the specific BusinessTrip
        $taksi = Taksi::where('no_sppd', $n->no_sppd)->first();

        // Retrieve all hotels for the specific BusinessTrip
        $hotels = Hotel::where('no_sppd', $n->no_sppd)->get();

        // Prepare hotel data for the view
        $hotelData = [];
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < count($hotels) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve all tickets for the specific BusinessTrip
        $tickets = Tiket::where('no_sppd', $n->no_sppd)->get();

        // Prepare ticket data for the view
        $ticketData = [];
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'noktp_tkt' => $ticket->noktp_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();
        // dd($taksi->toArray());

        return view('hcis.reimbursements.businessTrip.editFormBt', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Fetch the business trip record to update
        $n = BusinessTrip::find($id);
        if (!$n) {
            return redirect()->back()->with('error', 'Business trip not found');
        }
        if ($request->tujuan === 'Others' && !empty($request->others_location)) {
            $tujuan = $request->others_location;  // Use the value from the text box
        } else {
            $tujuan = $request->tujuan;  // Use the selected dropdown value
        }

        // Store old SPPD number for later use
        $oldNoSppd = $n->no_sppd;
        $userId = Auth::id();
        $employee = Employee::where('id', $userId)->first();
        function findDepartmentHead($employee)
        {
            $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

            if (!$manager) {
                return null;
            }

            $designation = Designation::where('job_code', $manager->designation_code)->first();

            if ($designation->dept_head_flag == 'T') {
                return $manager;
            } else {
                return findDepartmentHead($manager);
            }
            return null;
        }

        $deptHeadManager = findDepartmentHead($employee);

        $managerL1 = $deptHeadManager->employee_id;
        $managerL2 = $deptHeadManager->manager_l1_id;
        // Update business trip record
        $n->update([
            'nama' => $request->nama,
            'jns_dinas' => $request->jns_dinas,
            'divisi' => $request->divisi,
            'unit_1' => $request->unit_1,
            'atasan_1' => $request->atasan_1,
            'email_1' => $request->email_1,
            'unit_2' => $request->unit_2,
            'atasan_2' => $request->atasan_2,
            'email_2' => $request->email_2,
            'no_sppd' => $oldNoSppd,  // Preserve old SPPD number
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
            'manager_l1_id' => $managerL1,
            'manager_l2_id' => $managerL2,
            'id_ca' => $request->id_ca,
            'id_tiket' => $request->id_tiket,
            'id_hotel' => $request->id_hotel,
            'id_taksi' => $request->id_taksi,
        ]);

        // Handle "Taksi" update
        if ($request->taksi === 'Ya') {
            // Fetch existing Taksi records
            $existingTaksi = Taksi::where('no_sppd', $oldNoSppd)->get()->keyBy('id');

            // If no existing Taksi record, or need to update existing records
            if (isset($request->no_vt)) {
                // Prepare the data for update
                $taksiData = [
                    'id' => (string) Str::uuid(),
                    'no_vt' => $request->no_vt,
                    'user_id' => Auth::id(),
                    'unit' => $request->divisi,
                    'no_sppd' => $oldNoSppd,
                    'nominal_vt' => (int) str_replace('.', '', $request->nominal_vt),  // Convert to integer
                    'keeper_vt' => (int) str_replace('.', '', $request->keeper_vt),
                ];

                // Check if there's an existing Taksi record to update
                $existingTaksiRecord = $existingTaksi->first();

                if ($existingTaksiRecord) {
                    // Update existing Taksi record
                    $existingTaksiRecord->update($taksiData);
                } else {
                    // Create a new Taksi record
                    Taksi::create($taksiData);
                }
            } else {
                // If 'Taksi' is set to 'Ya' but no data provided, clear existing records
                Taksi::where('no_sppd', $oldNoSppd)->delete();
            }
        } else {
            // Remove all Taksi records if 'Taksi' is not selected
            Taksi::where('no_sppd', $oldNoSppd)->delete();
        }


        // Handle "Hotel" update
        if ($request->hotel === 'Ya') {
            // Get all existing hotels for this business trip
            $existingHotels = Hotel::where('no_sppd', $oldNoSppd)->get()->keyBy('id');

            $processedHotelIds = [];

            foreach ($request->nama_htl as $key => $value) {
                if (!empty($value)) {
                    // Check if the hotel ID exists in the request
                    $hotelId = $request->hotel_id[$key] ?? null;

                    if ($hotelId && isset($existingHotels[$hotelId])) {
                        // Update existing hotel record
                        $hotel = $existingHotels[$hotelId];
                        $hotel->update([
                            'nama_htl' => $value,
                            'lokasi_htl' => $request->lokasi_htl[$key],
                            'jmlkmr_htl' => $request->jmlkmr_htl[$key],
                            'bed_htl' => $request->bed_htl[$key],
                            'tgl_masuk_htl' => $request->tgl_masuk_htl[$key],
                            'tgl_keluar_htl' => $request->tgl_keluar_htl[$key],
                            'total_hari' => $request->total_hari[$key],
                        ]);

                        $processedHotelIds[] = $hotelId;
                    } else {
                        // Create a new hotel record if no valid ID is provided
                        $newHotel = Hotel::create([
                            'id' => (string) Str::uuid(),
                            'no_htl' => $this->generateNoSppdHtl(),
                            'user_id' => Auth::id(),
                            'unit' => $request->divisi,
                            'no_sppd' => $oldNoSppd,
                            'nama_htl' => $value,
                            'lokasi_htl' => $request->lokasi_htl[$key],
                            'jmlkmr_htl' => $request->jmlkmr_htl[$key],
                            'bed_htl' => $request->bed_htl[$key],
                            'tgl_masuk_htl' => $request->tgl_masuk_htl[$key],
                            'tgl_keluar_htl' => $request->tgl_keluar_htl[$key],
                            'total_hari' => $request->total_hari[$key],
                        ]);

                        $processedHotelIds[] = $newHotel->id;
                    }
                }
            }

            // Remove hotels that are no longer in the request
            Hotel::where('no_sppd', $oldNoSppd)->whereNotIn('id', $processedHotelIds)->delete();
        } else {
            Hotel::where('no_sppd', $oldNoSppd)->delete();  // Remove all hotels if not selected
        }

        // Handle "Ticket" update
        if ($request->tiket === 'Ya') {
            // Get all existing tickets for this business trip
            $existingTickets = Tiket::where('no_sppd', $oldNoSppd)->get()->keyBy('noktp_tkt');

            $processedTicketIds = [];

            foreach ($request->noktp_tkt as $key => $value) {
                if (!empty($value)) {
                    // Prepare ticket data
                    $ticketData = [
                        'no_sppd' => $oldNoSppd,
                        'user_id' => Auth::id(),
                        'unit' => $request->divisi,
                        'dari_tkt' => $request->dari_tkt[$key] ?? null,
                        'ke_tkt' => $request->ke_tkt[$key] ?? null,
                        'tgl_brkt_tkt' => $request->tgl_brkt_tkt[$key] ?? null,
                        'jam_brkt_tkt' => $request->jam_brkt_tkt[$key] ?? null,
                        'jenis_tkt' => $request->jenis_tkt[$key] ?? null,
                        'type_tkt' => $request->type_tkt[$key] ?? null,
                        'tgl_plg_tkt' => $request->tgl_plg_tkt[$key] ?? null,
                        'jam_plg_tkt' => $request->jam_plg_tkt[$key] ?? null,
                    ];

                    // Fetch employee data to get jk_tkt
                    $employee_data = Employee::where('ktp', $value)->first();

                    if (!$employee_data) {
                        return redirect()->back()->with('error', "NIK $value not found");
                    }

                    // Ensure jk_tkt is included in the data
                    $ticketData['jk_tkt'] = $employee_data->gender ?? null;
                    $ticketData['np_tkt'] = $employee_data->fullname ?? null;
                    $ticketData['tlp_tkt'] = $employee_data->personal_mobile_number ?? null;

                    if (isset($existingTickets[$value])) {
                        // Update existing ticket
                        $existingTicket = $existingTickets[$value];
                        $existingTicket->update($ticketData);
                    } else {
                        // Create a new ticket entry
                        Tiket::create(array_merge($ticketData, [
                            'id' => (string) Str::uuid(),
                            'no_tkt' => $this->generateNoSppdTkt(),
                            'noktp_tkt' => $value,
                        ]));
                    }

                    // Track the processed ticket IDs
                    $processedTicketIds[] = $value;
                }
            }
            // Remove tickets that are no longer in the request
            Tiket::where('no_sppd', $oldNoSppd)
                ->whereNotIn('noktp_tkt', $processedTicketIds)
                ->delete();
        } else {
            // Remove all tickets if not selected
            Tiket::where('no_sppd', $oldNoSppd)->delete();
        }

        // Handle "CA Transaction" update
        if ($request->ca === 'Ya') {
            $ca = ca_transaction::updateOrCreate(
                ['no_sppd' => $oldNoSppd],
                [
                    'id' => (string) Str::uuid(),
                    'type_ca' => 'dns',
                    'no_ca' => $this->generateNoSppdCa(),
                    'no_sppd' => $oldNoSppd,
                    'user_id' => Auth::id(),
                    'unit' => $request->divisi,
                    'contribution_level_code' => Company::find($request->bb_perusahaan)->contribution_level_code,
                    'destination' => $request->tujuan,
                    'others_location' => $request->others_location,
                    'ca_needs' => $request->keperluan,
                    'start_date' => $request->mulai,
                    'end_date' => $request->kembali,
                    'date_required' => $request->date_required,
                    'total_days' => $request->total_days,
                    'detail_ca' => $request->detail_ca,
                    'total_ca' => $request->total_ca,
                    'total_real' => $request->total_real,
                    'total_cost' => $request->total_cost,
                    'approval_status' => $request->status,
                    'approval_sett' => $request->approval_sett,
                    'approval_extend' => $request->approval_extend,
                ]
            );
        } else {
            ca_transaction::where('no_sppd', $oldNoSppd)->delete();  // Remove CA transaction if not selected
        }

        return redirect('/businessTrip')->with('success', 'Business trip updated successfully');
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

            $sppd->load(['manager1', 'manager2', 'approvals.employee']);

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
        $noSppdTkt = $this->generateNoSppdTkt();
        $noSppdHtl = $this->generateNoSppdHtl();
        $userId = Auth::id();
        $employee = Employee::where('id', $userId)->first();

        function findDepartmentHead($employee)
        {
            $manager = Employee::where('employee_id', $employee->manager_l1_id)->first();

            if (!$manager) {
                return null;
            }

            $designation = Designation::where('job_code', $manager->designation_code)->first();

            if ($designation->dept_head_flag == 'T') {
                return $manager;
            } else {
                return findDepartmentHead($manager);
            }
            return null;
        }
        $deptHeadManager = findDepartmentHead($employee);

        $managerL1 = $deptHeadManager->employee_id;
        $managerL2 = $deptHeadManager->manager_l1_id;

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
            'manager_l1_id' => $managerL1,
            'manager_l2_id' => $managerL2,
            'id_ca' => $request->id_ca,
            'id_tiket' => $request->id_tiket,
            'id_hotel' => $request->id_hotel,
            'id_taksi' => $request->id_taksi,
        ]);
        if ($request->taksi === 'Ya') {
            $taksi = new Taksi();
            $taksi->id = (string) Str::uuid();
            $taksi->no_vt = $request->no_vt;
            $taksi->no_sppd = $noSppd;
            $taksi->user_id = $userId;
            $taksi->unit = $request->divisi;
            $taksi->nominal_vt = (int) str_replace('.', '', $request->nominal_vt);  // Convert to integer
            $taksi->keeper_vt = (int) str_replace('.', '', $request->keeper_vt);   // Convert to integer

            $taksi->save();
        }

        if ($request->hotel === 'Ya') {
            $hotelData = [
                'no_htl' => $noSppdTkt,
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
                    $hotel->no_htl = $noSppdHtl;
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
                    $tiket->no_tkt = $noSppdTkt;
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

        $sppd = BusinessTrip::where('status', '!=', 'Draft')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // No sppd
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        // dd($tickets);
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip (Admin)';

        return view('hcis.reimbursements.businessTrip.btAdmin', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }

    public function filterDateAdmin(Request $request)
    {
        // Retrieve the start and end dates from the request
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        // Build the query to exclude drafts and apply date filtering
        $query = BusinessTrip::where('status', '!=', 'Draft');

        if ($startDate && $endDate) {
            $query->whereBetween('mulai', [$startDate, $endDate])
                ->orderBy('mulai', 'asc');
        } else {
            $query->orderBy('mulai', 'asc');
        }

        // Execute the query and get the results
        $sppd = $query->get();
        $sppdNos = $sppd->pluck('no_sppd');

        // Fetch related data based on the filtered SPPD numbers
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip (Admin)';

        return view('hcis.reimbursements.businessTrip.btAdmin', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi'));
    }

    public function exportExcel(Request $request)
    {
        // Retrieve query parameters
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        // Initialize query builder
        $query = BusinessTrip::query();

        // Apply filters if both dates are present
        if ($startDate && $endDate) {
            $query->whereBetween('mulai', [$startDate, $endDate]);
        }

        // Exclude drafts
        $query->where('status', '<>', 'draft'); // Adjust 'status' and 'draft' as needed

        // Fetch the filtered data
        $businessTrips = $query->get();

        // Pass the filtered data to the export class
        return Excel::download(new BusinessTripExport($businessTrips), 'Data Perjalanan Dinas.xlsx');
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

    public function approvalDetail($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        // Retrieve the taxi data for the specific BusinessTrip
        $taksi = Taksi::where('no_sppd', $n->no_sppd)->first();

        // Retrieve all hotels for the specific BusinessTrip
        $hotels = Hotel::where('no_sppd', $n->no_sppd)->get();

        // Prepare hotel data for the view
        $hotelData = [];
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < count($hotels) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve all tickets for the specific BusinessTrip
        $tickets = Tiket::where('no_sppd', $n->no_sppd)->get();

        // Prepare ticket data for the view
        $ticketData = [];
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'noktp_tkt' => $ticket->noktp_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();
        // dd($taksi->toArray());

        return view('hcis.reimbursements.businessTrip.btApprovalDetail', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
        ]);
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

        if ($lastTransaction && preg_match('/(\d{3})\/SPPD-HC\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_sppd, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/SPPD-HC/$romanMonth/$currentYear";

        return $newNoSppd;
    }
    private function generateNoSppdHtl()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Assuming you want to generate no_sppd similarly to no_ca
        $lastTransaction = Hotel::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_htl', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/HTLD-HRD\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_htl, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/HTLD-HRD/$romanMonth/$currentYear";

        return $newNoSppd;
    }
    private function generateNoSppdTkt()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Assuming you want to generate no_sppd similarly to no_ca
        $lastTransaction = Tiket::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_tkt', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/TKTD-HRD\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_tkt, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/TKTD-HRD/$romanMonth/$currentYear";

        return $newNoSppd;
    }
    private function generateNoSppdCa()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Assuming you want to generate no_sppd similarly to no_ca
        $lastTransaction = ca_transaction::whereYear('created_at', $currentYear)
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
