<?php

namespace App\Http\Controllers;

use App\Exports\BusinessTripExport;
use App\Exports\UsersExport;
use App\Models\BTApproval;
use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\CATransaction;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Taksi;
use App\Models\Tiket;
use App\Models\ca_sett_approval;
use App\Models\ListPerdiem;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\DB;
use App\Models\MatrixApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Models\ca_approval;


class BusinessTripController extends Controller
{
    public function businessTrip(Request $request)
    {
        $user = Auth::user();
        $query = BusinessTrip::where('user_id', $user->id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'all' if not provided
        $filter = $request->input('filter', 'all');

        if ($filter === 'request') {
            // Show all data where the date is < today and status is in ['Pending L1', 'Pending L2', 'Draft']
            $query->where(function ($query) {
                $query->whereDate('kembali', '<', now())
                    ->whereIn('status', ['Pending L1', 'Pending L2', 'Draft']);
            });
        } elseif ($filter === 'declaration') {
            // Show data with Approved, Declaration L1, Declaration L2, Draft Declaration
            $query->where(function ($query) {
                $query->whereIn('status', ['Approved', 'Declaration L1', 'Declaration L2', 'Declaration Draft']);
            });
        } elseif ($filter === 'done') {
            // Show data with Rejected, Refund, Doc Accepted, Verified
            $query->where(function ($query) {
                $query->whereIn('status', ['Rejected', 'Refund', 'Doc Accepted', 'Verified']);
            });
        }

        // If 'all' is selected or no filter is applied, just get all data
        if ($filter === 'all') {
            // No additional where clauses needed for 'all'
        }

        $sppd = $query->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // Fetch related data
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        // Get manager names
        $managerL1Names = Employee::whereIn('employee_id', $sppd->pluck('manager_l1_id'))->pluck('fullname', 'employee_id');
        $managerL2Names = Employee::whereIn('employee_id', $sppd->pluck('manager_l2_id'))->pluck('fullname', 'employee_id');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.businessTrip', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'managerL1Names', 'managerL2Names', 'filter'));
    }

    public function delete($id)
    {
        $n = BusinessTrip::findOrFail($id);
        if ($n) {
            $n->delete(); // Perform soft delete
        }
        return redirect()->route('businessTrip')->with('success', 'Business Trip marked as deleted.');
    }
    public function deleteAdmin($id)
    {
        $n = BusinessTrip::findOrFail($id);
        if ($n) {
            $n->delete(); // Perform soft delete
        }
        return redirect()->route('businessTrip.admin')->with('success', 'Business Trip marked as deleted.');
    }

    public function formUpdate($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        $ca = CATransaction::where('no_sppd', $n->no_sppd)->first();

        // Initialize caDetail with an empty array if it's null
        $caDetail = $ca ? json_decode($ca->detail_ca, true) : [];

        // Safely access nominalPerdiem with default '0' if caDetail is empty
        $nominalPerdiem = isset($caDetail['detail_perdiem'][0]['nominal']) ? $caDetail['detail_perdiem'][0]['nominal'] : '0';

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
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();

        return view('hcis.reimbursements.businessTrip.editFormBt', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
            'caDetail' => $caDetail,
            'ca' => $ca,
            'nominalPerdiem' => $nominalPerdiem,
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
                        'ket_tkt' => $request->ket_tkt[$key] ?? null,
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
        $oldNoCa = $request->old_no_ca; // Ensure you have the old `no_ca`

        if ($request->ca === 'Ya') {
            // Check if a CA transaction already exists for the given no_sppd
            $ca = CATransaction::where('no_sppd', $oldNoSppd)->first();
            if (!$ca) {
                // Create a new CA transaction
                $ca = new CATransaction();

                // Generate new 'no_ca' code
                $currentYear = date('Y');
                $currentYearShort = date('y');
                $prefix = 'CA';
                $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
                    ->orderBy('no_ca', 'desc')
                    ->first();

                $lastNumber = $lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches) ? intval($matches[1]) : 0;
                $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                $newNoCa = "$prefix$currentYearShort$newNumber";

                $ca->id = (string) Str::uuid();
                $ca->no_ca = $newNoCa;
            } else {
                // Update the existing CA transaction
                $ca->no_ca = $ca->no_ca; // Keep the existing no_ca
            }

            // Assign or update values to $ca model
            $ca->type_ca = 'dns';
            $ca->no_sppd = $oldNoSppd;
            $ca->user_id = $userId;
            $ca->unit = $request->divisi;
            $ca->contribution_level_code = $request->bb_perusahaan;
            $ca->destination = $request->tujuan;
            $ca->others_location = $request->others_location;
            $ca->ca_needs = $request->keperluan;
            $ca->start_date = $request->mulai;
            $ca->end_date = $request->kembali;
            $ca->date_required = Carbon::parse($request->kembali)->addDays(3);
            $ca->declare_estimate = Carbon::parse($request->kembali)->addDays(3);
            $ca->total_days = Carbon::parse($request->mulai)->diffInDays(Carbon::parse($request->kembali));
            $ca->total_ca = (int) str_replace('.', '', $request->totalca);
            $ca->total_real = '0';
            $ca->total_cost = (int) str_replace('.', '', $request->totalca);
            $ca->approval_status = $request->status;
            $ca->approval_sett = $request->approval_sett;
            $ca->approval_extend = $request->approval_extend;
            $ca->created_by = $userId;

            // Initialize arrays for details
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Populate detail_perdiem
            if ($request->has('start_bt_perdiem')) {
                foreach ($request->start_bt_perdiem as $key => $startDate) {
                    $endDate = $request->end_bt_perdiem[$key] ?? '';
                    $totalDays = $request->total_days_bt_perdiem[$key] ?? '';
                    $location = $request->location_bt_perdiem[$key] ?? '';
                    $other_location = $request->other_location_bt_perdiem[$key] ?? '';
                    $companyCode = $request->company_bt_perdiem[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_perdiem[$key] ?? '0');

                    $detail_perdiem[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'location' => $location,
                        'other_location' => $other_location,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Populate detail_transport
            if ($request->has('tanggal_bt_transport')) {
                foreach ($request->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $request->keterangan_bt_transport[$key] ?? '';
                    $companyCode = $request->company_bt_transport[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_transport[$key] ?? '0');

                    $detail_transport[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Populate detail_penginapan
            if ($request->has('start_bt_penginapan')) {
                foreach ($request->start_bt_penginapan as $key => $startDate) {
                    $endDate = $request->end_bt_penginapan[$key] ?? '';
                    $totalDays = $request->total_days_bt_penginapan[$key] ?? '';
                    $hotelName = $request->hotel_name_bt_penginapan[$key] ?? '';
                    $companyCode = $request->company_bt_penginapan[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_penginapan[$key] ?? '0');
                    $totalPenginapan = str_replace('.', '', $request->total_bt_penginapan[$key] ?? '0');

                    $detail_penginapan[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'hotel_name' => $hotelName,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                        'totalPenginapan' => $totalPenginapan,
                    ];
                }
            }

            // Populate detail_lainnya
            if ($request->has('tanggal_bt_lainnya')) {
                foreach ($request->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $request->keterangan_bt_lainnya[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_lainnya[$key] ?? '0');
                    $totalLainnya = str_replace('.', '', $request->total_bt_lainnya[$key] ?? '0');

                    $detail_lainnya[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'nominal' => $nominal,
                        'totalLainnya' => $totalLainnya,
                    ];
                }
            }

            // Save the details
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $ca->detail_ca = json_encode($detail_ca);
            $ca->declare_ca = json_encode($detail_ca);


            $model = $ca;

            $model->status_id = $managerL1;

            $cek_director_id = Employee::select([
                'dsg.department_level2',
                'dsg2.director_flag',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                'dsg2.designation_name',
                'dsg2.job_code',
                'emp.fullname',
                'emp.employee_id',
            ])
                ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                ->where('employees.designation_code', '=', $employee->designation_code)
                ->where('dsg2.director_flag', '=', 'F')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval

            $total_ca = str_replace('.', '', $request->totalca);
            // dd($total_ca);
            // dd($employee->group_company);
            // dd($request->bb_perusahaan);
            $data_matrix_approvals = MatrixApproval::where('modul', 'dns')
                ->where('group_company', 'like', '%' . $employee->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $request->bb_perusahaan . '%')
                ->whereRaw(
                    '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                    [$total_ca]
                )
                ->get();
            foreach ($data_matrix_approvals as $data_matrix_approval) {

                if ($data_matrix_approval->employee_id == "cek_L1") {
                    $employee_id = $managerL1;
                } else if ($data_matrix_approval->employee_id == "cek_L2") {
                    $employee_id = $managerL2;
                } else if ($data_matrix_approval->employee_id == "cek_director") {
                    $employee_id = $director_id;
                } else {
                    $employee_id = $data_matrix_approval->employee_id;
                }
                // $uuid = Str::uuid();
                $model_approval = new ca_approval;
                $model_approval->ca_id = $id;
                $model_approval->role_name = $data_matrix_approval->desc;
                $model_approval->employee_id = $employee_id;
                $model_approval->layer = $data_matrix_approval->layer;
                $model_approval->approval_status = 'Pending';

                // Simpan data ke database
                $model_approval->save();
            }
        } else {
            // If CA is not selected, remove existing CA transaction for this no_sppd
            CATransaction::where('no_sppd', $oldNoSppd)->delete();
        }

        return redirect('/businessTrip')->with('success', 'Business trip updated successfully');
    }

    public function deklarasi($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        $ca = CATransaction::where('no_sppd', $n->no_sppd)->first();

        // Initialize caDetail with an empty array if it's null
        $caDetail = $ca ? json_decode($ca->detail_ca, true) : [];
        $declareCa = $ca ? json_decode($ca->declare_ca, true) : [];

        // Safely access nominalPerdiem with default '0' if caDetail is empty
        $nominalPerdiem = isset($caDetail['detail_perdiem'][0]['nominal']) ? $caDetail['detail_perdiem'][0]['nominal'] : '0';
        $nominalPerdiemDeclare = isset($declareCa['detail_perdiem'][0]['nominal']) ? $declareCa['detail_perdiem'][0]['nominal'] : '0';

        $hasCaData = $ca !== null;
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
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();

        return view('hcis.reimbursements.businessTrip.deklarasi', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
            'caDetail' => $caDetail,
            'declareCa' => $declareCa,
            'ca' => $ca,
            'nominalPerdiem' => $nominalPerdiem,
            'nominalPerdiemDeclare' => $nominalPerdiemDeclare,
            'hasCaData' => $hasCaData, // Pass the flag to the view
        ]);
    }
    public function deklarasiCreate(Request $request, $id)
    {
        // Fetch the business trip record to update
        $n = BusinessTrip::find($id);

        // Update the status field in the BusinessTrip record
        $n->update([
            'status' => $request->status,
        ]);

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

        // Handle "CA Transaction" update
        $ca = CATransaction::where('no_sppd', $oldNoSppd)->first();

        if (!$ca) {
            // Create a new CA transaction if it doesn't exist
            $ca = new CATransaction();

            // Generate new 'no_ca' code
            $currentYear = date('Y');
            $currentYearShort = date('y');
            $prefix = 'CA';
            $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
                ->orderBy('no_ca', 'desc')
                ->first();

            $lastNumber = $lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches) ? intval($matches[1]) : 0;
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $newNoCa = "$prefix$currentYearShort$newNumber";

            $ca->id = (string) Str::uuid();
            $ca->no_ca = $newNoCa;
        }

        // Assign or update values to $ca model
        $ca->no_sppd = $oldNoSppd;
        $ca->user_id = $userId;
        // Update approval_status based on the status value from the request
        if ($request->status === 'Declaration L1') {
            $ca->approval_sett = 'Pending';
        } elseif ($request->status === 'Declaration Draft') {
            $ca->approval_sett = 'Draft';
        } else {
            $ca->approval_sett = $request->status;
        }

        $total_real = (int) str_replace('.', '', $request->totalca);
        $total_ca = $ca->total_ca;

        // Assign total_real and calculate total_cost
        $ca->total_real = $total_real;
        $ca->total_cost = $total_ca - $total_real;

        // Initialize arrays for details
        $detail_perdiem = [];
        $detail_transport = [];
        $detail_penginapan = [];
        $detail_lainnya = [];

        // Populate detail_perdiem
        if ($request->has('start_bt_perdiem')) {
            foreach ($request->start_bt_perdiem as $key => $startDate) {
                $detail_perdiem[] = [
                    'start_date' => $startDate,
                    'end_date' => $request->end_bt_perdiem[$key] ?? '',
                    'total_days' => $request->total_days_bt_perdiem[$key] ?? '',
                    'location' => $request->location_bt_perdiem[$key] ?? '',
                    'other_location' => $request->other_location_bt_perdiem[$key] ?? '',
                    'company_code' => $request->company_bt_perdiem[$key] ?? '',
                    'nominal' => str_replace('.', '', $request->nominal_bt_perdiem[$key] ?? '0'),
                ];
            }
        }

        // Populate detail_transport
        if ($request->has('tanggal_bt_transport')) {
            foreach ($request->tanggal_bt_transport as $key => $tanggal) {
                $detail_transport[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $request->keterangan_bt_transport[$key] ?? '',
                    'company_code' => $request->company_bt_transport[$key] ?? '',
                    'nominal' => str_replace('.', '', $request->nominal_bt_transport[$key] ?? '0'),
                ];
            }
        }

        // Populate detail_penginapan
        if ($request->has('start_bt_penginapan')) {
            foreach ($request->start_bt_penginapan as $key => $startDate) {
                $detail_penginapan[] = [
                    'start_date' => $startDate,
                    'end_date' => $request->end_bt_penginapan[$key] ?? '',
                    'total_days' => $request->total_days_bt_penginapan[$key] ?? '',
                    'hotel_name' => $request->hotel_name_bt_penginapan[$key] ?? '',
                    'company_code' => $request->company_bt_penginapan[$key] ?? '',
                    'nominal' => str_replace('.', '', $request->nominal_bt_penginapan[$key] ?? '0'),
                    'totalPenginapan' => str_replace('.', '', $request->total_bt_penginapan[$key] ?? '0'),
                ];
            }
        }

        // Populate detail_lainnya
        if ($request->has('tanggal_bt_lainnya')) {
            foreach ($request->tanggal_bt_lainnya as $key => $tanggal) {
                $detail_lainnya[] = [
                    'tanggal' => $tanggal,
                    'keterangan' => $request->keterangan_bt_lainnya[$key] ?? '',
                    'nominal' => str_replace('.', '', $request->nominal_bt_lainnya[$key] ?? '0'),
                    'totalLainnya' => str_replace('.', '', $request->total_bt_lainnya[$key] ?? '0'),
                ];
            }
        }

        // Save the details
        $declare_ca = [
            'detail_perdiem' => $detail_perdiem,
            'detail_transport' => $detail_transport,
            'detail_penginapan' => $detail_penginapan,
            'detail_lainnya' => $detail_lainnya,
        ];
        if ($request->hasFile('prove_declare')) {
            $file = $request->file('prove_declare');
            $path = $file->store('public/proofs'); // Store in 'public/proofs' directory
            $ca->prove_declare = $path;
        }

        $ca->declare_ca = json_encode($declare_ca);
        $model = $ca;

        $model->sett_id = $managerL1;

        $cek_director_id = Employee::select([
            'dsg.department_level2',
            'dsg2.director_flag',
            DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
            'dsg2.designation_name',
            'dsg2.job_code',
            'emp.fullname',
            'emp.employee_id',
        ])
            ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
            ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
            ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
            ->where('employees.designation_code', '=', $employee->designation_code)
            ->where('dsg2.director_flag', '=', 'F')
            ->get();

        $director_id = "";

        if ($cek_director_id->isNotEmpty()) {
            $director_id = $cek_director_id->first()->employee_id;
        }
        //cek matrix approval

        $total_ca = str_replace('.', '', $request->totalca);
        // dd($total_ca);
        // dd($employee->group_company);
        // dd($request->bb_perusahaan);
        $data_matrix_approvals = MatrixApproval::where('modul', 'dns')
            ->where('group_company', 'like', '%' . $employee->group_company . '%')
            ->where('contribution_level_code', 'like', '%' . $request->bb_perusahaan . '%')
            ->whereRaw(
                '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                [$total_ca]
            )
            ->get();
        // dd($request->bb_perusahaan);
        foreach ($data_matrix_approvals as $data_matrix_approval) {

            if ($data_matrix_approval->employee_id == "cek_L1") {
                $employee_id = $managerL1;
            } else if ($data_matrix_approval->employee_id == "cek_L2") {
                $employee_id = $managerL2;
            } else if ($data_matrix_approval->employee_id == "cek_director") {
                $employee_id = $director_id;
            } else {
                $employee_id = $data_matrix_approval->employee_id;
            }
            // $uuid = Str::uuid();
            $model_approval = new ca_sett_approval;
            $model_approval->ca_id = $request->no_id;
            $model_approval->role_name = $data_matrix_approval->desc;
            $model_approval->employee_id = $employee_id;
            $model_approval->layer = $data_matrix_approval->layer;
            $model_approval->approval_status = 'Pending';

            // Simpan data ke database
            $model_approval->save();
        }
        $ca->save();
        return redirect('/businessTrip')->with('success', 'Business trip updated successfully');
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
                            $ca = CATransaction::where('no_sppd', $sppd->no_sppd)->first();
                            if (!$ca)
                                continue 2;

                            // Integrate CA PDF generation from cashadvancedDownload
                            $pdfName = 'CA.pdf';
                            $viewPath = 'hcis.reimbursements.cashadv.printCashadv';
                            $employee_data = Employee::where('id', $user->id)->first();
                            $companies = Company::orderBy('contribution_level')->get();
                            $locations = Location::orderBy('area')->get();
                            $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
                            $no_sppds = CATransaction::where('user_id', $user->id)->where('approval_sett', '!=', 'Done')->get();

                            $data = [
                                'link' => 'Cash Advanced',
                                'parentLink' => 'Reimbursement',
                                'userId' => $user->id,
                                'companies' => $companies,
                                'locations' => $locations,
                                'employee_data' => $employee_data,
                                'perdiem' => $perdiem,
                                'no_sppds' => $no_sppds,
                                'transactions' => $ca,
                            ];
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
    public function pdfDownloadAdmin($id)
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

    public function exportAdmin($id, $types = null)
    {
        try {
            $user = Auth::user();
            $sppd = BusinessTrip::findOrFail($id);

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
                            $ca = CATransaction::where('no_sppd', $sppd->no_sppd)->first();
                            if (!$ca)
                                continue 2;

                            // Integrate CA PDF generation from cashadvancedDownload
                            $pdfName = 'CA.pdf';
                            $viewPath = 'hcis.reimbursements.cashadv.printCashadv';
                            $employee_data = Employee::where('id', $user->id)->first();
                            $companies = Company::orderBy('contribution_level')->get();
                            $locations = Location::orderBy('area')->get();
                            $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
                            $no_sppds = CATransaction::where('user_id', $user->id)->where('approval_sett', '!=', 'Done')->get();

                            $data = [
                                'link' => 'Cash Advanced',
                                'parentLink' => 'Reimbursement',
                                'userId' => $user->id,
                                'companies' => $companies,
                                'locations' => $locations,
                                'employee_data' => $employee_data,
                                'perdiem' => $perdiem,
                                'no_sppds' => $no_sppds,
                                'transactions' => $ca,
                            ];
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
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        return view(
            'hcis.reimbursements.businessTrip.formBusinessTrip',
            [
                'employee_data' => $employee_data,
                'companies' => $companies,
                'locations' => $locations,
                'no_sppds' => $no_sppds,
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
        // $noSppdCa = $this->generateNoSppdCa();
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
                'ket_tkt' => $request->ket_tkt,
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
                    $tiket->ket_tkt = $ticketData['ket_tkt'][$key] ?? null;

                    $tiket->save();
                }
            }
        }


        if ($request->ca === 'Ya') {
            $ca = new CATransaction();
            $businessTripStatus = $request->input('status');

            // Generate new 'no_ca' code
            $currentYear = date('Y');
            $currentYearShort = date('y');
            $prefix = 'CA';
            $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
                ->orderBy('no_ca', 'desc')
                ->first();

            $lastNumber = $lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches) ? intval($matches[1]) : 0;
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $newNoCa = "$prefix$currentYearShort$newNumber";

            $ca_id = (string) Str::uuid();
            // Assign values to $ca model
            $ca->id = $ca_id;
            $ca->type_ca = 'dns';
            $ca->no_ca = $newNoCa;
            $ca->no_sppd = $noSppd;
            $ca->user_id = $userId;
            $ca->unit = $request->divisi;
            $ca->contribution_level_code = $request->bb_perusahaan;
            $ca->destination = $request->tujuan;
            $ca->others_location = $request->others_location;
            $ca->ca_needs = $request->keperluan;
            $ca->start_date = $request->mulai;
            $ca->end_date = $request->kembali;
            $ca->date_required = Carbon::parse($request->kembali)->addDays(3);
            $ca->declare_estimate = Carbon::parse($request->kembali)->addDays(3);
            $ca->total_days = Carbon::parse($request->mulai)->diffInDays(Carbon::parse($request->kembali));
            $ca->total_ca = (int) str_replace('.', '', $request->totalca);
            $ca->total_real = '0';
            $ca->total_cost = (int) str_replace('.', '', $request->totalca);
            $ca->approval_status = 'Pending';
            $ca->approval_sett = $request->approval_sett;
            $ca->approval_extend = $request->approval_extend;
            $ca->created_by = $userId;

            // Initialize arrays
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Populate detail_perdiem
            if ($request->has('start_bt_perdiem')) {
                foreach ($request->start_bt_perdiem as $key => $startDate) {
                    $endDate = $request->end_bt_perdiem[$key] ?? '';
                    $totalDays = $request->total_days_bt_perdiem[$key] ?? '';
                    $location = $request->location_bt_perdiem[$key] ?? '';
                    $other_location = $request->other_location_bt_perdiem[$key] ?? '';
                    $companyCode = $request->company_bt_perdiem[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_perdiem[$key] ?? '0');

                    $detail_perdiem[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'location' => $location,
                        'other_location' => $other_location,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Populate detail_transport
            if ($request->has('tanggal_bt_transport')) {
                foreach ($request->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $request->keterangan_bt_transport[$key] ?? '';
                    $companyCode = $request->company_bt_transport[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_transport[$key] ?? '0');

                    $detail_transport[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Populate detail_penginapan
            if ($request->has('start_bt_penginapan')) {
                foreach ($request->start_bt_penginapan as $key => $startDate) {
                    $endDate = $request->end_bt_penginapan[$key] ?? '';
                    $totalDays = $request->total_days_bt_penginapan[$key] ?? '';
                    $hotelName = $request->hotel_name_bt_penginapan[$key] ?? '';
                    $companyCode = $request->company_bt_penginapan[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_penginapan[$key] ?? '0');
                    $totalPenginapan = str_replace('.', '', $request->total_bt_penginapan[$key] ?? '0');

                    $detail_penginapan[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'hotel_name' => $hotelName,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                        'totalPenginapan' => $totalPenginapan,
                    ];
                }
            }

            // Populate detail_lainnya
            if ($request->has('tanggal_bt_lainnya')) {
                foreach ($request->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $request->keterangan_bt_lainnya[$key] ?? '';
                    $nominal = str_replace('.', '', $request->nominal_bt_lainnya[$key] ?? '0');
                    $totalLainnya = str_replace('.', '', $request->total_bt_lainnya[$key] ?? '0');

                    $detail_lainnya[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'nominal' => $nominal,
                        'totalLainnya' => $totalLainnya,
                    ];
                }
            }

            // Save the details
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];


            $ca->detail_ca = json_encode($detail_ca);
            $ca->declare_ca = json_encode($detail_ca);
            $ca->save();

            if ($businessTripStatus !== 'Draft') {

                $model = $ca;

                $model->status_id = $managerL1;

                $cek_director_id = Employee::select([
                    'dsg.department_level2',
                    'dsg2.director_flag',
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1) AS department_director"),
                    'dsg2.designation_name',
                    'dsg2.job_code',
                    'emp.fullname',
                    'emp.employee_id',
                ])
                    ->leftJoin('designations as dsg', 'dsg.job_code', '=', 'employees.designation_code')
                    ->leftJoin('designations as dsg2', 'dsg2.department_code', '=', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(dsg.department_level2, '(', -1), ')', 1)"))
                    ->leftJoin('employees as emp', 'emp.designation_code', '=', 'dsg2.job_code')
                    ->where('employees.designation_code', '=', $employee->designation_code)
                    ->where('dsg2.director_flag', '=', 'F')
                    ->get();

                $director_id = "";

                if ($cek_director_id->isNotEmpty()) {
                    $director_id = $cek_director_id->first()->employee_id;
                }
                //cek matrix approval

                $total_ca = str_replace('.', '', $request->totalca);
                // dd($total_ca);
                // dd($employee->group_company);
                // dd($request->bb_perusahaan);
                $data_matrix_approvals = MatrixApproval::where('modul', 'dns')
                    ->where('group_company', 'like', '%' . $employee->group_company . '%')
                    ->where('contribution_level_code', 'like', '%' . $request->bb_perusahaan . '%')
                    ->whereRaw(
                        '
            ? BETWEEN
            CAST(SUBSTRING_INDEX(condt, "-", 1) AS UNSIGNED) AND
            CAST(SUBSTRING_INDEX(condt, "-", -1) AS UNSIGNED)',
                        [$total_ca]
                    )
                    ->get();
                foreach ($data_matrix_approvals as $data_matrix_approval) {

                    if ($data_matrix_approval->employee_id == "cek_L1") {
                        $employee_id = $managerL1;
                    } else if ($data_matrix_approval->employee_id == "cek_L2") {
                        $employee_id = $managerL2;
                    } else if ($data_matrix_approval->employee_id == "cek_director") {
                        $employee_id = $director_id;
                    } else {
                        $employee_id = $data_matrix_approval->employee_id;
                    }
                    // $uuid = Str::uuid();
                    $model_approval = new ca_approval;
                    $model_approval->ca_id = $ca_id;
                    $model_approval->role_name = $data_matrix_approval->desc;
                    $model_approval->employee_id = $employee_id;
                    $model_approval->layer = $data_matrix_approval->layer;
                    $model_approval->approval_status = 'Pending';

                    // Simpan data ke database
                    $model_approval->save();
                }
                $ca->save();
            }
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

        $sppd = BusinessTrip::where('status', '!=', 'Draft')->orderBy('created_at', 'desc')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // No sppd
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        // dd($tickets);
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $managerL1Names = Employee::whereIn('employee_id', $sppd->pluck('manager_l1_id'))->pluck('fullname', 'employee_id');
        $managerL2Names = Employee::whereIn('employee_id', $sppd->pluck('manager_l2_id'))->pluck('fullname', 'employee_id');

        $parentLink = 'Reimbursement';
        $link = 'Business Trip (Admin)';

        return view('hcis.reimbursements.businessTrip.btAdmin', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'managerL1Names', 'managerL2Names'));
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
    public function deklarasiAdmin($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        $ca = CATransaction::where('no_sppd', $n->no_sppd)->first();

        // Initialize caDetail with an empty array if it's null
        $caDetail = $ca ? json_decode($ca->detail_ca, true) : [];
        $declareCa = $ca ? json_decode($ca->declare_ca, true) : [];

        // Safely access nominalPerdiem with default '0' if caDetail is empty
        $nominalPerdiem = isset($caDetail['detail_perdiem'][0]['nominal']) ? $caDetail['detail_perdiem'][0]['nominal'] : '0';
        $nominalPerdiemDeclare = isset($declareCa['detail_perdiem'][0]['nominal']) ? $declareCa['detail_perdiem'][0]['nominal'] : '0';

        $hasCaData = $ca !== null;
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
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();

        return view('hcis.reimbursements.businessTrip.deklarasiAdmin', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
            'caDetail' => $caDetail,
            'declareCa' => $declareCa,
            'ca' => $ca,
            'nominalPerdiem' => $nominalPerdiem,
            'nominalPerdiemDeclare' => $nominalPerdiemDeclare,
            'hasCaData' => $hasCaData, // Pass the flag to the view
        ]);
    }
    public function deklarasiStatusAdmin(Request $request, $id)
    {
        $n = BusinessTrip::find($id);
        $companies = Company::orderBy('contribution_level')->get();
        // $ca = ca_transaction::find($id);

        $status = $request->input('accept_status');
        $n->status = $status;

        // $ca = $n->ca;
        // $ca->approval_status = $status;
        // $ca->save();

        $n->save();

        return redirect()->route('businessTrip.admin');
    }

    public function exportExcel(Request $request)
    {
        // Retrieve query parameters
        $startDate = $request->query('start-date');
        $endDate = $request->query('end-date');

        // Initialize query builders
        $query = BusinessTrip::query();
        $queryCA = CATransaction::query();

        // Apply filters if both dates are present
        if ($startDate && $endDate) {
            $query->whereBetween('mulai', [$startDate, $endDate]);
        }

        // Exclude drafts
        $query->where('status', '<>', 'draft'); // Adjust 'status' and 'draft' as needed
        $queryCA->where('approval_status', '<>', 'draft'); // Adjust 'status' and 'draft' as needed

        // Fetch the filtered BusinessTrip data
        $businessTrips = $query->get();

        // Extract the no_sppd values from the filtered BusinessTrip records
        $noSppds = $businessTrips->pluck('no_sppd')->unique();

        // Fetch CA data where no_sppd matches the filtered BusinessTrip records
        $caData = $queryCA->whereIn('no_sppd', $noSppds)->get();

        // Pass the filtered data to the export class
        return Excel::download(new BusinessTripExport($businessTrips, $caData), 'Data_Perjalanan_Dinas.xlsx');
    }

    public function approval(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'all');

        $query = BusinessTrip::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_l1_id', $user->employee_id)
                    ->whereIn('status', ['Pending L1', 'Declaration L1']);
            })->orWhere(function ($q) use ($user) {
                $q->where('manager_l2_id', $user->employee_id)
                    ->whereIn('status', ['Pending L2', 'Declaration L2']);
            });
        });

        if ($filter === 'request') {
            $query->where(function ($q) use ($user) {
                $q->where(function ($subQ) use ($user) {
                    $subQ->where('manager_l1_id', $user->employee_id)
                        ->where('status', 'Pending L1');
                })->orWhere(function ($subQ) use ($user) {
                    $subQ->where('manager_l2_id', $user->employee_id)
                        ->where('status', 'Pending L2');
                });
            });
        } elseif ($filter === 'declaration') {
            $query->where(function ($q) use ($user) {
                $q->where(function ($subQ) use ($user) {
                    $subQ->where('manager_l1_id', $user->employee_id)
                        ->where('status', 'Declaration L1');
                })->orWhere(function ($subQ) use ($user) {
                    $subQ->where('manager_l2_id', $user->employee_id)
                        ->where('status', 'Declaration L2');
                });
            });
        }

        $sppd = $query->orderBy('created_at', 'desc')->get();

        // Collect all SPPD numbers from the BusinessTrip instances
        $sppdNos = $sppd->pluck('no_sppd');

        // Retrieve related data based on the collected SPPD numbers
        $caTransactions = ca_transaction::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');
        $tickets = Tiket::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $hotel = Hotel::whereIn('no_sppd', $sppdNos)->get()->groupBy('no_sppd');
        $taksi = Taksi::whereIn('no_sppd', $sppdNos)->get()->keyBy('no_sppd');

        $parentLink = 'Approval';
        $link = 'Business Trip';

        return view('hcis.reimbursements.businessTrip.btApproval', compact('sppd', 'parentLink', 'link', 'caTransactions', 'tickets', 'hotel', 'taksi', 'filter'));
    }
    public function approvalDetail($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        // Retrieve the taxi data for the specific BusinessTrip
        $taksi = Taksi::where('no_sppd', $n->no_sppd)->first();
        $ca = CATransaction::where('no_sppd', $n->no_sppd)->first();
        // Initialize caDetail with an empty array if it's null
        $caDetail = $ca ? json_decode($ca->detail_ca, true) : [];

        // Safely access nominalPerdiem with default '0' if caDetail is empty
        $nominalPerdiem = isset($caDetail['detail_perdiem'][0]['nominal']) ? $caDetail['detail_perdiem'][0]['nominal'] : '0';


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
            'caDetail' => $caDetail,
            'ca' => $ca,
            'nominalPerdiem' => $nominalPerdiem,
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

            // Handle CA approval for L1
            if ($businessTrip->ca == 'Ya') {
                $caTransaction = CATransaction::where('no_sppd', $businessTrip->no_sppd)->first();
                if ($caTransaction) {
                    // Update CA approval status for L1
                    ca_approval::updateOrCreate(
                        ['ca_id' => $caTransaction->id, 'employee_id' => $employeeId, 'layer' => $layer],
                        ['approval_status' => 'Approved', 'approved_at' => now()]
                    );

                    // Find the next approver (Layer 2) from ca_approval
                    $nextApproval = ca_approval::where('ca_id', $caTransaction->id)
                        ->where('layer', $layer + 1)
                        ->first();

                    if ($nextApproval) {
                        $updateCa = CATransaction::where('id', $caTransaction->id)->first();
                        $updateCa->status_id = $nextApproval->employee_id;
                        $updateCa->save();
                    } else {
                        // No next layer, so mark as Approved
                        $caTransaction->update(['approval_status' => 'Approved']);
                    }
                }
            }
        } elseif ($employeeId == $businessTrip->manager_l2_id) {
            $statusValue = 'Approved';
            $layer = 2;

            // Handle CA approval for L2
            if ($businessTrip->ca == 'Ya') {
                $caTransaction = CATransaction::where('no_sppd', $businessTrip->no_sppd)->first();
                if ($caTransaction) {
                    // Update CA approval status for L2
                    ca_approval::updateOrCreate(
                        ['ca_id' => $caTransaction->id, 'employee_id' => $employeeId, 'layer' => $layer],
                        ['approval_status' => 'Approved', 'approved_at' => now()]
                    );

                    // Find the next approver (Layer 3) explicitly
                    $nextApproval = ca_approval::where('ca_id', $caTransaction->id)
                        ->where('layer', $layer + 1) // This will ensure it gets the immediate next layer (3)
                        ->first();

                    if ($nextApproval) {
                        $updateCa = CATransaction::where('id', $caTransaction->id)->first();
                        $updateCa->status_id = $nextApproval->employee_id;
                        $updateCa->save();
                    } else {
                        // No next layer, so mark as Approved
                        $caTransaction->update(['approval_status' => 'Approved']);
                    }
                }
            }
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
            ? 'The request has been successfully Rejected.'
            : 'The request has been successfully Accepted.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', $message);
    }

    public function updateStatusDeklarasi($id, Request $request)
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
            $statusValue = 'Declaration L2';
            $layer = 1;

            // Handle CA approval for L1
            if ($businessTrip->ca == 'Ya') {
                $caTransaction = CATransaction::where('no_sppd', $businessTrip->no_sppd)->first();
                if ($caTransaction) {
                    // Update CA approval status for L1
                    ca_sett_approval::updateOrCreate(
                        ['ca_id' => $caTransaction->id, 'employee_id' => $employeeId, 'layer' => $layer],
                        ['approval_status' => 'Approved', 'approved_at' => now()]
                    );

                    // Find the next approver (Layer 2) from ca_approval
                    $nextApproval = ca_sett_approval::where('ca_id', $caTransaction->id)
                        ->where('layer', $layer + 1)
                        ->first();

                    if ($nextApproval) {
                        $updateCa = CATransaction::where('id', $caTransaction->id)->first();
                        $updateCa->sett_id = $nextApproval->employee_id;
                        $updateCa->save();
                    } else {
                        // No next layer, so mark as Approved
                        $caTransaction->update(['approval_status' => 'Approved']);
                    }
                }
            }
        } elseif ($employeeId == $businessTrip->manager_l2_id) {
            $statusValue = 'Declaration Approved';
            $layer = 2;

            // Handle CA approval for L2
            if ($businessTrip->ca == 'Ya') {
                $caTransaction = CATransaction::where('no_sppd', $businessTrip->no_sppd)->first();
                if ($caTransaction) {
                    // Update CA approval status for L2
                    ca_sett_approval::updateOrCreate(
                        ['ca_id' => $caTransaction->id, 'employee_id' => $employeeId, 'layer' => $layer],
                        ['approval_status' => 'Approved', 'approved_at' => now()]
                    );

                    // Find the next approver (Layer 3) explicitly
                    $nextApproval = ca_sett_approval::where('ca_id', $caTransaction->id)
                        ->where('layer', $layer + 1) // This will ensure it gets the immediate next layer (3)
                        ->first();

                    if ($nextApproval) {
                        $updateCa = CATransaction::where('id', $caTransaction->id)->first();
                        $updateCa->sett_id = $nextApproval->employee_id;
                        $updateCa->save();
                    } else {
                        // No next layer, so mark as Approved
                        $caTransaction->update(['approval_sett' => 'Approved']);
                    }
                }
            }
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
            ? 'The request has been successfully Rejected.'
            : 'The request has been successfully Accepted.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', $message);
    }

    public function ApprovalDeklarasi($id)
    {
        $n = BusinessTrip::find($id);
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();

        $ca = CATransaction::where('no_sppd', $n->no_sppd)->first();

        // Initialize caDetail with an empty array if it's null
        $caDetail = $ca ? json_decode($ca->detail_ca, true) : [];
        $declareCa = $ca ? json_decode($ca->declare_ca, true) : [];

        // Safely access nominalPerdiem with default '0' if caDetail is empty
        $nominalPerdiem = isset($caDetail['detail_perdiem'][0]['nominal']) ? $caDetail['detail_perdiem'][0]['nominal'] : '0';
        $nominalPerdiemDeclare = isset($declareCa['detail_perdiem'][0]['nominal']) ? $declareCa['detail_perdiem'][0]['nominal'] : '0';

        $hasCaData = $ca !== null;
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
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < count($tickets) - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Retrieve locations and companies data for the dropdowns
        $locations = Location::orderBy('id')->get();
        $companies = Company::orderBy('contribution_level')->get();

        return view('hcis.reimbursements.businessTrip.btApprovalDeklarasi', [
            'n' => $n,
            'hotelData' => $hotelData,
            'taksiData' => $taksi, // Pass the taxi data
            'ticketData' => $ticketData,
            'employee_data' => $employee_data,
            'companies' => $companies,
            'locations' => $locations,
            'caDetail' => $caDetail,
            'declareCa' => $declareCa,
            'ca' => $ca,
            'nominalPerdiem' => $nominalPerdiem,
            'nominalPerdiemDeclare' => $nominalPerdiemDeclare,
            'hasCaData' => $hasCaData, // Pass the flag to the view
        ]);
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
