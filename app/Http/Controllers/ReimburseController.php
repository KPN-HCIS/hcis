<?php

namespace App\Http\Controllers;

use App\Models\BusinessTrip;
use App\Models\ca_transaction;
use App\Models\CAApproval;
use App\Models\Hotel;
use Exception;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Location;
use App\Models\Employee;
use App\Models\MatrixApproval;
use App\Models\ListPerdiem;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\CATransaction;
// use App\Http\Controllers\Log;
use App\Models\ca_approval;
use App\Models\htl_transaction;
use App\Models\Tiket;
use App\Models\TiketApproval;
use App\Models\HotelApproval;
use App\Models\tkt_transaction;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class ReimburseController extends Controller
{
    function reimbursements()
    {

        $userId = Auth::id();

        return view('hcis.reimbursements.dash', [
            'userId' => $userId,
        ]);
    }
    public function cashadvanced()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';
        $ca_transactions = CATransaction::with('employee')->where('user_id', $userId)->get();
        $pendingCACount = CATransaction::where('user_id', $userId)->where('approval_status', 'Pending')->count();

        // Memformat tanggal
        foreach ($ca_transactions as $transaction) {
            $transaction->formatted_start_date = Carbon::parse($transaction->start_date)->format('d-m-Y');
            $transaction->formatted_end_date = Carbon::parse($transaction->end_date)->format('d-m-Y');
        }

        return view('hcis.reimbursements.cashadv.cashadv', [
            'pendingCACount' => $pendingCACount,
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'ca_transactions' => $ca_transactions,
        ]);
    }
    function cashadvancedCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::orderBy('no_sppd')->get();

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
        $deptHeadManager = findDepartmentHead($employee_data);

        $managerL1 = $deptHeadManager->employee_id;
        $managerL2 = $deptHeadManager->manager_l1_id;

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
            ->where('employees.designation_code', '=', $employee_data->designation_code)
            ->where('dsg2.director_flag', '=', 'T')
            ->get();

        $director_id = "";

        if ($cek_director_id->isNotEmpty()) {
            $director_id = $cek_director_id->first()->employee_id;
        }

        return view('hcis.reimbursements.cashadv.formCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'managerL1' => $managerL1,
            'managerL2' => $managerL2,
            'director_id' => $director_id,
        ]);
    }
    public function cashadvancedSubmit(Request $req)
    {
        $userId = Auth::id();
        $employee_data = Employee::where('id', $userId)->first();
        $currentYear = date('Y');
        $currentYearShort = date('y'); // Mengambil 2 digit terakhir dari tahun
        $prefix = 'CA';

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = CATransaction::whereYear('created_at', $currentYear)
            ->orderBy('no_ca', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/CA' . $currentYearShort . '(\d{6})/', $lastTransaction->no_ca, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        $newNoCa = "$prefix$currentYearShort$newNumber";

        $uuid = Str::uuid();
        $model = new CATransaction;
        $model->id = $uuid;
        $model->type_ca = $req->ca_type;
        $model->no_ca = $newNoCa;
        $model->no_sppd = $req->bisnis_numb;
        $model->user_id = $userId;
        $model->unit = $req->unit;
        $model->contribution_level_code = $req->companyFilter;
        $model->destination = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs = $req->ca_needs;
        $model->start_date = $req->start_date;
        $model->end_date = $req->end_date;
        $model->date_required = $req->ca_required;
        $model->declare_estimate = $req->ca_decla;
        $model->total_days = $req->totaldays;
        if ($req->ca_type == 'dns') {
            // Menyiapkan array untuk menyimpan detail dari setiap bagian
            $detail_perdiem = [];
            $detail_transport = [];
            $detail_penginapan = [];
            $detail_lainnya = [];

            // Loop untuk Perdiem
            if ($req->has('start_bt_perdiem')) {
                // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);
                foreach ($req->start_bt_perdiem as $key => $startDate) {
                    $endDate = $req->end_bt_perdiem[$key];
                    $totalDays = $req->total_days_bt_perdiem[$key];
                    $location = $req->location_bt_perdiem[$key];
                    $other_location = $req->other_location_bt_perdiem[$key];
                    $companyCode = $req->company_bt_perdiem[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_perdiem[$key]);
                    // $totalPerdiem = str_replace('.', '', $req->total_bt_perdiem[]);

                    $for_perdiem[] = [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'total_days' => $totalDays,
                        'location' => $location,
                        'other_location' => $other_location,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Loop untuk Transport
            if ($req->has('tanggal_bt_transport')) {
                foreach ($req->tanggal_bt_transport as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_transport[$key];
                    $companyCode = $req->company_bt_transport[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_transport[$key]);

                    $detail_transport[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'company_code' => $companyCode,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Loop untuk Penginapan
            if ($req->has('start_bt_penginapan')) {
                foreach ($req->start_bt_penginapan as $key => $startDate) {
                    $endDate = $req->end_bt_penginapan[$key];
                    $totalDays = $req->total_days_bt_penginapan[$key];
                    $hotelName = $req->hotel_name_bt_penginapan[$key];
                    $companyCode = $req->company_bt_penginapan[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_penginapan[$key]);
                    $totalPenginapan = str_replace('.', '', $req->total_bt_penginapan[$key]);

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

            // Loop untuk Lainnya
            if ($req->has('tanggal_bt_lainnya')) {
                foreach ($req->tanggal_bt_lainnya as $key => $tanggal) {
                    $keterangan = $req->keterangan_bt_lainnya[$key];
                    $nominal = str_replace('.', '', $req->nominal_bt_lainnya[$key]);
                    $totalLainnya = str_replace('.', '', $req->total_bt_lainnya[$key]);

                    $detail_lainnya[] = [
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'nominal' => $nominal,
                        'totalLainnya' => $totalLainnya,
                    ];
                }
            }

            // Konversi array menjadi JSON untuk disimpan di database
            $detail_ca = [
                'detail_perdiem' => $detail_perdiem,
                'detail_transport' => $detail_transport,
                'detail_penginapan' => $detail_penginapan,
                'detail_lainnya' => $detail_lainnya,
            ];

            $model->detail_ca = json_encode($detail_ca);
        } else if ($req->ca_type == 'ndns') {
            // Menyiapkan array untuk menyimpan detail 'ndns'
            $detail_ndns = [];

            // Loop melalui setiap tanggal yang diberikan (dari input dinamis)
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    // Ambil keterangan, nominal, dan tanggal untuk setiap set input
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    // Tambahkan ke array detail_ndns
                    $detail_ndns[] = [
                        'tanggal_nbt' => $tanggal,
                        'keterangan_nbt' => $keterangan_nbt,
                        'nominal_nbt' => $nominal_nbt,
                    ];
                }
            }

            // Konversi array detail_ndns menjadi JSON untuk disimpan di database
            $detail_ndns_json = json_encode($detail_ndns);

            // Simpan data 'detail_ca' ke model
            $model->detail_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_e = [];
            $relation_e = [];

            // Mengumpulkan detail entertain
            if ($req->has('enter_type_e_detail')) {
                foreach ($req->enter_type_e_detail as $key => $type) {
                    $fee_detail = $req->enter_fee_e_detail[$key];
                    $nominal = str_replace('.', '', $req->nominal_e_detail[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    $detail_e[] = [
                        'type' => $type,
                        'fee_detail' => $fee_detail,
                        'nominal' => $nominal,
                    ];
                }
            }

            // Mengumpulkan detail relation
            if ($req->has('rname_e_relation')) {
                foreach ($req->rname_e_relation as $key => $name) {
                    $relation_e[] = [
                        'name' => $name,
                        'position' => $req->rposition_e_relation[$key],
                        'company' => $req->rcompany_e_relation[$key],
                        'purpose' => $req->rpurpose_e_relation[$key],
                        'relation_type' => array_filter([
                            'Food/Beverages/Souvenir' => in_array('food_cost', $req->food_cost_e_relation ?? []),
                            'Transport' => in_array('transport', $req->transport_e_relation ?? []),
                            'Accommodation' => in_array('accommodation', $req->accommodation_e_relation ?? []),
                            'Gift' => in_array('gift', $req->gift_e_relation ?? []),
                            'Fund' => in_array('fund', $req->fund_e_relation ?? []),
                        ], fn($checked) => $checked),
                    ];
                }
            }

            // Gabungkan detail entertain dan relation, lalu masukkan ke detail_ca
            $detail_ca = [
                'detail_e' => $detail_e,
                'relation_e' => $relation_e,
            ];
            $model->detail_ca = json_encode($detail_ca);
        }

        $model->total_ca = str_replace('.', '', $req->totalca);
        $model->total_real = "0";
        $model->total_cost = str_replace('.', '', $req->totalca);

        if ($req->input('action_ca_draft')) {
            $model->approval_status = $req->input('action_ca_draft');
        }
        if ($req->input('action_ca_submit')) {
            $model->approval_status = $req->input('action_ca_submit');
        }

        $model->created_by = $userId;
        $model->save();

        if ($req->input('action_ca_submit')) {
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
            $deptHeadManager = findDepartmentHead($employee_data);

            $managerL1 = $deptHeadManager->employee_id;
            $managerL2 = $deptHeadManager->manager_l1_id;

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
                ->where('employees.designation_code', '=', $employee_data->designation_code)
                ->where('dsg2.director_flag', '=', 'T')
                ->get();

            $director_id = "";

            if ($cek_director_id->isNotEmpty()) {
                $director_id = $cek_director_id->first()->employee_id;
            }
            //cek matrix approval
            $total_ca = str_replace('.', '', $req->totalca);
            $data_matrix_approvals = MatrixApproval::where(function ($query) use ($req) {
                if ($req->ca_type === 'dns') {
                    $query->where('modul', 'dns');
                } else {
                    $query->where('modul', 'like', '%' . $req->ca_type . '%');
                }
            })
                ->where('group_company', 'like', '%' . $employee_data->group_company . '%')
                ->where('contribution_level_code', 'like', '%' . $req->companyFilter . '%')
                ->whereRaw('
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

                $model = new ca_approval;
                $model->ca_id = $uuid;
                $model->role_name = $data_matrix_approval->desc;
                $model->employee_id = $employee_id;
                $model->layer = $data_matrix_approval->layer;
                $model->approval_status = 'Pending';

                // Simpan data ke database
                $model->save();
            }
        }

        Alert::success('Success');
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    function cashadvancedEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::find($key);

        return view('hcis.reimbursements.cashadv.editCashadv', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);
    }
    function cashadvancedUpdate(Request $req, $key)
    {
        $userId = Auth::id();
        $model = ca_transaction::find($key);
        $model->type_ca = $req->ca_type;
        $model->no_ca = $req->no_ca;
        $model->no_sppd = $req->bisnis_numb;
        // $model->user_id         = $req->id;
        $model->unit = $req->unit;
        $model->contribution_level_code = $req->companyFilter;
        $model->destination = $req->locationFilter;
        $model->others_location = $req->others_location;
        $model->ca_needs = $req->ca_needs;
        $model->start_date = $req->start_date;
        $model->end_date = $req->end_date;
        $model->date_required = $req->ca_required;
        $model->declare_estimate = $req->ca_decla;
        $model->total_days = $req->totaldays;
        if ($req->ca_type == 'dns') {
            $detail_ca = [
                'allowance' => $req->allowance,
                'transport' => $req->transport,
                'accommodation' => $req->accommodation,
                'other' => $req->other,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        } else if ($req->ca_type == 'ndns') {
            // Menyiapkan array untuk menyimpan detail 'ndns'
            $detail_ndns = [];

            // Loop melalui setiap tanggal yang diberikan (dari input dinamis)
            if ($req->has('tanggal_nbt')) {
                foreach ($req->tanggal_nbt as $key => $tanggal) {
                    // Ambil keterangan, nominal, dan tanggal untuk setiap set input
                    $keterangan_nbt = $req->keterangan_nbt[$key];
                    $nominal_nbt = str_replace('.', '', $req->nominal_nbt[$key]); // Menghapus titik dari nominal sebelum menyimpannya

                    // Tambahkan ke array detail_ndns
                    $detail_ndns[] = [
                        'tanggal_nbt' => $tanggal,
                        'keterangan_nbt' => $keterangan_nbt,
                        'nominal_nbt' => $nominal_nbt,
                    ];
                }
            }

            // Konversi array detail_ndns menjadi JSON untuk disimpan di database
            $detail_ndns_json = json_encode($detail_ndns);

            // Simpan data 'detail_ca' ke model
            $model->detail_ca = $detail_ndns_json;
        } else if ($req->ca_type == 'entr') {
            $detail_ca = [
                'enter_type_1' => $req->enter_type_1,
                'enter_fee_1' => $req->enter_fee_1,
                'nominal_1' => $req->nominal_1,
                'enter_type_2' => $req->enter_type_2,
                'enter_fee_2' => $req->enter_fee_2,
                'nominal_2' => $req->nominal_2,
                'enter_type_3' => $req->enter_type_3,
                'enter_fee_3' => $req->enter_fee_3,
                'nominal_3' => $req->nominal_3,
                'enter_type_4' => $req->enter_type_4,
                'enter_fee_4' => $req->enter_fee_4,
                'nominal_4' => $req->nominal_4,
                'enter_type_5' => $req->enter_type_5,
                'enter_fee_5' => $req->enter_fee_5,
                'nominal_5' => $req->nominal_5,
                'rname_1' => $req->rname_1,
                'rposition_1' => $req->rposition_1,
                'rcompany_1' => $req->rcompany_1,
                'rpurpose_1' => $req->rpurpose_1,
                'rname_2' => $req->rname_2,
                'rposition_2' => $req->rposition_2,
                'rcompany_2' => $req->rcompany_2,
                'rpurpose_2' => $req->rpurpose_2,
                'rname_3' => $req->rname_3,
                'rposition_3' => $req->rposition_3,
                'rcompany_3' => $req->rcompany_3,
                'rpurpose_3' => $req->rpurpose_3,
                'rname_4' => $req->rname_4,
                'rposition_4' => $req->rposition_4,
                'rcompany_4' => $req->rcompany_4,
                'rpurpose_4' => $req->rpurpose_4,
                'rname_5' => $req->rname_5,
                'rposition_5' => $req->rposition_5,
                'rcompany_5' => $req->rcompany_5,
                'rpurpose_5' => $req->rpurpose_5,
            ];
            $detail_ca_json = json_encode($detail_ca);
            $model->detail_ca = $detail_ca_json;
        }
        $model->total_ca = str_replace('.', '', $req->totalca);
        $model->total_real = "0";
        $model->total_cost = str_replace('.', '', $req->totalca);
        $model->approval_status = "Pending";
        $model->created_by = $userId;
        $model->save();

        Alert::success('Success Update');
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    function cashadvancedDelete($id)
    {
        $model = ca_transaction::find($id);
        $model->delete();
        return redirect()->intended(route('cashadvanced', absolute: false));
    }
    function cashadvancedDownload($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Cash Advanced';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        // $kantor = Company::where('contribution_level', $companies->contribution_level_code)->first();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = CATransaction::find($key);

        // return view('hcis.reimbursements.cashadv.downloadCashadv', [
        $pdf = PDF::loadView('hcis.reimbursements.cashadv.printCashadv', [
            'link' => $link,
            // 'pdf' => $pdf,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
        ]);

        return $pdf->stream('Cash Advanced ' . $key . '.pdf');
    }

    public function hotel(Request $request)
    {
        $userId = Auth::user();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        $query = Hotel::where('user_id', $userId->id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        $filter = $request->input('filter', 'request');

        // Apply filter to the query
        if ($filter === 'request') {
            $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        } elseif ($filter === 'rejected') {
            $statusFilter = ['Rejected'];
        }

        // Apply status filter to the query
        $query->whereIn('approval_status', $statusFilter);

        // Log::info('Filtered Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get the filtered tickets
        $hotelFilter = $query->get();

        // Fetch latest hotel entries grouped by 'no_htl'
        $latestHotelIds = Hotel::selectRaw('MAX(id) as id')
            ->where('user_id', $userId->id)
            ->groupBy('no_htl')
            ->pluck('id');

        // Fetch the hotel transactions using the latest ids
        $transactions = Hotel::whereIn('id', $latestHotelIds)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->whereIn('approval_status', $statusFilter)
            ->select('id', 'no_htl', 'nama_htl', 'lokasi_htl', 'approval_status', 'user_id', 'no_sppd')
            ->get();

        // Fetch all hotel transactions of the user
        $hotels = Hotel::where('user_id', $userId->id)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get();

        $hotel = Hotel::where('user_id', $userId->id)
            ->with('employee', 'hotelApproval')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_htl');

        $hotelIds = $hotels->pluck('id');

        // Fetch hotel approval details using the hotel IDs
        $hotelApprovals = HotelApproval::whereIn('htl_id', $hotelIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get();
        Log::info('Hotel Approvals:', $hotelApprovals->toArray());

        $hotelApprovals = $hotelApprovals->keyBy('htl_id');
        // dd($hotelApprovals);

        // Group transactions by hotel number
        $hotelGroups = $hotels->groupBy('no_htl');

        // Fetch employee data
        $employeeIds = $hotels->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Fetch manager IDs from the employees data
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();

        // Fetch manager names
        $managerL1Names = Employee::whereIn('employee_id', $managerL1Ids)->pluck('fullname');
        $managerL2Names = Employee::whereIn('employee_id', $managerL2Ids)->pluck('fullname');

        // Count grouped hotel entries
        $hotelCounts = $hotels->groupBy('no_htl')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.hotel.hotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
            'hotelCounts' => $hotelCounts,
            'hotels' => $hotels,
            'hotel' => $hotel,
            'hotelGroups' => $hotelGroups,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
            'hotelApprovals' => $hotelApprovals,
            'employeeName' => $employeeName,
            'filter' => $filter,
        ]);
    }

    function hotelCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        // $no_sppds = ca_transaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();


        return view('hcis.reimbursements.hotel.formHotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
        ]);
    }

    private function generateNoSppdHtl()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = $this->getRomanMonth($currentMonth);

        // Get the last transaction for the current year, including deleted ones
        $lastTransaction = Hotel::whereYear('created_at', $currentYear)
            ->orderBy('no_htl', 'desc')
            ->withTrashed()
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/HTLD-HRD\/([IVX]+)\/\d{4}/', $lastTransaction->no_htl, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoSppd = "$newNumber/HTLD-HRD/$romanMonth/$currentYear";
        // dd($newNoSppd);

        return $newNoSppd;
    }

    public function hotelSubmit(Request $req)
    {
        $userId = Auth::id();
        $noSppdHtl = $this->generateNoSppdHtl();

        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }
        // Prepare the hotel data arrays
        $hotelData = [
            'nama_htl' => $req->nama_htl,
            'lokasi_htl' => $req->lokasi_htl,
            'jmlkmr_htl' => $req->jmlkmr_htl,
            'bed_htl' => $req->bed_htl,
            'tgl_masuk_htl' => $req->tgl_masuk_htl,
            'tgl_keluar_htl' => $req->tgl_keluar_htl,
            'total_hari' => $req->total_hari,
            'approval_status' => $statusValue,
        ];

        foreach ($hotelData['nama_htl'] as $key => $value) {
            // Only process if required fields are filled
            if (!empty($hotelData['nama_htl'][$key]) && !empty($hotelData['lokasi_htl'][$key]) && !empty($hotelData['tgl_masuk_htl'][$key])) {
                $model = new Hotel();
                $model->id = (string) Str::uuid();
                $model->no_htl = $noSppdHtl; // Use the pre-generated hotel number
                $model->no_sppd = $req->bisnis_numb;
                $model->user_id = $userId;
                $model->unit = $req->unit;
                $model->nama_htl = $hotelData['nama_htl'][$key];
                $model->lokasi_htl = $hotelData['lokasi_htl'][$key];
                $model->jmlkmr_htl = $hotelData['jmlkmr_htl'][$key] ?? null;
                $model->bed_htl = $hotelData['bed_htl'][$key] ?? null;
                $model->tgl_masuk_htl = $hotelData['tgl_masuk_htl'][$key] ?? null;
                $model->tgl_keluar_htl = $hotelData['tgl_keluar_htl'][$key] ?? null;
                $model->total_hari = $hotelData['total_hari'][$key] ?? null;
                $model->created_by = $userId;
                $model->approval_status = $statusValue;
                $model->hotel_only = 'Y';
                $model->created_by = $userId;
                // dd($statusValue);
                $model->save();
            }
        }

        // Update BusinessTrip record if applicable
        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $model->approval_status == 'Pending L1') {
            // Update the 'hotel' field to 'Ya'
            $bt->hotel = 'Ya';
            $bt->save();
        }
        return redirect('/hotel')->with('success', 'Hotel request input successfully');
    }

    public function hotelEdit($key)
    {
        $userId = Auth::id();

        // Define links for navigation
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        // Fetch the specific hotel transaction by key
        $hotel = Hotel::findByRouteKey($key);

        // Check if the hotel transaction exists, if not redirect with an error message
        if (!$hotel) {
            return redirect()->route('hotel')->with('error', 'Hotel transaction not found');
        }

        // Fetch all hotel transactions associated with the same no_sppd for reference
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();

        // Prepare data for multiple forms
        $hotelData = [];
        $hotelCount = $hotels->count();
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'id' => $hotel->id, // Include ID for updating
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < $hotelCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.hotel.editHotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $hotels,
            'hotel' => $hotel,
            'hotelData' => $hotelData,
        ]);
    }


    public function hotelUpdate(Request $req, $key)
    {
        $hotelIds = $req->input('hotel_ids', []); // Get the array of existing hotel IDs
        $existingHotels = Hotel::whereIn('id', $hotelIds)->get()->keyBy('id'); // Load existing hotels into a collection

        $processedHotelIds = [];
        $updateBusinessTrip = false;

        // Determine approval status based on the action
        if ($req->has('action_draft')) {
            $statusValue = 'Draft';  // When "Save as Draft" is clicked
        } elseif ($req->has('action_submit')) {
            $statusValue = 'Pending L1';  // When "Submit" is clicked
        }

        // Get the no_htl from the first existing hotel record
        $existingNoHtl = $existingHotels->first()->no_htl ?? null;
        // dd($existingNoHtl);

        // Loop through hotel data
        foreach ($req->nama_htl as $index => $value) {
            if (!empty($value)) {
                // Get hotel ID for this index
                $hotelId = $req->hotel_ids[$index] ?? null;

                // Prepare hotel data array
                $hotelData = [
                    'unit' => $req->unit,
                    'no_sppd' => $req->bisnis_numb,
                    'nama_htl' => $req->nama_htl[$index],
                    'lokasi_htl' => $req->lokasi_htl[$index],
                    'jmlkmr_htl' => $req->jmlkmr_htl[$index],
                    'bed_htl' => $req->bed_htl[$index],
                    'tgl_masuk_htl' => $req->tgl_masuk_htl[$index],
                    'tgl_keluar_htl' => $req->tgl_keluar_htl[$index],
                    'total_hari' => $req->total_hari[$index],
                    'approval_status' => $statusValue,
                    'jns_dinas_htl' => $req->jns_dinas_htl,
                ];

                // Check if hotel ID exists to decide if it's an update or a new entry
                if ($hotelId && isset($existingHotels[$hotelId])) {
                    $existingHotel = $existingHotels[$hotelId];
                    $hotelData['user_id'] = $existingHotel->user_id;

                    // Check if status is changing to "Pending L1"
                    if ($existingHotel->approval_status != 'Pending L1' && $hotelData['approval_status'] == 'Pending L1') {
                        $updateBusinessTrip = true;
                    }

                    // Update existing hotel record
                    $existingHotel->update($hotelData);
                    $processedHotelIds[] = $hotelId; // Keep track of processed hotel IDs
                } else {
                    // If hotel ID doesn't exist, create a new hotel record
                    // Use the existing no_htl from the first hotel
                    $newHotel = Hotel::create(array_merge($hotelData, [
                        'id' => (string) Str::uuid(), // Auto-generated UUID
                        'no_htl' => $existingNoHtl, // Use the existing no_htl
                        'user_id' => Auth::id(),
                        'created_by' => Auth::id(),
                        'hotel_only' => 'Y',
                    ]));

                    // dd($newHotel);
                    $processedHotelIds[] = $newHotel->id; // Keep track of processed hotel IDs

                    // Check if the new hotel is created with "Pending L1" status
                    if ($hotelData['approval_status'] == 'Pending L1') {
                        $updateBusinessTrip = true;
                    }
                }
            }
        }

        // Update BusinessTrip if status changed to "Pending L1" for any hotel
        if ($updateBusinessTrip) {
            $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
            if ($bt) {
                $bt->hotel = 'Ya';
                $bt->save();
            }
        }
        // dd([$hotelIds, $processedHotelIds]);
        // Delete hotels with the same no_htl but not in the processedHotelIds
        Hotel::where('no_htl', $existingNoHtl)
            ->whereNotIn('id', $processedHotelIds)
            ->delete();

        // Show success or warning message
        // if (count($processedHotelIds) > 0) {
        //     Alert::success('Success', "Hotels updated successfully");
        // } else {
        //     Alert::warning('Warning', "No hotels were updated.");
        // }

        return redirect('/hotel')->with('success', 'Hotel request updated successfully');
    }



    public function hotelDelete($key)
    {
        // Find the hotel record by its primary key
        $model = Hotel::findByRouteKey($key);

        if ($model) {
            // Retrieve the no_htl value of the hotel to delete all hotels with the same no_htl
            $noHtl = $model->no_htl;

            // Delete all hotels with the same no_htl
            Hotel::where('no_htl', $noHtl)->delete();
        }

        // Redirect after deletion
        return redirect()->intended(route('hotel', absolute: false))->with('success', 'All related hotels deleted successfully');
    }


    public function hotelExport($id)
    {
        // Find the hotel by ID
        $hotel = Hotel::findOrFail($id);

        // Retrieve all hotels with the same `no_htl`
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();

        // Prepare the data to be passed to the PDF view
        $data = [
            'hotel' => $hotel,
            'hotels' => $hotels
        ];

        // Load the view and pass the data
        $pdf = PDF::loadView('hcis.reimbursements.hotel.hotel_pdf', $data);

        // Stream the generated PDF to the browser, opening in a new tab
        return $pdf->stream('Hotel.pdf');
    }



    public function hotelApproval()
    {
        $user = Auth::user();
        $userId = $user->id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record

        $parentLink = 'Reimbursement';
        $link = 'Hotel Approval';

        // Get unique ticket numbers with conditions
        $hotelNumbers = Hotel::where('hotel_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_htl')->unique();
        // dd($ticketNumbers);

        // Fetch all tickets using the latestTicketIds
        $transactions = Hotel::whereIn('no_htl', $hotelNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($transactions);

        // Filter tickets based on manager and approval status
        $hotels = $transactions->filter(function ($hotel) use ($employee) {
            // Get the employee who owns the ticket
            $ticketOwnerEmployee = Employee::where('id', $hotel->user_id)->first();

            if ($hotel->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) {
                return true;
            } elseif ($hotel->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id) {
                return true;
            }
            return false;
        });

        // Group tickets by `no_tkt` and get the first entry for each group
        $hotelGroups = $hotels->groupBy('no_htl')->map(function ($group) {
            return $group->first();
        });

        // dd($tickets);

        // Group tickets by `no_tkt`
        $hotel = $hotels->groupBy('no_htl');

        // Fetch employee data for ticket owners
        $employeeIds = $hotels->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

        // Fetch manager names for display (optional, depending on your view)
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();
        $managerL1Names = Employee::whereIn('id', $managerL1Ids)->pluck('fullname', 'id');
        $managerL2Names = Employee::whereIn('id', $managerL2Ids)->pluck('fullname', 'id');

        // Count tickets per `no_tkt`
        $hotelCounts = $hotels->groupBy('no_htl')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.hotel.hotelApproval', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $hotelGroups,
            'hotelCounts' => $hotelCounts,
            'hotels' => $hotels,
            'hotel' => $hotel,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
        ]);
    }

    public function hotelApprovalDetail($key)
    {
        // Define links for navigation
        $parentLink = 'Reimbursement';
        $link = 'Hotel Approval';

        $hotel = Hotel::findByRouteKey($key);

        // Check if the ticket exists, if not redirect with an error message
        if (!$hotel) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $hotels = Hotel::where('no_htl', $hotel->no_htl)->get();
        $userId = Hotel::where('no_htl', $hotel->no_htl)->pluck('user_id')->first();
        // dd($userId);

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        // dd($employee_data);
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();

        $hotelOwnerEmployee = Employee::where('id', $hotel->user_id)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $hotels;

        $hotelData = [];
        $hotelCount = $hotels->count();
        foreach ($hotels as $index => $hotel) {
            $hotelData[] = [
                'id' => $hotel->id, // Include ID for updating
                'no_htl' => $hotel->no_htl,
                'nama_htl' => $hotel->nama_htl,
                'lokasi_htl' => $hotel->lokasi_htl,
                'jmlkmr_htl' => $hotel->jmlkmr_htl,
                'bed_htl' => $hotel->bed_htl,
                'tgl_masuk_htl' => $hotel->tgl_masuk_htl,
                'tgl_keluar_htl' => $hotel->tgl_keluar_htl,
                'total_hari' => $hotel->total_hari,
                'more_htl' => ($index < $hotelCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.hotel.hotelApprovalDetail', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'hotel' => $hotel,
            'hotelData' => $hotelData,
            'hotelOwnerEmployee' => $hotelOwnerEmployee,
            'hotelCount' => $hotelCount,
        ]);
    }

    public function updateStatusHotel($id, Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Find the hotel by ID
        $hotel = Hotel::findOrFail($id);
        $noHtl = $hotel->no_htl;

        // Check the provided status_approval input
        $statusApproval = $request->input('status_approval');

        // Handle rejection scenario
        if ($statusApproval == 'Rejected') {
            $rejectInfo = $request->input('reject_info');

            // Get the current approval status before updating it
            $currentApprovalStatus = $hotel->approval_status;

            // Update all hotels with the same no_htl to 'Rejected'
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Rejected']);

            // Log the rejection into the hotel_approvals table for all hotels with the same no_htl
            $hotels = Hotel::where('no_htl', $noHtl)->get();
            foreach ($hotels as $hotel) {
                $rejection = new HotelApproval();
                $rejection->id = (string) Str::uuid();
                $rejection->htl_id = $hotel->id;
                $rejection->employee_id = $employeeId;

                // Determine the correct layer based on the hotel's approval status BEFORE rejection
                $rejection->layer = $currentApprovalStatus == 'Pending L2' ? 2 : 1;

                $rejection->approval_status = 'Rejected';
                $rejection->approved_at = now();
                $rejection->reject_info = $rejectInfo;
                $rejection->save();
            }

            return redirect('/hotel/approval')->with('success', 'Request approved successfully');
        }

        // Handle approval scenarios
        if ($hotel->approval_status == 'Pending L1') {
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Pending L2']);
        } elseif ($hotel->approval_status == 'Pending L2') {
            Hotel::where('no_htl', $noHtl)->update(['approval_status' => 'Approved']);
        } else {
            return redirect()->back()->with('error', 'Invalid status update.');
        }

        // Log the approval into the hotel_approvals table for all hotels with the same no_htl
        $hotels = Hotel::where('no_htl', $noHtl)->get();
        foreach ($hotels as $hotel) {
            $approval = new HotelApproval();
            $approval->id = (string) Str::uuid();
            $approval->htl_id = $hotel->id;
            $approval->employee_id = $employeeId;
            $approval->layer = $hotel->approval_status == 'Pending L2' ? 1 : 2;
            $approval->approval_status = $hotel->approval_status;
            $approval->approved_at = now();
            $approval->save();
        }

        // Redirect to the hotel approval page
        return redirect('/hotel/approval')->with('success', 'Request approved successfully');
    }


    public function ticket(Request $request)
    {
        $userId = Auth::user();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        // Base query for filtering
        $query = Tiket::where('user_id', $userId->id)->orderBy('created_at', 'desc');

        // Get the filter value, default to 'request' if not provided
        $filter = $request->input('filter', 'request');

        // Apply filter to the query
        if ($filter === 'request') {
            $statusFilter = ['Pending L1', 'Pending L2', 'Approved', 'Draft'];
        } elseif ($filter === 'rejected') {
            $statusFilter = ['Rejected'];
        }

        // Apply status filter to the query
        $query->whereIn('approval_status', $statusFilter);

        // Log::info('Filtered Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Get the filtered tickets
        $ticketsFilter = $query->get();

        // Fetch latest ticket IDs
        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->where('user_id', $userId->id)
            ->groupBy('no_tkt')
            ->pluck('id');

        // Get transactions with the latest ticket IDs
        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->with('businessTrip')
            ->whereIn('approval_status', $statusFilter) // Apply the same filter to transactions
            ->orderBy('created_at', 'desc')
            ->select('id', 'no_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();
        // Get all tickets for user
        $tickets = Tiket::where('user_id', $userId->id)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group tickets by 'no_tkt'
        $ticket = $tickets->groupBy('no_tkt');

        // Get ticket IDs
        $tiketIds = $tickets->pluck('id');

        // Get ticket approvals
        $ticketApprovals = TiketApproval::whereIn('tkt_id', $tiketIds)
            ->where(function ($query) {
                $query->where('approval_status', 'Rejected')
                    ->orWhere('approval_status', 'Declaration Rejected');
            })
            ->get();

        // Log ticket approvals
        Log::info('Ticket Approvals:', $ticketApprovals->toArray());

        // Key ticket approvals by ticket ID
        $ticketApprovals = $ticketApprovals->keyBy('tkt_id');

        // Group transactions by 'no_tkt'
        $ticketsGroups = $tickets->groupBy('no_tkt');

        // Fetch employee data
        $employeeIds = $tickets->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $employeeName = Employee::pluck('fullname', 'employee_id');

        // Fetch manager IDs from employee data
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();

        // Fetch manager names
        $managerL1Names = Employee::whereIn('employee_id', $managerL1Ids)->pluck('fullname');
        $managerL2Names = Employee::whereIn('employee_id', $managerL2Ids)->pluck('fullname');

        // Count tickets grouped by 'no_tkt'
        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        // Return the view with all the data
        return view('hcis.reimbursements.ticket.ticket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'ticketsGroups' => $ticketsGroups,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
            'ticketApprovals' => $ticketApprovals,
            'employeeName' => $employeeName,
            'filter' => $filter,
            'ticketsFilter' => $ticketsFilter,
        ]);
    }


    public function ticketCreate()
    {

        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        // $no_sppds = ca_transaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();


        return view('hcis.reimbursements.ticket.formTicket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
        ]);
    }
    public function ticketSubmit(Request $req)
    {
        function getRomanMonth_tkt($month)
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

        function generateTicketNumber($type)
        {
            $userId = Auth::id();
            $currentYear = date('Y');
            $currentMonth = date('n');
            $romanMonth = getRomanMonth_tkt($currentMonth);

            // Determine the prefix based on the type
            $prefix = '';
            if ($type === 'Dinas') {
                $prefix = 'TKTD-HRD';
            } elseif ($type === 'Cuti') {
                $prefix = 'TKTC-HRD';
            } else {
                throw new Exception('Invalid ticket type');
            }

            // Get the last transaction of the current year and month for the specific type
            $lastTransaction = Tiket::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('no_tkt', 'like', "%/$prefix/$romanMonth/$currentYear")
                ->orderBy('no_tkt', 'desc')
                ->first();

            // Determine the new ticket number
            if ($lastTransaction && preg_match('/(\d{3})\/' . $prefix . '\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_tkt, $matches)) {
                $lastNumber = intval($matches[1]);
            } else {
                $lastNumber = 0;
            }

            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $newNoTkt = "$newNumber/$prefix/$romanMonth/$currentYear";

            return $newNoTkt;
        }

        // Determine the ticket type from the request
        $ticketType = $req->jns_dinas_tkt == 'Dinas' ? 'Dinas' : 'Cuti';
        // Generate the ticket number for the entire submission
        $newNoTkt = generateTicketNumber($ticketType);

        // Prepare the ticket data arrays
        $ticketData = [
            'noktp_tkt' => $req->noktp_tkt,
            'tlp_tkt' => $req->tlp_tkt,
            'jk_tkt' => $req->jk_tkt,
            'np_tkt' => $req->np_tkt,
            'dari_tkt' => $req->dari_tkt,
            'ke_tkt' => $req->ke_tkt,
            'tgl_brkt_tkt' => $req->tgl_brkt_tkt,
            'tgl_plg_tkt' => $req->tgl_plg_tkt,
            'jam_brkt_tkt' => $req->jam_brkt_tkt,
            'jam_plg_tkt' => $req->jam_plg_tkt,
            'jenis_tkt' => $req->jenis_tkt,
            'type_tkt' => $req->type_tkt,
            'ket_tkt' => $req->ket_tkt,
            'approval_status' => $req->status,
        ];

        foreach ($ticketData['noktp_tkt'] as $key => $value) {
            // Only process if the required fields are filled
            if (!empty($ticketData['noktp_tkt'][$key]) && !empty($ticketData['jk_tkt'][$key]) && !empty($ticketData['np_tkt'][$key])) {
                $tiket = new Tiket();
                $tiket->id = (string) Str::uuid();

                // Use the pre-generated ticket number
                $tiket->no_tkt = $newNoTkt;

                $userId = Auth::id();
                $tiket->no_sppd = $req->bisnis_numb;
                $tiket->user_id = $userId;
                $tiket->unit = $req->unit;
                $tiket->jk_tkt = $ticketData['jk_tkt'][$key];
                $tiket->np_tkt = $ticketData['np_tkt'][$key];
                $tiket->noktp_tkt = $ticketData['noktp_tkt'][$key];
                $tiket->tlp_tkt = $ticketData['tlp_tkt'][$key];
                $tiket->dari_tkt = $ticketData['dari_tkt'][$key] ?? null;
                $tiket->ke_tkt = $ticketData['ke_tkt'][$key] ?? null;
                $tiket->tgl_brkt_tkt = $ticketData['tgl_brkt_tkt'][$key] ?? null;
                $tiket->tgl_plg_tkt = $ticketData['tgl_plg_tkt'][$key] ?? null;
                $tiket->jam_brkt_tkt = $ticketData['jam_brkt_tkt'][$key] ?? null;
                $tiket->jam_plg_tkt = $ticketData['jam_plg_tkt'][$key] ?? null;
                $tiket->jenis_tkt = $ticketData['jenis_tkt'][$key] ?? null;
                $tiket->type_tkt = $ticketData['type_tkt'][$key] ?? null;
                $tiket->ket_tkt = $ticketData['ket_tkt'][$key] ?? null;
                $tiket->approval_status = $req->status;
                $tiket->jns_dinas_tkt = $req->jns_dinas_tkt;
                $tiket->tkt_only = 'Y';
                // dd($req->all());
                $tiket->save();
            }
        }

        // Update BusinessTrip record
        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $tiket->approval_status == 'Pending L1') {
            $bt->tiket = 'Ya';
            $bt->save();
        }

        Alert::success('Success');
        session()->flash('message', 'Berhasil di Tambahkan');
        return redirect()->route('ticket');
    }


    public function ticketEdit($key)
    {
        $userId = Auth::id();

        // Define links for navigation
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $ticket = Tiket::findByRouteKey($key);

        // Check if the ticket exists, if not redirect with an error message
        if (!$ticket) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $tickets;

        $ticketData = [];
        $ticketCount = $tickets->count();
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'noktp_tkt' => $ticket->noktp_tkt,
                'tlp_tkt' => $ticket->tlp_tkt,
                'jk_tkt' => $ticket->jk_tkt,
                'np_tkt' => $ticket->np_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < $ticketCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.ticket.editTicket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'ticket' => $ticket,
            'ticketData' => $ticketData,
        ]);
    }

    public function ticketUpdate(Request $req)
    {
        function getRomanMonth_tkt($month)
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

        function generateTicketNumber($type)
        {
            $currentYear = date('Y');
            $currentMonth = date('n');
            $romanMonth = getRomanMonth_tkt($currentMonth);

            $prefix = ($type === 'Dinas') ? 'TKTD-HRD' : (($type === 'Cuti') ? 'TKTC-HRD' : throw new Exception('Invalid ticket type'));

            $lastTransaction = Tiket::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('no_tkt', 'like', "%/$prefix/$romanMonth/$currentYear")
                ->orderBy('no_tkt', 'desc')
                ->first();

            $lastNumber = $lastTransaction && preg_match('/(\d{3})\/' . $prefix . '\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_tkt, $matches)
                ? intval($matches[1]) : 0;

            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            return "$newNumber/$prefix/$romanMonth/$currentYear";
        }

        $ticketType = $req->jns_dinas_tkt == 'Dinas' ? 'Dinas' : 'Cuti';

        // Get all existing tickets for this business trip
        $existingTickets = Tiket::where('no_sppd', $req->bisnis_numb)->get()->keyBy('noktp_tkt');

        $processedTicketIds = [];
        $newNoTkt = null;

        foreach ($req->noktp_tkt as $key => $value) {
            if (!empty($value)) {
                // Prepare ticket data
                $ticketData = [
                    'no_sppd' => $req->bisnis_numb,
                    'user_id' => Auth::id(),
                    'unit' => $req->unit,
                    'dari_tkt' => $req->dari_tkt[$key] ?? null,
                    'ke_tkt' => $req->ke_tkt[$key] ?? null,
                    'tgl_brkt_tkt' => $req->tgl_brkt_tkt[$key] ?? null,
                    'jam_brkt_tkt' => $req->jam_brkt_tkt[$key] ?? null,
                    'jenis_tkt' => $req->jenis_tkt[$key] ?? null,
                    'type_tkt' => $req->type_tkt[$key] ?? null,
                    'tgl_plg_tkt' => $req->tgl_plg_tkt[$key] ?? null,
                    'jam_plg_tkt' => $req->jam_plg_tkt[$key] ?? null,
                    'ket_tkt' => $req->ket_tkt[$key] ?? null,
                    'jk_tkt' => $req->jk_tkt[$key] ?? null,
                    'np_tkt' => $req->np_tkt[$key] ?? null,
                    'tlp_tkt' => $req->tlp_tkt[$key] ?? null,
                    'approval_status' => $req->status,
                    'jns_dinas_tkt' => $req->jns_dinas_tkt,
                ];

                if (isset($existingTickets[$value])) {
                    // Update existing ticket
                    $existingTicket = $existingTickets[$value];
                    $existingTicket->update($ticketData);
                } else {
                    // Determine no_tkt for new ticket
                    if (is_null($newNoTkt)) {
                        $newNoTkt = $existingTickets->isNotEmpty()
                            ? $existingTickets->first()->no_tkt
                            : generateTicketNumber($ticketType);
                    }

                    // Create a new ticket entry
                    Tiket::create(array_merge($ticketData, [
                        'id' => (string) Str::uuid(),
                        'no_tkt' => $newNoTkt,
                        'noktp_tkt' => $value,
                    ]));
                }

                // Track the processed ticket IDs
                $processedTicketIds[] = $value;
            }
        }

        // Soft delete tickets that are no longer in the request or marked as 'tidak'
        Tiket::where('no_sppd', $req->bisnis_numb)
            ->whereNotIn('noktp_tkt', $processedTicketIds)
            ->delete();

        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();
        if ($bt && $req->status == 'Pending L1') {
            $bt->tiket = 'Ya';
            $bt->save();
        }

        if (count($processedTicketIds) > 0) {
            Alert::success('Success', "Tickets updated successfully");
        } else {
            Alert::warning('Warning', "No tickets were updated.");
        }

        return redirect()->route('ticket');
    }

    public function ticketDelete($key)
    {
        $model = Tiket::findByRouteKey($key);
        $model->delete();
        return redirect()->intended(route('ticket', absolute: false));
    }
    public function ticketExport($id)
    {
        // Fetch all tickets related to the provided ID (assuming 'no_sppd' is the common field)
        $ticket = Tiket::findOrFail($id);
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();

        // Prepare the data to be passed to the PDF view
        $data = [
            'ticket' => $ticket,
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
                    'company_name' => $ticket->employee->company_name ?? 'N/A',
                    'cost_center' => $ticket->cost_center ?? 'N/A'
                ];
            })
        ];

        // Load the view and pass the data
        $pdf = PDF::loadView('hcis.reimbursements.ticket.tiket_pdf', $data);

        // Stream the generated PDF to the browser, opening in a new tab
        return $pdf->stream('Ticket.pdf');
    }

    public function ticketApproval()
    {
        $user = Auth::user();
        $userId = $user->id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record

        $parentLink = 'Reimbursement';
        $link = 'Ticket Approval';

        // Get unique ticket numbers with conditions
        $ticketNumbers = Tiket::where('tkt_only', 'Y')
            ->where('approval_status', '!=', 'Draft')
            ->pluck('no_tkt')->unique();
        // dd($ticketNumbers);

        // Fetch all tickets using the latestTicketIds
        $transactions = Tiket::whereIn('no_tkt', $ticketNumbers)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($transactions);

        // Filter tickets based on manager and approval status
        $tickets = $transactions->filter(function ($ticket) use ($employee) {
            // Get the employee who owns the ticket
            $ticketOwnerEmployee = Employee::where('id', $ticket->user_id)->first();

            if ($ticket->approval_status == 'Pending L1' && $ticketOwnerEmployee->manager_l1_id == $employee->employee_id) {
                return true;
            } elseif ($ticket->approval_status == 'Pending L2' && $ticketOwnerEmployee->manager_l2_id == $employee->employee_id) {
                return true;
            }
            return false;
        });

        // Group tickets by `no_tkt` and get the first entry for each group
        $ticketGroups = $tickets->groupBy('no_tkt')->map(function ($group) {
            return $group->first();
        });

        // dd($tickets);

        // Group tickets by `no_tkt`
        $ticket = $tickets->groupBy('no_tkt');

        // Fetch employee data for ticket owners
        $employeeIds = $tickets->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

        // Fetch manager names for display (optional, depending on your view)
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();
        $managerL1Names = Employee::whereIn('id', $managerL1Ids)->pluck('fullname', 'id');
        $managerL2Names = Employee::whereIn('id', $managerL2Ids)->pluck('fullname', 'id');

        // Count tickets per `no_tkt`
        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });

        return view('hcis.reimbursements.ticket.ticketApproval', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $ticketGroups,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
        ]);
    }

    public function ticketApprovalDetail($key)
    {
        // Define links for navigation
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $ticket = Tiket::findByRouteKey($key);

        // Check if the ticket exists, if not redirect with an error message
        if (!$ticket) {
            return redirect()->route('ticket')->with('error', 'Ticket not found');
        }

        // Fetch all tickets associated with the same no_sppd for reference
        $tickets = Tiket::where('no_tkt', $ticket->no_tkt)->get();
        $userId = Tiket::where('no_tkt', $ticket->no_tkt)->pluck('user_id')->first();
        // dd($userId);

        // Fetch additional data needed for the form
        $employee_data = Employee::where('id', $userId)->first();
        // dd($employee_data);
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();

        $ticketOwnerEmployee = Employee::where('id', $ticket->user_id)->first();
        $no_sppds = BusinessTrip::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', '!=', 'Verified')
                    ->where('status', '!=', 'Draft');
            })
            ->orderBy('no_sppd', 'asc')
            ->get();
        $transactions = $tickets;

        $ticketData = [];
        $ticketCount = $tickets->count();
        foreach ($tickets as $index => $ticket) {
            $ticketData[] = [
                'id' => $ticket->id,
                'noktp_tkt' => $ticket->noktp_tkt,
                'tlp_tkt' => $ticket->tlp_tkt,
                'jk_tkt' => $ticket->jk_tkt,
                'np_tkt' => $ticket->np_tkt,
                'dari_tkt' => $ticket->dari_tkt,
                'ke_tkt' => $ticket->ke_tkt,
                'tgl_brkt_tkt' => $ticket->tgl_brkt_tkt,
                'jam_brkt_tkt' => $ticket->jam_brkt_tkt,
                'jenis_tkt' => $ticket->jenis_tkt,
                'type_tkt' => $ticket->type_tkt,
                'tgl_plg_tkt' => $ticket->tgl_plg_tkt,
                'jam_plg_tkt' => $ticket->jam_plg_tkt,
                'ket_tkt' => $ticket->ket_tkt,
                'more_tkt' => ($index < $ticketCount - 1) ? 'Ya' : 'Tidak'
            ];
        }

        // Return the view with the necessary data
        return view('hcis.reimbursements.ticket.ticketApprovalDetail', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'companies' => $companies,
            'locations' => $locations,
            'employee_data' => $employee_data,
            'perdiem' => $perdiem,
            'no_sppds' => $no_sppds,
            'transactions' => $transactions,
            'ticket' => $ticket,
            'ticketData' => $ticketData,
            'ticketOwnerEmployee' => $ticketOwnerEmployee,
            'ticketCount' => $ticketCount,
        ]);
    }

    public function updateStatusTicket($id, Request $request)
    {
        $user = Auth::user();
        $employeeId = $user->employee_id;

        // Find the ticket by ID
        $ticket = Tiket::findOrFail($id);
        $noTkt = $ticket->no_tkt;

        // Check the provided status_approval input
        $statusApproval = $request->input('status_approval');

        // Handle rejection scenario
        if ($statusApproval == 'Rejected') {

            $rejectInfo = $request->input('reject_info');

            // Get the current approval status before updating it
            $currentApprovalStatus = $ticket->approval_status;

            // Update all tickets with the same no_tkt to 'Rejected'
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Rejected']);

            // Log the rejection into the tkt_approvals table for all tickets with the same no_tkt
            $tickets = Tiket::where('no_tkt', $noTkt)->get();
            foreach ($tickets as $ticket) {
                $rejection = new TiketApproval();
                $rejection->id = (string) Str::uuid();
                $rejection->tkt_id = $ticket->id;
                $rejection->employee_id = $employeeId;

                // Determine the correct layer based on the ticket's approval status BEFORE rejection
                if ($currentApprovalStatus == 'Pending L2') {
                    $rejection->layer = 2; // Layer 2 if ticket was at L2
                } else {
                    $rejection->layer = 1; // Otherwise, it's Layer 1
                }

                $rejection->approval_status = 'Rejected';
                $rejection->approved_at = now();
                $rejection->reject_info = $rejectInfo;
                $rejection->save();
            }

            // Return a rejection message
            $message = 'The request has been successfully Rejected.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            // Redirect to the ticket approval page instead of back to the same page
            return redirect('/ticket/approval')->with('success', $message);
        }

        // If not rejected, proceed with normal approval process
        if ($ticket->approval_status == 'Pending L1') {
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Pending L2']);
        } elseif ($ticket->approval_status == 'Pending L2') {
            Tiket::where('no_tkt', $noTkt)->update(['approval_status' => 'Approved']);
        } else {
            return redirect()->back()->with('error', 'Invalid status update.');
        }

        // Log the approval into the tkt_approvals table for all tickets with the same no_tkt
        $tickets = Tiket::where('no_tkt', $noTkt)->get();
        foreach ($tickets as $ticket) {
            $approval = new TiketApproval();
            $approval->id = (string) Str::uuid();
            $approval->tkt_id = $ticket->id;
            $approval->employee_id = $employeeId;
            $approval->layer = $ticket->approval_status == 'Pending L2' ? 1 : 2;
            $approval->approval_status = $ticket->approval_status;
            $approval->approved_at = now();
            $approval->save();
        }

        // Set success message based on new status
        $message = ($ticket->approval_status == 'Approved')
            ? 'The request has been successfully Approved.'
            : 'The request has been successfully moved to Pending L2.';

        // Return success message
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        // Redirect to the ticket approval page
        return redirect('/ticket/approval')->with('success', $message);
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
