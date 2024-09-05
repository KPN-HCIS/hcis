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
use App\Models\tkt_transaction;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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

    public function hotel()
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';
        $transactions = Hotel::with('employee')->get();

        // foreach ($transactions as $transaction) {
        //     dd($transaction); // This will dump the first transaction and stop execution
        // }

        return view('hcis.reimbursements.hotel.hotel', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
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
        $no_sppds = BusinessTrip::where('user_id', $userId)->where('status', '!=', 'Approved')->orderBy('no_sppd', 'asc')->get();
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
    public function hotelSubmit(Request $req)
    {
        function getRomanMonth_htl($month)
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
        $userId = Auth::id();
        $currentYear = date('Y');
        $currentMonth = date('n');
        $romanMonth = getRomanMonth_htl($currentMonth);

        // Ambil nomor urut terakhir dari tahun berjalan menggunakan Eloquent
        $lastTransaction = htl_transaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('no_htl', 'desc')
            ->first();

        if ($lastTransaction && preg_match('/(\d{3})\/HTLD-HRD\/' . $romanMonth . '\/\d{4}/', $lastTransaction->no_htl, $matches)) {
            $lastNumber = intval($matches[1]);
        } else {
            $lastNumber = 0;
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $newNoHtl = "$newNumber/HTLD-HRD/$romanMonth/$currentYear";

        $model = new htl_transaction;

        $model->id = Str::uuid();
        $model->no_htl = $newNoHtl;
        $model->no_sppd = $req->bisnis_numb;
        $model->user_id = $userId;
        $model->unit = $req->unit;
        $model->nama_htl = $req->nama_htl;
        $model->lokasi_htl = $req->lokasi_htl;
        $model->jmlkmr_htl = $req->jmlkmr_htl;
        $model->bed_htl = $req->bed_htl;
        $model->tgl_masuk_htl = $req->tgl_masuk_htl;
        $model->tgl_keluar_htl = $req->tgl_keluar_htl;
        $model->total_hari = $req->totaldays;
        $model->created_by = $userId;

        $bt = BusinessTrip::where('no_sppd', $req->bisnis_numb)->first();

        if ($bt) {
            // Update the 'hotel' field to 'Ya'
            $bt->hotel = 'Ya';
            $bt->save();
        }

        $model->save();

        Alert::success('Success');
        session()->flash('message', 'Berhasil di Tambahkan');
        return redirect()->intended(route('hotel', absolute: false));
    }
    function hotelEdit($key)
    {
        $userId = Auth::id();
        $parentLink = 'Reimbursement';
        $link = 'Hotel';

        $employee_data = Employee::where('id', $userId)->first();
        $companies = Company::orderBy('contribution_level')->get();
        $locations = Location::orderBy('area')->get();
        $perdiem = ListPerdiem::where('grade', $employee_data->job_level)->first();
        $no_sppds = CATransaction::where('user_id', $userId)->where('approval_sett', '!=', 'Done')->get();
        $transactions = htl_transaction::findByRouteKey($key);

        return view('hcis.reimbursements.hotel.editHotel', [
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
    public function hotelUpdate(Request $req, $key)
    {
        $model = htl_transaction::findByRouteKey($key);

        if ($model) {
            $model->unit = $req->unit;
            $model->nama_htl = $req->nama_htl;
            $model->lokasi_htl = $req->lokasi_htl;
            $model->jmlkmr_htl = $req->jmlkmr_htl;
            $model->bed_htl = $req->bed_htl;
            $model->tgl_masuk_htl = $req->tgl_masuk_htl;
            $model->tgl_keluar_htl = $req->tgl_keluar_htl;
            $model->total_hari = $req->totaldays;
            $model->save();

            Alert::success('Success');
            session()->flash('message', 'Edit Berhasil');
            return redirect()->route('hotel');
        } else {
            return redirect()->back()->withErrors(['message' => 'Transaction not found']);
        }
    }
    function hotelDelete($key)
    {
        $model = htl_transaction::findByRouteKey($key);
        $model->delete();
        return redirect()->intended(route('hotel', absolute: false));
    }
    public function ticket()
    {
        $userId = Auth::user();
        $parentLink = 'Reimbursement';
        $link = 'Ticket';

        $latestTicketIds = Tiket::selectRaw('MAX(id) as id')
            ->where('user_id', $userId->id)
            ->groupBy('no_tkt')
            ->pluck('id');

        $transactions = Tiket::whereIn('id', $latestTicketIds)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->select('id', 'no_tkt', 'dari_tkt', 'ke_tkt', 'approval_status', 'jns_dinas_tkt', 'user_id', 'no_sppd')
            ->get();

        $tickets = Tiket::where('user_id', $userId->id)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get();
        $ticket = Tiket::where('user_id', $userId->id)
            ->with('businessTrip')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('no_tkt');

        // Fetch employee data
        $employeeIds = $tickets->pluck('user_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

        // Fetch manager IDs from the employees data
        $managerL1Ids = $employees->pluck('manager_l1_id')->unique();
        $managerL2Ids = $employees->pluck('manager_l2_id')->unique();
        // dd($managerL1Ids, $managerL2Ids);

        // Fetch manager names
        $managerL1Names = Employee::whereIn('employee_id', $managerL1Ids)->pluck('fullname');
        $managerL2Names = Employee::whereIn('employee_id', $managerL2Ids)->pluck('fullname');
        // dd($managerL1Names, $managerL2Names);

        $ticketCounts = $tickets->groupBy('no_tkt')->mapWithKeys(function ($group, $key) {
            return [$key => ['total' => $group->count()]];
        });
        // dd($tickets, $transaction->no_tkt);

        return view('hcis.reimbursements.ticket.ticket', [
            'link' => $link,
            'parentLink' => $parentLink,
            'userId' => $userId,
            'transactions' => $transactions,
            'ticketCounts' => $ticketCounts,
            'tickets' => $tickets,
            'ticket' => $ticket,
            'managerL1Names' => $managerL1Names,
            'managerL2Names' => $managerL2Names,
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
        $no_sppds = BusinessTrip::where('user_id', $userId)->where('status', '!=', 'Verified')->orderBy('no_sppd', 'asc')->get();
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
            'ket_tkt' => $req->ket_tkt,
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
            ->where('status', '!=', 'Verified')
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
        $pdf = PDF::loadView('hcis.reimbursements.businessTrip.tiket_pdf', $data);

        // Stream the generated PDF to the browser, opening in a new tab
        return $pdf->stream('Ticket.pdf');
    }

    public function ticketApproval()
    {
        $user = Auth::user();
        $userId = $user->id;
        $employee = Employee::where('id', $userId)->first();  // Authenticated user's employee record

        $parentLink = 'Reimbursement';
        $link = 'Ticket';

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

}
